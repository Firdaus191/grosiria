<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PointLedger;
use App\Models\User;
use App\Models\UserVoucher;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class GrosiriaOrderService
{
    /**
     * Logika Tebus Voucher dengan Poin (Sesuai Flowchart: Voucher Marketplace)
     */
    public function redeemVoucherWithPoints(User $user, Voucher $voucher): UserVoucher
    {
        return DB::transaction(function () use ($user, $voucher) {
            // Row lock: cegah race condition (kuota voucher & saldo poin tertebus ganda)
            $voucher = Voucher::query()->lockForUpdate()->findOrFail($voucher->id);
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            if (!$voucher->is_active || $voucher->used_count >= $voucher->quota) {
                throw new Exception('Voucher tidak aktif atau kuota telah habis.');
            }

            if ($user->points_balance < $voucher->points_cost) {
                throw new Exception('Saldo G-Poin Anda tidak mencukupi untuk menebus voucher ini.');
            }

            if ($voucher->points_cost > 0) {
                $user->decrement('points_balance', $voucher->points_cost);
                $user->refresh();

                PointLedger::create([
                    'user_id' => $user->id,
                    'order_id' => null,
                    'transaction_type' => 'redeem',
                    'points_amount' => -$voucher->points_cost,
                    'balance_after' => $user->points_balance,
                    'description' => "Tebus Voucher Promo: {$voucher->code}",
                ]);
            }

            // Kuota terpakai bertambah saat voucher diklaim
            $voucher->increment('used_count');

            return UserVoucher::create([
                'user_id' => $user->id,
                'voucher_id' => $voucher->id,
                'status' => 'available',
                'claimed_at' => now(),
            ]);
        });
    }

    /**
     * Logika Penyelesaian Pembayaran Webhook Midtrans (Sesuai Flowchart: Webhook Handler)
     * - Mengubah status order -> processing / ready_for_pickup (+ PIN Pickup)
     * - Mengurangi stok produk (products.stock) & menambah sold_count
     * - Menandai voucher milik pelanggan sebagai terpakai
     * - Menambahkan poin loyalitas (users.points_balance & point_ledgers)
     */
    public function handleOrderPaid(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Lock baris order: webhook ganda dari Midtrans tidak akan diproses dua kali (idempotent)
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->order_status !== 'pending_payment') {
                return;
            }

            // 1. Update status order & generate PIN Pickup jika metode pickup
            $pickupCode = $order->fulfillment_method === 'pickup'
                ? 'PIN-' . strtoupper(Str::random(6))
                : null;

            $order->update([
                'order_status' => $order->fulfillment_method === 'pickup' ? 'ready_for_pickup' : 'processing',
                'pickup_code' => $pickupCode,
            ]);

            // 2. Kurangi stok produk & tambah sold_count untuk Static Recommendation Engine
            //    (satu query UPDATE atomik per item, tanpa lazy-load relasi product)
            foreach ($order->items as $item) {
                DB::table('products')
                    ->where('id', $item->product_id)
                    ->update([
                        'stock' => DB::raw('GREATEST(stock - ' . (int) $item->quantity . ', 0)'),
                        'sold_count' => DB::raw('sold_count + ' . (int) $item->quantity),
                        'updated_at' => now(),
                    ]);
            }

            // 3. Tandai voucher pelanggan sebagai terpakai (klaim voucher)
            if ($order->voucher_id) {
                UserVoucher::query()
                    ->where('user_id', $order->user_id)
                    ->where('voucher_id', $order->voucher_id)
                    ->where('status', 'available')
                    ->limit(1)
                    ->update(['status' => 'used', 'used_at' => now()]);
            }

            // 4. Tambahkan Poin Loyalitas ke Pelanggan
            if ($order->points_earned > 0) {
                $user = User::query()->lockForUpdate()->findOrFail($order->user_id);
                $user->increment('points_balance', $order->points_earned);
                $user->refresh();

                PointLedger::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'transaction_type' => 'earn',
                    'points_amount' => $order->points_earned,
                    'balance_after' => $user->points_balance,
                    'description' => "Perolehan G-Poin dari transaksi #{$order->order_number}",
                ]);
            }
        });
    }
}
