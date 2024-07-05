<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class CategoryController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all();
        return $this->success('Categories Retrived Succesfully!', $category);
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
            'name' => 'required|unique:categories,name',
            'slug' => 'required|unique:categories,name',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', ['errors' => $validator->errors()]);
        }

        $categories = Category::create($request->all());
        return $this->success('Category Created Succesfully!', $categories);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categories = Category::find($id);
        if (!$categories) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }
        return $this->success('Category Retrived Succesfully!', $categories);
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
        $categories = Category::find($id);
        if (!$categories) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,' . $id,
            'slug' => 'required|unique:categories,slug,' . $id,
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);
        if ($validator->fails()) {
            return $this->error('Category Error.', ['errors' => $validator->errors()]);
        }

        $categories->update($request->all());
        return $this->success('Category Updated Succesfully!', $categories);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categories = Category::find($id);
        if (!$categories) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }
        $categories->delete();
        return $this->success('Category Deleted Succesfully!', $categories);
    }
}
