<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;


class UserOrderController extends Controller
{
    use ApiResponses;

    public function orderByCustomerId($customerId)
    {
        $orders = Order::where('customer_id', $customerId)->orderBy('created_at', 'desc')
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
}
