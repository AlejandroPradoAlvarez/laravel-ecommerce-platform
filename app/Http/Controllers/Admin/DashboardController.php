<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'productsCount' => Product::count(),
            'usersCount' => User::where('role', 'user')->count(),
            'ordersCount' => Order::count(),
            'revenue' => Order::whereNotIn('status', ['cancelled'])->sum('total'),
            'recentOrders' => Order::with('user')->latest()->take(5)->get(),
        ]);
    }
}
