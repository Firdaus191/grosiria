<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GrosiriaStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    // Polling real-time: data KPI disegarkan otomatis setiap 10 detik
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        // 1. Omzet: seluruh order selain 'cancelled'
        $omzet = (float) Order::query()
            ->where('order_status', '!=', 'cancelled')
            ->sum('grand_total');

        // 2. Antrean omnichannel: 1 query agregat untuk seluruh rincian
        $queue = Order::query()
            ->whereIn('order_status', ['processing', 'ready_for_pickup', 'on_delivery'])
            ->selectRaw("COUNT(*) AS total")
            ->selectRaw("COALESCE(SUM(fulfillment_method = 'pickup'), 0) AS pickup_total")
            ->selectRaw("COALESCE(SUM(fulfillment_method = 'delivery'), 0) AS delivery_total")
            ->first();

        // 3. Katalog & stok menipis: 1 query agregat
        $catalog = Product::query()
            ->where('is_active', true)
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('COALESCE(SUM(stock < 20), 0) AS low_stock')
            ->first();

        // 4. Member & sirkulasi G-Poin: 1 query agregat
        $members = User::query()
            ->where('role', 'customer')
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('COALESCE(SUM(points_balance), 0) AS points_total')
            ->first();

        $lowStock = (int) $catalog->low_stock;

        return [
            Stat::make('Total Omzet Transaksi', 'Rp ' . number_format($omzet, 0, ',', '.'))
                ->description('Seluruh order selain berstatus cancelled')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Antrean Order Omnichannel', number_format((int) $queue->total, 0, ',', '.') . ' Pesanan')
                ->description(
                    (int) $queue->pickup_total . ' Pickup (PIN Kasir) • ' .
                    (int) $queue->delivery_total . ' Delivery (Kurir Toko)'
                )
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make('Katalog & Peringatan Stok', number_format((int) $catalog->total, 0, ',', '.') . ' Produk Aktif')
                ->description($lowStock > 0
                    ? $lowStock . ' produk stok menipis (< 20)'
                    : 'Seluruh stok produk aman')
                ->descriptionIcon($lowStock > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->descriptionColor($lowStock > 0 ? 'danger' : 'success')
                ->color($lowStock > 0 ? 'danger' : 'success'),

            Stat::make('Member & Sirkulasi G-Poin', number_format((int) $members->total, 0, ',', '.') . ' Member')
                ->description(number_format((int) $members->points_total, 0, ',', '.') . ' G-Poin beredar')
                ->descriptionIcon('heroicon-m-star')
                ->color('info'),
        ];
    }
}
