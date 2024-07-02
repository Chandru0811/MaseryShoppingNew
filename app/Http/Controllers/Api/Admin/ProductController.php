<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('images')->get();
        return $this->success('Products Retrieved Successfully!', $products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name',
            'slug' => 'required|unique:products,slug',
            'min_price' => 'required|numeric',
            'max_price' => 'required|numeric',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
            'requires_shipping' => 'required|boolean',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
            'min_price.required' => 'The list price field is required.',
            'max_price.required' => 'The sale price field is required.',
            'min_price.numeric' => 'The list price must be a number.',
            'max_price.numeric' => 'The sale price must be a number.',
            'brand_id.required' => 'The brand id field is required.',
            'brand_id.integer' => 'The brand id must be an integer.',
            'category_id.required' => 'The category id field is required.',
            'category_id.integer' => 'The category id must be an integer.',
            'requires_shipping.required' => 'The requires shipping field is required.',
            'requires_shipping.boolean' => 'The requires shipping field must be true or false.',
            'image.image' => 'The image field must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', ['errors' => $validator->errors()]);
        }

        $input = $request->all();
        $input['requires_shipping'] = $request->requires_shipping ? 1 : 0;

        $product = Product::create($input);

        $images = $request->images;
        if ($images != null) {
            $publicPath = "assets/products/images/" . $product->slug;

            if (!file_exists($publicPath)) {
                File::makeDirectory($publicPath, $mode = 0777, true, true);
            }
            $i = 1;
            foreach ($images as $image) {
                $imageName = $image->getClientOriginalName();
                $imageSize = $image->getSize();  // Get the size before moving the file

                $image->move($publicPath, $imageName);

                $product->images()->create([
                    'path' => $publicPath . "/" . $imageName,
                    'name' => $imageName,
                    'extension' => $image->getClientOriginalExtension(),
                    'size' => $imageSize,
                    'order' => $i++,
                    'imageable_id' => $product->id,
                    'imageable_type' => get_class($product),
                ]);
            }
        }

        return $this->success('Product Created Successfully!', $product);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $products = Product::with('images')->find($id);
        if (!$products) {
            return $this->error('Product Not Found.', ['error' => 'Product Not Found']);
        }
        return $this->success('Product Retrieved Successfully!', $products);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->error('Product Not Found.', ['error' => 'Product Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name,' . $product->id,
            'slug' => 'required|unique:products,slug,' . $product->id,
            'min_price' => 'required|numeric',
            'max_price' => 'required|numeric',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
            'requires_shipping' => 'sometimes|boolean',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
            'min_price.required' => 'The list price field is required.',
            'max_price.required' => 'The sale price field is required.',
            'min_price.numeric' => 'The list price must be a number.',
            'max_price.numeric' => 'The sale price must be a number.',
            'brand_id.required' => 'The brand id field is required.',
            'brand_id.integer' => 'The brand id must be an integer.',
            'category_id.required' => 'The category id field is required.',
            'category_id.integer' => 'The category id must be an integer.',
            'requires_shipping.boolean' => 'The requires shipping field must be true or false.',
            'image.image' => 'The image field must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', ['errors' => $validator->errors()]);
        }

        $input = $request->all();
        if ($request->has('requires_shipping')) {
            $input['requires_shipping'] = $request->requires_shipping ? 1 : 0;
        }

        $product->update($input);

        if ($request->has('images')) {
            $images = $request->images;
            $publicPath = "assets/products/images/" . $product->slug;

            if (!file_exists($publicPath)) {
                File::makeDirectory($publicPath, $mode = 0777, true, true);
            }

            // Delete existing images
            $product->images()->delete();

            // Re-upload new images
            $i = 1;
            foreach ($images as $image) {
                $imageName = $image->getClientOriginalName();
                $imageSize = $image->getSize();

                $image->move($publicPath, $imageName);

                $product->images()->create([
                    'path' => $publicPath . "/" . $imageName,
                    'name' => $imageName,
                    'extension' => $image->getClientOriginalExtension(),
                    'size' => $imageSize,
                    'order' => $i++,
                    'imageable_id' => $product->id,
                    'imageable_type' => get_class($product),
                ]);
            }
        }

        return $this->success('Product Updated Successfully!', $product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $products = Product::with('images')->find($id);
        if (!$products) {
            return $this->error('Product Not Found.', ['error' => 'Product Not Found']);
        }

        foreach ($products->images as $image) {
            $imagePath = public_path($image->path);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $image->delete();
        }

        $productFolderPath = public_path("assets/products/images/" . $products->name);
        if (File::isDirectory($productFolderPath) && count(File::files($productFolderPath)) === 0) {
            File::deleteDirectory($productFolderPath);
        }

        $products->delete();
        return $this->success('Product Deleted Successfully!', $products);
    }
}
