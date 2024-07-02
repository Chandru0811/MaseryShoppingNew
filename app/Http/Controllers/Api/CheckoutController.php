<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Models\Cart;
use App\Models\Order;
use DB;
use Auth;
use App\Models\User;

class CheckoutController extends Controller
{
    use ApiResponses;

    public function checkout(Request $request, Cart $cart)
    {
        $cart->shipping = $request->shipping;
        $cart->packaging = $request->packaging;
        $cart->payment_method_id = $request->payment_method_id;

        $cart->save();

        $cart = $this->crosscheckAndUpdateOldCartInfo($request, $cart);

        $order = $this->saveOrderFromCart($request, $cart);

        $cart->forceDelete();   // Delete the cart

        return $this->ok('Order Created Successfully');
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
        if($request->zone_id) {
            $taxrate = $request->tax_id ? getTaxRate($request->tax_id) : Null;
            $taxes = ($total * $taxrate)/100;

            $cart->shipping_zone_id = $request->zone_id;
            $cart->taxrate = $taxrate;
        }
        else{
            $taxes = ($total * $cart->taxrate)/100;
        }

        // Shipping
        if($request->shipping_rate_id) {
            $shippingRate = ShippingRate::select('rate')->where([
                ['id', '=', $request->shipping_rate_id],
                ['shipping_zone_id', '=', $request->zone_id]
            ])->first();

            // abort_unless( $shippingRate, 403, trans('theme.notify.seller_doesnt_ship') );

            if($shippingRate){
                $shipping = $shippingRate->rate;
                $cart->shipping_rate_id = $request->shipping_rate_id;
            }
            else{
                $cart->shipping_rate_id = Null;
            }
        }

        // Discount
        if($request->discount_id) {
            $coupon = Coupon::where([
                ['id', '=', $request->discount_id],
                ['shop_id', '=', $cart->shop_id],
                ['code', '=', $request->coupon]
            ])->active()->first();

            if($coupon && $coupon->isValidForTheCart($total, $request->zone_id)){
                $discount = ('percent' == $coupon->type) ? ($coupon->value * ($total/100)) : $coupon->value;
                $cart->coupon_id = $request->discount_id;
            }
        }
        else if($cart->coupon_id){
            // Validate the old coupon
            if($cart->coupon->isValidForTheCart($total, $request->zone_id)){
                $discount = ('percent' == $cart->coupon->type) ? ($cart->coupon->value * ($total/100)) : $cart->coupon->value;
            }
            // Validation failed
            else{
                $cart->coupon_id = Null;
            }
        }

        // Packaging
        if($request->packaging_id && $request->packaging_id != Packaging::FREE_PACKAGING_ID) {
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

    public function saveOrderFromCart($request, $cart)
    {
        // Set shipping_rate_id and handling cost to NULL if its free shipping
        if($cart->is_free_shipping()) {
            $cart->shipping_rate_id = Null;
            $cart->handling = Null;
        }

        // Save the order
        $order = new Order;
        $order->fill(
            array_merge($cart->toArray(), [
                'grand_total' => $cart->grand_total(),
                'order_number' => $this->get_formated_order_number(),
                'carrier_id' => NULL,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'email' => $request->email,
                'buyer_note' => $request->buyer_note
            ])
        );
        $order->save();

        // Add order item into pivot table
        $cart_items = $cart->inventories->pluck('pivot');
        $order_items = [];
        foreach ($cart_items as $item) {
            $order_items[] = [
                'order_id'          => $order->id,
                'inventory_id'      => $item->inventory_id,
                'item_description'  => $item->item_description,
                'quantity'          => $item->quantity,
                'unit_price'        => $item->unit_price,
                'created_at'        => $item->created_at,
                'updated_at'        => $item->updated_at,
            ];
        }
        \DB::table('order_items')->insert($order_items);

         // Sync up the inventory. Decrease the stock of the order items from the listing
        foreach ($order->inventories as $item) {
            $item->decrement('stock_quantity', $item->pivot->quantity);
        }


        // Reduce the coupone in use
        if ($order->coupon_id)
            Coupon::find($order->coupon_id)->decrement('quantity');
            // \DB::table('coupons')->where('id', $order->coupon_id)->decrement('quantity');

        return $order;
    }
    
    public function get_formated_order_number()
    {
        $order_id = str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

        if(auth()->guard('api')->check()) {
            $user_id = auth()->user()->id;
        }

        $order_id_with_prefix = $user_id . '-' . $order_id;
        return $order_id_with_prefix;
        
    }
    
    public function getcheckoutdetails(Request $request)
    {
         $ordersummary = Cart::whereNull('customer_id')->where('ip_address', $request->ip());

        if(Auth::guard('api')->check())
            $ordersummary = $ordersummary->orWhere('customer_id', Auth::guard('api')->user()->id);

        $ordersummary = $ordersummary->get();

        // Load related models
        //$carts->load(['inventories']);
        
        $ordersummary->load([
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
        
         return $this->success('Order Summary Retrived Successfully',$ordersummary);
    }
}
