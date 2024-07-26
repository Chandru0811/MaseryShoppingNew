<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Inventory;
use Auth;
use App\Traits\ApiResponses;

class WishlistController extends Controller
{
    use ApiResponses;
    
    public function index(Request $request)
    {
        $wishlists = Wishlist::mine()->whereHas('inventory', function($q) {
            $q->available();
        })->with([
            'inventory',
            'product.image:path,imageable_id,imageable_type'
        ])->paginate(10);
        
        return response()->json(['wishlist' =>$wishlists]);
    }

    public function add(Request $request, $slug)
    {
        $item = Inventory::where('slug', $slug)->firstOrFail();

        $customer_id = Auth::guard('api')->check() ? Auth::guard('api')->user()->id : Null;
        
        if($customer_id){
            $item_in_wishlist = Wishlist::where('inventory_id', $item->id)->where('customer_id', $customer_id)->first();
        }else{
            $item_in_wishlist = Wishlist::where('inventory_id', $item->id)->whereNull('customer_id')->where('ip_address', $request->ip())->first();
        }
        
        if($item_in_wishlist)
            return response()->json(['message' => 'Item Already in the Cart'], 409); // Item alrealy in cart

        $wishlist = new Wishlist;
        $wishlist->updateOrCreate([
            'inventory_id'   =>  $item->id,
            'product_id'   =>  $item->product_id,
            'customer_id' => $customer_id,
            'ip_address' => $request->ip()
        ]);
        
        return $this->ok('Item Added SuccessFully');
    }

    public function remove($id)
    {
         $item = Wishlist::find($id);

        if ($item) {
            // Delete the item
            $item->delete();

            // Return a success response
            return response()->json(['success' => 'Item removed from wishlist.']);
        } else {
            // Return an error response if item not found
            return response()->json(['error' => 'Item not found.'], 404);
        }
    }
    
    public function totalitems(Request $request)
    {
        $wishlist = Wishlist::whereNull('customer_id')->where('ip_address', $request->ip());

        if (Auth::guard('api')->check()) {
            $wishlist = $wishlist->orWhere('customer_id', Auth::guard('api')->user()->id);
        }


        $wishlistCount = $wishlist->count();
        
        if ($wishlistCount) {
            return response()->json(['total_items' => $wishlistCount]);
        } else {
            return response()->json(['total_items' => 0]);
        }
    }
}
