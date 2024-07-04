<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Cart;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CartController extends Controller
{
    use ApiResponses;

    public function index(Request $request, $expressId = Null)
    {
        $carts = Cart::whereNull('customer_id')->where('ip_address', $request->ip());
        // dd($carts);

        if (Auth::guard('api')->check())
            $carts = $carts->orWhere('customer_id', Auth::guard('api')->user()->id);

        $carts = $carts->get();

        // Load related models
        //$carts->load(['inventories']);

        $carts->load([
            'inventories' => function ($query) {
                $query->with([
                    'product' => function ($q) {
                        $q->with([
                            'images' => function ($q) {
                                $q->select('path', 'imageable_id', 'imageable_type');
                            }
                        ]);
                    }
                ]);
            }
        ]);

        return $this->success('Cart Retrived Successfully', $carts);
    }


    public function addToCart(Request $request, $slug)
    {
        $item = Inventory::where('slug', $slug)->first();

        if (!$item)
            return $this->error('Item Not found', []);

        $customer_id = Auth::guard('api')->check() ? Auth::guard('api')->user()->id : Null;

        if ($customer_id) {
            $old_cart = Cart::where('customer_id', $customer_id)
                ->orWhere(function ($q) {
                    $q->whereNull('customer_id')
                        ->where('ip_address', request()->ip());
                })
                ->first();
        } else {
            $old_cart = Cart::whereNull('customer_id')->where('ip_address', $request->ip())->first();
        }

        // Check the available stock limit
        if ($request->quantity > $item->stock_quantity)
            return $this->error('limited stock available', []);

        // Check if the item is alrealy in the cart
        if ($old_cart) {
            $item_in_cart = DB::table('cart_items')->where('cart_id', $old_cart->id)->where('inventory_id', $item->id)->first();

            if ($item_in_cart)
                return $this->error('Item alrealy in your cart', []); // Item alrealy in cart
        }

        $qtt = $request->quantity ?? $item->min_order_quantity;
        // $shipping_rate_id = $old_cart ? $old_cart->shipping_rate_id : $request->shippingRateId;
        $unit_price = $item->currnt_sale_price();

        // Instantiate new cart if old cart not found for the shop and customer
        $cart = $old_cart ?? new Cart;
        $cart->customer_id = $customer_id;
        $cart->ip_address = $request->ip();
        $cart->item_count = $old_cart ? ($old_cart->item_count + 1) : 1;
        $cart->quantity = $old_cart ? ($old_cart->quantity + $qtt) : $qtt;

        if ($request->shipTo)
            $cart->ship_to = $request->shipTo;

        //Reset if the old cart exist, bcoz shipping rate will change after adding new item
        $cart->shipping_zone_id = $old_cart ? Null : $request->shippingZoneId;
        $cart->shipping_rate_id = $old_cart ? null : ($request->shippingRateId == 'Null' ? null : $request->shippingRateId);


        $cart->handling = $old_cart ? $old_cart->handling : 0.00;
        $cart->total = $old_cart ? ($old_cart->total + ($qtt * $unit_price)) : $unit_price;
        $cart->packaging_id = $old_cart ? $old_cart->packaging_id : 0;
        $cart->grand_total = $cart->grand_total();

        // All items need to have shipping_weight to calculate shipping
        // If any one the item missing shipping_weight set null to cart shipping_weight
        if ($item->shipping_weight == Null || ($old_cart && $old_cart->shipping_weight == Null))
            $cart->shipping_weight = Null;
        else
            $cart->shipping_weight = $old_cart ? ($old_cart->shipping_weight + $item->shipping_weight) : $item->shipping_weight;

        $cart->save();

        // Prepare pivot data
        $cart_item_pivot_data = [];
        $cart_item_pivot_data[$item->id] = [
            'inventory_id' => $item->id,
            'item_description' => $item->title . ' - ' . $item->condition,
            'quantity' => $qtt,
            'unit_price' => $unit_price,
        ];

        // Save cart items into pivot
        if (!empty($cart_item_pivot_data))
            $cart->inventories()->syncWithoutDetaching($cart_item_pivot_data);

        return $this->ok('Item added to cart');
    }

    public function remove(Request $request)
    {
        $cart = Cart::findOrFail($request->cart);

        $result = DB::table('cart_items')->where([
            ['cart_id', $request->cart],
            ['inventory_id', $request->item],
        ])->delete();

        if (!$result)
            return response()->json(['message' => 'Not Found'], 404);

        if (!$cart->inventories()->count()) {
            $cart->forceDelete();
        } else {
            $this->crosscheckAndUpdateOldCartInfo($request, $cart);
        }

        return $this->success('Item Removed From Cart Successfully!', $cart);
    }

    public function crosscheckAndUpdateOldCartInfo($request, Cart $cart)
    {
        $total = 0;
        $quantity = 0;
        $discount = Null;
        $shipping_weight = 0;
        $handling = 0.00;
        // Start with old values
        $shipping = $cart->shipping;
        $packaging = $cart->packaging;
        // $discount = $cart->discount;

        // Qtt and Total
        foreach ($cart->inventories as $item) {
            $temp_qtt = $request->quantity ? $request->quantity[$item->id] : $item->pivot->quantity;
            $unit_price = $item->currnt_sale_price();
            $temp_total = $unit_price * $temp_qtt;

            $shipping_weight = $item->shipping_weight * $temp_qtt;
            $quantity += $temp_qtt;
            $total += $temp_total;

            // Update the cart item pivot table
            $cart->inventories()->updateExistingPivot($item->id, ['quantity' => $temp_qtt, 'unit_price' => $unit_price]);
        }

        // Taxes
        if ($request->zone_id) {
            $taxrate = $request->tax_id ? getTaxRate($request->tax_id) : Null;
            $taxes = ($total * $taxrate) / 100;

            $cart->shipping_zone_id = $request->zone_id;
            $cart->taxrate = $taxrate;
        } else {
            $taxes = ($total * $cart->taxrate) / 100;
        }

        // Shipping
        if ($request->shipping_rate_id) {
            $shippingRate = ShippingRate::select('rate')->where([
                ['id', '=', $request->shipping_rate_id],
                ['shipping_zone_id', '=', $request->zone_id]
            ])->first();

            // abort_unless( $shippingRate, 403, trans('theme.notify.seller_doesnt_ship') );

            if ($shippingRate) {
                $shipping = $shippingRate->rate;
                $cart->shipping_rate_id = $request->shipping_rate_id;
            } else {
                $cart->shipping_rate_id = Null;
            }
        }

        // Discount
        if ($request->discount_id) {
            $coupon = Coupon::where([
                ['id', '=', $request->discount_id],
                ['shop_id', '=', $cart->shop_id],
                ['code', '=', $request->coupon]
            ])->active()->first();

            if ($coupon && $coupon->isValidForTheCart($total, $request->zone_id)) {
                $discount = ('percent' == $coupon->type) ? ($coupon->value * ($total / 100)) : $coupon->value;
                $cart->coupon_id = $request->discount_id;
            }
        } else if ($cart->coupon_id) {
            // Validate the old coupon
            if ($cart->coupon->isValidForTheCart($total, $request->zone_id)) {
                $discount = ('percent' == $cart->coupon->type) ? ($cart->coupon->value * ($total / 100)) : $cart->coupon->value;
            }
            // Validation failed
            else {
                $cart->coupon_id = Null;
            }
        }

        // Packaging
        if ($request->packaging_id && $request->packaging_id != Packaging::FREE_PACKAGING_ID) {
            $packagingCost = Packaging::select('cost')->where([
                ['id', '=', $request->packaging_id],
                ['shop_id', '=', $cart->shop_id]
            ])->active()->first();

            $packaging = $packagingCost->cost;
            $cart->packaging_id = $request->packaging_id;
        }

        if ($request->payment_method) {
            $cart->payment_method_id = $request->payment_method;
        }

        // Set customer_id if not set yet
        if (!$cart->customer_id && Auth::guard('customer')->check())
            $cart->customer_id = Auth::guard('customer')->user()->id;
        else if (Auth::guard('api')->check())
            $cart->customer_id = Auth::guard('api')->user()->id;

        $cart->ship_to = $request->ship_to ?? $request->country_id ?? $cart->ship_to;
        $cart->shipping_weight = $shipping_weight;
        $cart->quantity = $quantity;
        $cart->total = $total;
        $cart->taxes = $taxes;
        $cart->shipping = $shipping;
        $cart->packaging = $packaging;
        $cart->discount = $discount;
        $cart->handling = $handling;
        $cart->grand_total = ($total + $taxes + $shipping + $packaging + $handling) - $discount;
        $cart->save();

        return $cart;
    }

    public function update(Request $request, Cart $cart)
    {
        if ($request->item && $request->quantity) {
            if (is_numeric($request->item))
                $item = Inventory::findOrFail($request->item);
            else
                $item = Inventory::where('slug', $request->item)->first();

            // Check the available stock limit
            if ($request->quantity > $item->stock_quantity)
                return response()->json(['message' => trans('api.item_max_stock')], 409);

            $pivot = DB::table('cart_items')->where('cart_id', $cart->id)->where('inventory_id', $item->id)->first();

            if (!$pivot)
                return response()->json(['message' => trans('api.404')], 404);

            $quantity = $request->quantity;
            $old_quantity = $pivot->quantity;

            $cart->quantity = $quantity < $item->min_order_quantity ? $item->min_order_quantity : $quantity;
            $cart->item_count = ($cart->item_count - $old_quantity) + $quantity;

            if ($item->shipping_weight)
                $cart->shipping_weight = ($cart->shipping_weight - ($item->shipping_weight * $old_quantity)) + ($item->shipping_weight * $quantity);

            $unit_price = $item->currnt_sale_price();

            $cart->total = ($cart->total - ($pivot->unit_price * $old_quantity)) + ($quantity * $unit_price);
            $cart->grand_total = $cart->grand_total();

            // Updating pivot data
            $cart->inventories()->updateExistingPivot($item->id, [
                'quantity' => $quantity,
                'unit_price' => $unit_price,
            ]);
        }

        if ($request->shipTo)
            $cart->ship_to = $request->shipTo;

        if ($request->shipping_zone_id)
            $cart->shipping_zone_id = $request->shipping_zone_id;

        if ($request->shipping_rate_id)
            $cart->shipping_rate_id = $request->shipping_rate_id;

        if ($request->packaging_id)
            $cart->packaging_id = $request->packaging_id;

        // Update some filed only if the cart is older than 24hrs
        if ($cart->updated_at < Carbon::now()->subHour(24)) {
            $cart->handling = getShopConfig($item->shop_id, 'order_handling_cost');
        }

        $cart->save();

        return $this->success('Cart Updated Successfully', $cart);
    }

    public function totalitemsincart(Request $request)
    {
        $cartQuery = Cart::whereNull('customer_id')->where('ip_address', $request->ip());

        if (Auth::guard('api')->check()) {
            $cartQuery = $cartQuery->orWhere('customer_id', Auth::guard('api')->user()->id);
        }

        $cart = $cartQuery->first();

        if ($cart) {
            return response()->json(['total_items' => $cart->inventories_count, 'cart_id' => $cart->id]);
        } else {
            return response()->json(['total_items' => 0, 'cart_id' => null]);
        }
    }
}
