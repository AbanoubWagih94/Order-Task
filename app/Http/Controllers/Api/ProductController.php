<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product as Model;
use App\Services\ApiService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // Retrieve All Products
    public function index(Request $request)
    {
        try {
            $Product_query = Model::query();
            $result = ApiService::format_model_paginate($Product_query, $request['limit'], $request['offset']);
            $result['items'] = ProductResource::collection($result['items']);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Retrieve Specific Product
    public function show($id)
    {
        try {
            $Product = Model::find($id);
            if ($Product) {
                return response()->json($Product, 200);
            } else {
                return response()->json(['error' => 'This product not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Create New Product
    public function store(ProductRequest $request)
    {
        try {
            // Assign validated data to data array
            $data = $request->validated();
            // upload image and assgin its path to data array
            $data['image'] = $this->uploadImage($request, null, $data['title']);
            // get user id
            $user = $request->user();
            $data['user_id'] = $user->id;
            // create product
            $product = Model::create($data);
            // format product
            $product = new ProductResource($product);
            return response()->json($product, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update Specific Product
    public function update(ProductRequest $request, $id)
    {
        try {
            $data = $request->validated();
            // get user id
            $user = $request->user();
            // find product with provided id
            $Item = Model::where('id', $id)->first();

            if ($Item) {
                if ($Item->user_id != $user->id) {
                    return response()->json(['error' => 'You don\'t has access for this product'], 401);
                }

                // upload image if provided and replace new path with old one if no image is provided keep old path
                $title = isset($data['title']) ? $data['title'] : $Item->title;
                $data['image'] = $this->uploadImage($request, $Item, $title);
                // Update Product with new data
                $Item->update($data);
                // format product
                $Item = new ProductResource($Item);

                return response()->json($Item, 200);
            } else {
                return response()->json(['error' => 'This product not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Delete Specific Product
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $Item = Model::find($id);

            if ($Item) {
                if ($Item->user_id != $user->id) {
                    return response()->json(['error' => 'You don\'t has access for this product'], 401);
                }
                // remove image from storage before delete product
                $image = $Item->image;
                if (file_exists($image)) {
                    unlink($image);
                }
                // Soft delete
                $Item->delete();
                return response()->json(204);
            } else {
                return response()->json(['error' => 'This product not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    private function uploadImage($request, $modelClass, $title)
    {
        $path = 'storage/products/';
        if (isset($modelClass)) {
            $image = $modelClass->image;
            if ($request->has('image')) {
                if (file_exists(public_path($image))) {
                    unlink(public_path($image));
                }
                $image = $path . ImageService::uploadImage($request->image, $title, $path);
            }
        } else {
            $image = $path . ImageService::uploadImage($request->image,  $title, $path);
        }
        return $image;
    }
}
