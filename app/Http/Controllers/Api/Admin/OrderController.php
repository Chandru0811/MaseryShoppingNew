<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Models\Order;

class OrderController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $orders = Order::all();

        return $this->success('Order Retrived Successfully',$orders);
    }

    public function show($id)
    {
        $order = Order::with('inventories.image:path,imageable_id,imageable_type')->where('id',$id)->first();
        
        return $this->success('Order Retrived Successfully',$order);
    }
}