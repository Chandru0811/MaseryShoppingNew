<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategorySubGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class CategorySubGroupController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $categorySubGroups = CategorySubGroup::all();
        return $this->success('Category Sub Groups Retrived Succesfully!', $categorySubGroups);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_group_id' => 'required|exists:category_groups,id',
            'name' => 'required|string|max:200|unique:category_sub_groups,name',
            'slug' => 'required|string|max:200|unique:category_sub_groups,slug',
        ], [
            'category_group_id.required' => 'The category group id field is required.',
            'category_group_id.exists' => 'The selected category group id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug field must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $categorySubGroup = CategorySubGroup::create($request->all());

        return $this->success('Category Sub Group Created Successfully!', $categorySubGroup);
    }

    public function show(string $id)
    {
        $categorySubGroup = CategorySubGroup::with('categorySubGroupOne')->find($id);

        if (!$categorySubGroup) {
            return $this->error('Category Sub Group Not Found.', ['error' => 'Category Sub Group Not Found']);
        }
        return $this->success('Category Sub Group Retrived Succesfully!', $categorySubGroup);
    }

    public function update(Request $request, string $id)
    {
        $categorySubGroup = CategorySubGroup::find($id);

        if (!$categorySubGroup) {
            return $this->error('Category Sub Group Not Found.', ['error' => 'Category Sub Group Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'category_group_id' => 'required|exists:category_groups,id',
            'name' => 'required|string|max:200|unique:category_sub_groups,name,' . $id,
            'slug' => 'required|string|max:200|unique:category_sub_groups,slug,' . $id,
            'description' => 'nullable|string',
        ], [
            'category_group_id.required' => 'The category group id field is required.',
            'category_group_id.exists' => 'The selected category group id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $categorySubGroup->update($validatedData);

        return $this->success('Category Sub Group Updated Successfully!', $categorySubGroup);
    }

    public function destroy($id)
    {
        $categorySubGroup = CategorySubGroup::find($id);

        if (!$categorySubGroup) {
            return $this->error('Category Sub Group Not Found.', ['error' => 'Category Sub Group Not Found']);
        }

        $categorySubGroup->delete();

        return $this->ok('Category Sub Group Deleted Successfully!');
    }

    public function restore($id)
    {
        $categorySubGroup = CategorySubGroup::onlyTrashed()->find($id);

        if (!$categorySubGroup) {
            return $this->error('Category Sub Group Not Found.', ['error' => 'Category Sub Group Not Found']);
        }

        $categorySubGroup->restore();

        return $this->success('Category Sub Group Restored Successfully!', $categorySubGroup);
    }

}
