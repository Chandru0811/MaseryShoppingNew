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

    public function index()
    {
        $categories = Category::with('allChildren')->where('parent_id', null)->withCount('products')->get();
        return $this->success('Categories Retrived Successfully', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug',
            'children' => 'nullable|array',
            'children.*.name' => 'required_with:children|string|max:255',
            'children.*.slug' => 'required_with:children|string|unique:categories,slug',
            'children.*.children' => 'nullable|array', // Validate nested children
            'children.*.children.*.name' => 'required_with:children.*.children|string|max:255',
            'children.*.children.*.slug' => 'required_with:children.*.children|string|unique:categories,slug',
            'children.*.children.*.children' => 'nullable|array',
            'children.*.children.*.children.*.name' => 'required_with:children.*.children.*.children|string|max:255',
            'children.*.children.*.children.*.slug' => 'required_with:children.*.children.*.children|string|unique:categories,slug',
            'children.*.children.*.children.*.children' => 'nullable|array',
            'children.*.children.*.children.*.children.*.name' => 'required_with:children.*.children.*.children|string|max:255',
            'children.*.children.*.children.*.children.*.slug' => 'required_with:children.*.children.*.children|string|unique:categories,slug',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the validated data
        $validatedData = $validator->validated();

        // Save the category and its children
        $this->saveCategoryWithChildren($validatedData, null);

        return response()->json(['message' => 'Categories stored successfully']);
    }

    private function saveCategoryWithChildren(array $categoryData, $parentId = null)
    {

        // Ensure both 'name' and 'slug' are present before creating the category
        $category = Category::create([
            'name' => $categoryData['name'],
            'slug' => $categoryData['slug'],  // Ensure 'slug' is passed here
            'parent_id' => $parentId,
        ]);

        // dd($category);

        // If the category has children, save them recursively
        if (isset($categoryData['children']) && is_array($categoryData['children'])) {
            foreach ($categoryData['children'] as $childCategoryData) {
                $this->saveCategoryWithChildren($childCategoryData, $category->id);
            }
        }
    }




    public function show(string $id)
    {
        $category = Category::with('allChildren')->find($id);
        if (!$category) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }
        return $this->success('Category Retrieved Successfully!', $category);
    }




    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->json()->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
            'parent_id' => 'nullable|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->update($validator->validated());

        return $this->success('Category Updated Successfully', $category);
    }


    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }

        $category->delete();

        return $this->success('Category Deleted Successfully!', $category);
    }
}
