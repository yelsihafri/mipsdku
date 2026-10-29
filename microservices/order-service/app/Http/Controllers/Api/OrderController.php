<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
{
    $validated = $request->validate([
        'status' => 'sometimes|in:pending,processing,completed,cancelled',
    ]);

    $query = Order::latest();

    if (isset($validated['status'])) {
        $query->where('status', $validated['status']);
    }

    return response()->json([
        'message' => 'Daftar pesanan berhasil diambil',
        'data' => $query->get(),
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

    public function update(Request $request, string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'customer_name' => 'sometimes|required|string|max:255',
            'product_name' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|integer|min:1',
            'total_price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:pending,processing,completed,cancelled',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Pesanan berhasil diperbarui',
            'data' => $order->fresh(),
        ], 200);
    }

    public function destroy(string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        $order->delete();

        return response()->json([
            'message' => 'Pesanan berhasil dihapus',
        ], 200);
    }
}
