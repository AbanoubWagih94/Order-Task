<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ApiService
{
    public function usersMadePurchasedInLastThirtyDays()
    {
        $users = DB::table('users')
            ->join('purchases', 'users.id', '=', 'purchases.user_id')
            ->where('purchases.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('SUM(purchases.amount) as total_spent')
            )
            ->get();
    }
    public function topFivePurchasedProducts()
    {
        $products = DB::table('products')
            ->join('purchases', 'products.id', '=', 'purchases.product_id')
            ->leftJoin('ratings', 'products.id', '=', 'ratings.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(purchases.quantity) as total_quantity_sold'),
                DB::raw('AVG(ratings.rating) as average_rating')
            )
            ->groupBy('products.id')
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();
    }
    public function refactoredQuary()
    {
        DB::table('orders as o')
            ->join('order_items as oi', 'o.id', '=', 'oi.order_id')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->join('categories as c', 'p.category_id', '=', 'c.id')
            ->select('o.id as orderId', 'o.created_at')
            ->where('c.name', '=', 'Electronics')
            ->where('o.created_at', '>', Carbon::now()->subDays(30))
            ->orderBy('o.created_at', 'DESC')
            ->limit(10)
            ->get();
        // opitmize as Eloquent-based
        $orders = Order::whereHas('orderItems.product.category', function ($query) {
            $query->where('name', '=', 'Electronics');
        })
            ->where('created_at', '>', Carbon::now()->subDays(30))
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }
}
