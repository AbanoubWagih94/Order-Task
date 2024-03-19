<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article as Model;
use App\Services\ApiService;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\Request;
class ArticleController extends Controller
{
    // Retrieve All Articles
    public function index(Request $request)
    {
        try {
            $Article_query = Model::query();
            $result = ApiService::format_model_paginate($Article_query, $request['limit'], $request['offset']);
            $result['items'] = ArticleResource::collection($result['items']);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Retrieve Specific Article
    public function show($id)
    {
        try {
            $Article = Model::find($id);
            if ($Article) {
                return response()->json($Article, 200);
            } else {
                return response()->json(['error' => 'This Article not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Create New Article
    public function store(ArticleRequest $request)
    {
        try {
            // Assign validated data to data array
            $data = $request->validated();
            // upload image and assgin its path to data array
            $data['image'] = $this->uploadImage($request, null, $data['title']);
            // get user id
            $user = $request->user();
            $data['user_id'] = $user->id;
            // create Article
            $Article = Model::create($data);
            // format Article
            $Article = new ArticleResource($Article);
            return response()->json($Article, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update Specific Article
    public function update(ArticleRequest $request, $id)
    {
        try {

            // get user id
            $user = $request->user();
            // find Article with provided id
            $Item = Model::where('id', $id)->first();

            if ($Item) {
                if ($Item->user_id != $user->id) {
                    return response()->json(['error' => 'You don\'t has access for this Article'], 401);
                }
                $data = $request->validated();
                // upload image if provided and replace new path with old one if no image is provided keep old path
                $title = isset($data['title']) ? $data['title'] : $Item->title;
                $data['image'] = $this->uploadImage($request, $Item, $title);
                // Update Article with new data
                $Item->update($data);
                // format Article
                $Item = new ArticleResource($Item);

                return response()->json($Item, 200);
            } else {
                return response()->json(['error' => 'This Article not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Delete Specific Article
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $Item = Model::find($id);

            if ($Item) {
                if ($Item->user_id != $user->id) {
                    return response()->json(['error' => 'You don\'t has access for this Article'], 401);
                }
                // remove image from storage before delete Article
                $image = $Item->image;
                if (file_exists($image)) {
                    unlink($image);
                }
                // Soft delete
                $Item->delete();
                return response()->json(204);
            } else {
                return response()->json(['error' => 'This Article not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    private function uploadImage($request, $modelClass, $title)
    {
        $path = 'storage/articles/';
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
