<?php

namespace App\Repositories\Inventory;

use Illuminate\Http\Request;

interface InventoryRepository
{
    public function checkInveoryExist($productId);

    public function findProduct($id);

    public function updateQtt(Request $request, $id);
}