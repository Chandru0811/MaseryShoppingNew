<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Inventory\InventoryRepository;
use App\Models\Inventory;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class InventoryController extends Controller
{
    use ApiResponses;

    private $model;

    private $inventory;
    /**
     * construct
     */
    public function __construct(InventoryRepository $inventory)
    {

        $this->model = Inventory::class;
        $this->inventory = $inventory;
    }

    public function index()
    {
        $inventories = $this->inventory->all();

        return $this->success('Prooducts Retrived Successfulluy!', $inventories);
    }

    public function add(Request $request, $id)
    {
        $inInventory = $this->inventory->checkInveoryExist($id);

        if ($inInventory)
            return response()->json(['warning' => 'inventory already exist']);

        $product = $this->inventory->findProduct($id);

        return $this->success('Create Inventory Using Product', $product);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'sku' => 'required',
            'sale_price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric',
            'available_from' => 'nullable|date',
            'slug' => 'required|unique:inventories,slug',
            'image' => 'mimes:jpeg,png',
        ], [
            'title.required' => 'The title field is required.',
            'sku.required' => 'The sku field is required.',
            'order.required' => 'The order field is required.',
            'image.mimes' => 'The image must be a file of type: jpeg, png',
        ]);
        if ($validator->fails()) {
            return $this->error('Validation Error.', ['errors' => $validator->errors()]);
        }

        $inventory = $this->inventory->store($request);

        if ($request->hasFile('images')) {
            $images = $request->images;
            if ($images != null) {
                $publicPath = "assets/inventories/images/" . $inventory->slug;

                if (!file_exists($publicPath)) {
                    File::makeDirectory($publicPath, $mode = 0777, true, true);
                }
                $i = 1;
                foreach ($images as $image) {
                    $imageName = $image->getClientOriginalName();
                    $imageSize = $image->getSize();  // Get the size before moving the file

                    $image->move($publicPath, $imageName);

                    $inventory->images()->create([
                        'path' => $publicPath . "/" . $imageName,
                        'name' => $imageName,
                        'extension' => $image->getClientOriginalExtension(),
                        'size' => $imageSize,
                        'order' => $i++,
                        'imageable_id' => $inventory->id,
                        'imageable_type' => get_class($inventory),
                    ]);
                }
            }
        }

        return $this->success('Inventory Created Succesfully!', $inventory);
    }

    public function update(Request $request, $id)
    {
        $inventory = $this->inventory->update($request, $id);
        return $this->success('Inventory Updated Succesfully!', $inventory);
    }

    public function destroy($id)
    {
        $inventory = $this->inventory->find($id);
        $inventory->delete();
        return $this->success('Inventory Deleted Successfully!', $inventory);
    }

    public function show($id)
    {
        $inventory = $this->inventory->find($id);
        return $this->success('Inventory Deleted Successfully!', $inventory);
    }
}
