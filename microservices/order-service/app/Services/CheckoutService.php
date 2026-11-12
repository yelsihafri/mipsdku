
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private MicroserviceClient $client
    ) {}

    public function checkout(int $userId, string $customerName): Order
    {
        // 1. Ambil keranjang pengguna.
        $cart = $this->client->getCart($userId);
        $items = $cart['items'] ?? [];

        if (empty($items)) {
            throw new RuntimeException('Keranjang belanja masih kosong.');
        }

        $orderItems = [];
        $totalAmount = 0;

        // 2. Ambil detail produk dan validasi stok.
        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId < 1 || $quantity < 1) {
                throw new RuntimeException(
                    'Data item keranjang tidak valid.'
                );
            }

            $product = $this->client->getProduct($productId);

            $price = $product['price'] ?? null;
            $stock = $product['stock'] ?? null;
            $productName = $product['name'] ?? null;

            if (
                !is_numeric($price) ||
                !is_numeric($stock) ||
                empty($productName)
            ) {
                throw new RuntimeException(
                    "Data produk dengan ID {$productId} tidak valid."
                );
            }

            if ($quantity > (int) $stock) {
                throw new RuntimeException(
                    "Stok produk {$productName} tidak mencukupi."
                );
            }

            $subtotal = (float) $price * $quantity;
            $totalAmount += $subtotal;

            // Simpan snapshot nama dan harga produk.
            $orderItems[] = [
                'product_id' => $productId,
                'product_name' => $productName,
                'unit_price' => $price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        // 3. Simpan satu order dan seluruh item dalam satu transaksi.
        $order = DB::transaction(function () use (
            $userId,
            $customerName,
            $totalAmount,
            $orderItems
        ) {
            $order = Order::create([
                'user_id' => $userId,
                'customer_name' => $customerName,
                'product_name' => 'Checkout',
                'quantity' => array_sum(
                    array_column($orderItems, 'quantity')
                ),
                'total_price' => $totalAmount,
                'total_amount' => $totalAmount,
                'status' => 'PENDING',
            ]);

            $order->items()->createMany($orderItems);

            return $order->load('items');
        });

        // 4. Kosongkan keranjang setelah order berhasil disimpan.
        // Kegagalan clear cart tidak membatalkan order.
        try {
            $this->client->clearCart($userId);
        } catch (RuntimeException $e) {
            Log::warning('Order berhasil dibuat, tetapi keranjang gagal dikosongkan.', [
                'order_id' => $order->id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }

        return $order;
    }
}
