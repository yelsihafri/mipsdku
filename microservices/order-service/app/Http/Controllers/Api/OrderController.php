
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:PENDING,PAID',
        ]);

        $query = Order::with('items')->latest();

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json([
            'data' => $query->get(),
        ], 200);
    }

    public function statistics()
    {
        return response()->json([
            'data' => [
                'total_orders' => Order::count(),
                'pending' => Order::where('status', 'PENDING')->count(),
                'paid' => Order::where('status', 'PAID')->count(),
            ],
        ], 200);
    }

    public function show(string $id)
    {
        $order = Order::with('items')->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $order,
        ], 200);
    }

    public function userOrders(Request $request, string $userId)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:PENDING,PAID',
        ]);

        $query = Order::with('items')
            ->where('user_id', $userId)
            ->latest();

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json([
            'data' => $query->get(),
        ], 200);
    }

    public function checkout(Request $request, CheckoutService $checkoutService)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|min:1',
        ]);

        try {
            $order = $checkoutService->checkout(
                (int) $validated['user_id'],
                'User ' . $validated['user_id']
            );

            return response()->json([
                'message' => 'Checkout berhasil.',
                'data' => $order,
            ], 201);

        } catch (RuntimeException $e) {
            $message = $e->getMessage();

            $isServiceFailure =
                str_contains($message, 'Service') ||
                str_contains($message, 'timeout');

            Log::error('Checkout gagal.', [
                'user_id' => $validated['user_id'],
                'error' => $message,
            ]);

            return response()->json([
                'message' => $isServiceFailure
                    ? 'Layanan eksternal sedang tidak tersedia.'
                    : $message,
            ], $isServiceFailure ? 503 : 409);
        }
    }

    public function pay(string $id)
    {
        try {
            $order = DB::transaction(function () use ($id) {
                $order = Order::where('id', $id)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    return null;
                }

                if ($order->status !== 'PENDING') {
                    throw new RuntimeException(
                        'Pesanan hanya dapat dibayar jika berstatus PENDING.'
                    );
                }

                $order->update([
                    'status' => 'PAID',
                ]);

                return $order->fresh()->load('items');
            });

            if (!$order) {
                return response()->json([
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'message' => 'Pembayaran berhasil.',
                'data' => $order,
            ], 200);

        } catch (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    public function update(Request $request, string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:PENDING,PAID',
        ]);

        $order->update($validated);

        return response()->json([
            'data' => $order->fresh()->load('items'),
        ], 200);
    }

    public function destroy(string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $order->delete();

        return response()->json([
            'message' => 'Pesanan berhasil dihapus.',
        ], 200);
    }
}
