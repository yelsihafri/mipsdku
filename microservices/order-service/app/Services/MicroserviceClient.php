<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use RuntimeException;

class MicroserviceClient
{
    public function getCart(int $userId): array
    {
        try {
            $response = Http::timeout(
                config('services.cart_service.timeout')
            )->get(
                rtrim(config('services.cart_service.url'), '/') . "/carts/{$userId}"
            );

            if (!$response->successful()) {
                throw new RuntimeException(
                    'Gagal mengambil keranjang dari Cart Service.'
                );
            }

            $data = $response->json('data');

            if (!is_array($data)) {
                throw new RuntimeException(
                    'Format respons Cart Service tidak valid.'
                );
            }

            return $data;
        } catch (ConnectionException $e) {
            throw new RuntimeException(
                'Cart Service tidak dapat dihubungi atau mengalami timeout.',
                0,
                $e
            );
        }
    }

    public function getProduct(int $productId): array
    {
        try {
            $response = Http::timeout(
                config('services.product_service.timeout')
            )->get(
                rtrim(config('services.product_service.url'), '/') . "/products/{$productId}"
            );

            if (!$response->successful()) {
                throw new RuntimeException(
                    "Gagal mengambil produk dengan ID {$productId} dari Product Service."
                );
            }

            $data = $response->json('data');

            if (!is_array($data)) {
                throw new RuntimeException(
                    'Format respons Product Service tidak valid.'
                );
            }

            return $data;
        } catch (ConnectionException $e) {
            throw new RuntimeException(
                'Product Service tidak dapat dihubungi atau mengalami timeout.',
                0,
                $e
            );
        }
    }

    public function clearCart(int $userId): void
    {
        try {
            $response = Http::timeout(
                config('services.cart_service.timeout')
            )->delete(
                rtrim(config('services.cart_service.url'), '/') . "/carts/{$userId}"
            );

            if (!$response->successful()) {
                throw new RuntimeException(
                    'Gagal mengosongkan keranjang pada Cart Service.'
                );
            }
        } catch (ConnectionException $e) {
            throw new RuntimeException(
                'Cart Service tidak dapat dihubungi saat mengosongkan keranjang.',
                0,
                $e
            );
        }
    }
}