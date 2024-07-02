<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class BrandController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::all();
        return $this->success('Brands Retrived Succesfully!',$brands);
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
            'name' => 'required|unique:brands,name',
            'slug' => 'required|unique:brands,name',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', ['errors'=>$validator->errors()]);
        }

        $brand = Brand::create($request->all());
        return $this->success('Brand Created Succesfully!',$brand);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return $this->error('Brand Not Found.', ['error'=>'Brand Not Found']);
        }
        return $this->success('Brand Retrived Succesfully!',$brand);
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
        $brand = Brand::find($id);

        if (!$brand) {
            return $this->error('Brand Not Found.', ['error'=>'Brand Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:brands,name,' . $id,
            'slug' => 'required|unique:brands,slug,' . $id,
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', ['errors'=>$validator->errors()]);
        }

        $brand->update($request->all());
        return $this->success('Brand Updated Succesfully!',$brand);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return $this->error('Brand Not Found.', ['error'=>'Brand Not Found']);
        }

        $brand->delete();

        return $this->success('Brand Deleted Succesfully!',$brand);
    }
}
