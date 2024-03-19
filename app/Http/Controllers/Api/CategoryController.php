<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CategoryRequest;
use App\Http\Resources\Category as ResourcesCategory;
use App\Models\Category as Model;
use App\Services\ApiService;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Retrieve All Categories
    public function index(Request $request)
    {
        try {
            $category_query = Model::query();
            $result = ApiService::format_model_paginate($category_query, $request['limit'], $request['offset']);
            $result['items'] = ResourcesCategory::collection($result['items']);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Retrieve Specific Category
    public function show($id)
    {
        try {
            $category = Model::find($id);
            if ($category) {
                return response()->json($category, 200);
            } else {
                return response()->json(['error' => 'This category not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Create New Category
    public function store(CategoryRequest $request)
    {
        try {
            $category = Model::create($request->validated());
            return response()->json($category, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Update Specific Category
    public function update(CategoryRequest $request, $id)
    {
        try {
            $Item = Model::find($id);
            if ($Item) {
                $Item = Model::create($request->validated());
                return response()->json($Item, 200);
            } else {
                return response()->json(['error' => 'This category not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Delete Specific Category
    public function destroy($id)
    {
        try {
            $Item = Model::find($id);
            if ($Item) {
                // Soft delete
                $Item->delete();
                return response()->json(204);
            } else {
                return response()->json(['error' => 'This category not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }
}
