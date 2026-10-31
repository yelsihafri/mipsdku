<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private MicroserviceClient $client
    ) {}

    public function checkout(int $userId, string $customerName): array
    {
        // 1. Ambil keranjang pengguna.
        $cart = $this->client->getCart($userId);

        $items = $cart['items'] ?? [];

        if (empty($items)) {
            throw new RuntimeException('Keranjang belanja masih kosong.');
        }

        $orderData = [];

        // 2. Ambil detail produk dan hitung total.
        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId < 1 || $quantity < 1) {
                throw new RuntimeException('Data item keranjang tidak valid.');
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

            $orderData[] = [
                'customer_name' => $customerName,
                'product_name' => $productName,
                'quantity' => $quantity,
                'total_price' => (float) $price * $quantity,
                'status' => 'pending',
            ];
        }

        // 3. Simpan seluruh pesanan dalam satu database transaction.
        $orders = DB::transaction(function () use ($orderData) {
            $createdOrders = [];

            foreach ($orderData as $data) {
                $createdOrders[] = Order::create($data);
            }

            return $createdOrders;
        });

        // 4. Kosongkan keranjang setelah transaksi database berhasil.
        $this->client->clearCart($userId);

        return $orders;
    }
}