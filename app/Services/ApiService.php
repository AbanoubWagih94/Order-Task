<?php

namespace App\Services;



class ApiService
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }

        return $err_keeper;
    }

    public static function format_model_paginate($model, $limit = 10, $offset = 1)
    {
        $paginator = $model->paginate($limit, ['*'], 'page', $offset);
        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'items' => $paginator->items()
        ];
    }
}
