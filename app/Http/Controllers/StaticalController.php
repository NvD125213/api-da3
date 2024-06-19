<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use App\Models\User;

class StaticalController extends Controller
{
    public function getStatistics()
    {
        $totalOrders = Order::count();

        $totalUsers = User::count();

        $totalProductsSold = OrderDetails::sum('quantity'); 
        
        $totalRevenue = Order::with('orderdetails')
        ->get()
        ->sum(function($order) {
            return $order->orderDetails->sum(function($detail) {
                return $detail->quantity * $detail->price;
            });
        });

        return response()->json([
            'total_orders' => $totalOrders,
            'total_users' => $totalUsers,
            'total_products_sold' => $totalProductsSold,
            'total_revenue' => $totalRevenue,
        ]);
    }
}
