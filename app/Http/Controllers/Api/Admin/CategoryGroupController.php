<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class CategoryGroupController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $categoryGroups = CategoryGroup::all();
        return $this->success('Category Groups Retrived Succesfully!', $categoryGroups);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:category_groups,name',
            'slug' => 'required|string|max:200|unique:category_groups,slug',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer'
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug field must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $categoryGroup = CategoryGroup::create($request->all());

        return $this->success('Category Group Created Successfully!', $categoryGroup);
    }

    public function show(string $id)
    {
        $categoryGroup = CategoryGroup::with('categorySubGroup')->find($id);

        if (!$categoryGroup) {
            return $this->error('Category Group Not Found.', ['error' => 'Category Group Not Found']);
        }
        return $this->success('Category Group Retrived Succesfully!', $categoryGroup);
    }

    public function update(Request $request, string $id)
    {
        $categoryGroup = CategoryGroup::find($id);

        if (!$categoryGroup) {
            return $this->error('Category Group Not Found.', ['error' => 'Category Group Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200|unique:category_groups,name,' . $id,
            'slug' => 'required|string|max:200|unique:category_groups,slug,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer'
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $categoryGroup->update($validatedData);

        return $this->success('Category Group Updated Successfully!', $categoryGroup);
    }

    public function destroy($id)
    {
        $categoryGroup = CategoryGroup::find($id);

        if (!$categoryGroup) {
            return $this->error('Category Group Not Found.', ['error' => 'Category Group Not Found']);
        }

        $categoryGroup->delete();

        return $this->ok('Category Group Deleted Successfully!');
    }

    public function restore($id)
    {
        $categoryGroup = CategoryGroup::onlyTrashed()->find($id);

        if (!$categoryGroup) {
            return $this->error('Category Group Not Found.', ['error' => 'Category Group Not Found']);
        }

        $categoryGroup->restore();

        return $this->success('Category Group Restored Successfully!', $categoryGroup);
    }

}
