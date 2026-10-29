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
        'per_page' => 'sometimes|integer|min:1|max:100',
        'page' => 'sometimes|integer|min:1',
        'search' => 'sometimes|string|max:100',
    ]);

    $query = Order::latest();

    if (isset($validated['status'])) {
        $query->where('status', $validated['status']);
    }

    if (isset($validated['search'])) {
        $query->where(
            'customer_name',
            'like',
            '%' . $validated['search'] . '%'
        );
    }

    $perPage = $validated['per_page'] ?? 10;

    $orders = $query->paginate($perPage);

    return response()->json([
        'message' => 'Daftar pesanan berhasil diambil',
        'data' => $orders->items(),
        'pagination' => [
            'current_page' => $orders->currentPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total(),
            'last_page' => $orders->lastPage(),
        ],
    ], 200);
}
    public function statistics()
{
    return response()->json([
        'message' => 'Statistik pesanan berhasil diambil',
        'data' => [
            'total_orders' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ],
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
