<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Daftar pesanan berhasil diambil',
            'data' => Order::latest()->get(),
        ], 200);
    }
    
    public function show(string $id)
{
    $order = Order::find($id);

    if (!$order) {
        return response()->json([
            'message' => 'Pesanan tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'message' => 'Detail pesanan berhasil diambil',
        'data' => $order,
    ], 200);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
        ]);

        $order = Order::create($validated);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat',
            'data' => $order,
        ], 201);
    }
}