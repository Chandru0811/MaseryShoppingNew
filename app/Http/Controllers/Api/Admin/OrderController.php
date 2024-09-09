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
        $orders = Order::orderBy('created_at', 'desc')
            ->get();
        $orders->load([
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

        return $this->success('Order Retrived Successfully', $orders);
    }

    public function show($id)
    {
        $order = Order::where('id',$id)->first();

        $order->load([
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

        return $this->success('Order Retrived Successfully',$order);
    }
}
