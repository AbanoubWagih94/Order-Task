<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task as Model;
use App\Services\ApiService;
use App\Services\ImageService;
use Exception;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Retrieve All Tasks
    public function index(Request $request)
    {
        try {
            $Task_query = Model::query();
            $result = ApiService::format_model_paginate($Task_query, $request['limit'], $request['offset']);
            $result['items'] = TaskResource::collection($result['items']);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Retrieve Specific Task
    public function show($id)
    {
        try {
            $Task = Model::find($id);
            if ($Task) {
                return response()->json($Task, 200);
            } else {
                return response()->json(['error' => 'This Task not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Create New Task
    public function store(TaskRequest $request)
    {
        try {
            // Assign validated data to data array
            $data = $request->validated();
            // get user id
            $user = $request->user();
            $data['user_id'] = $user->id;
            // create Task
            $Task = Model::create($data);
            // format Task
            $Task = new TaskResource($Task);
            return response()->json($Task, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update Specific Task
    public function update(TaskRequest $request, $id)
    {
        try {

            // get user id
            $user = $request->user();
            // find Task with provided id
            $Item = Model::where('id', $id)->first();

            if ($Item) {
                if ($Item->user_id != $user->id) {
                    return response()->json(['error' => 'You don\'t has access for this Task'], 401);
                }
                $data = $request->validated();

                // Update Task with new data
                $Item->update($data);
                // format Task
                $Item = new TaskResource($Item);

                return response()->json($Item, 200);
            } else {
                return response()->json(['error' => 'This Task not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

    // Delete Specific Task
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $Item = Model::find($id);

            if ($Item) {
                if ($Item->user_id != $user->id) {
                    return response()->json(['error' => 'You don\'t has access for this Task'], 401);
                }
                // Soft delete
                $Item->delete();
                return response()->json(204);
            } else {
                return response()->json(['error' => 'This Task not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Something goes wrong! try again later'], 500);
        }
    }

}
