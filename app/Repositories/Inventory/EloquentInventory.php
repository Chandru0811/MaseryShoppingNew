<?php

namespace App\Repositories\Inventory;

use Auth;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Repositories\BaseRepository;
use App\Repositories\EloquentRepository;

class EloquentInventory extends EloquentRepository implements BaseRepository, InventoryRepository
{
	protected $model;

	public function __construct(Inventory $inventory)
	{
		$this->model = $inventory;
	}
	
	public function checkInveoryExist($productId)
    {
        return $this->model->mine()->where('product_id', $productId)->first();
    }
    
    public function findProduct($id)
    {
        return Product::findOrFail($id);
    }
    
    public function updateQtt(Request $request, $id)
    {
        $inventory = parent::find($id);

        $inventory->stock_quantity = $request->input('stock_quantity');

        return $inventory->save();
    }
    
    public function store(Request $request)
    {
        $inventory = parent::store($request);
        return $inventory;
    }

    public function update(Request $request, $id)
    {
        $inventory = parent::update($request, $id);
        return $inventory;
    }
}