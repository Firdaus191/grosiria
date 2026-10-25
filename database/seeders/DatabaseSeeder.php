<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PointLedger;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserVoucher;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. SEED USERS (3 User Utama untuk Konsistensi Pengujian UTS T08 TC-01)
        // =========================================================================
        $admin = User::create([
            'name' => 'Admin Utama Grosiria',
            'email' => 'admin@grosiria.test',
            'phone' => '081200000001',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'points_balance' => 0,
            'member_tier' => 'platinum',
            'email_verified_at' => now(),
        ]);

        $customer1 = User::create([
            'name' => 'Muhammad Firdaus Yusdiandara',
            'email' => 'firdaus@grosiria.test',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'points_balance' => 4850,
            'member_tier' => 'gold',
            'email_verified_at' => now(),
        ]);

        $customer2 = User::create([
            'name' => 'Pelanggan Setia Grosiria',
            'email' => 'pelanggan@grosiria.test',
            'phone' => '081298765432',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'points_balance' => 1200,
            'member_tier' => 'silver',
            'email_verified_at' => now(),
        ]);

        // =========================================================================
        // 2. SEED ALAMAT PELANGGAN (user_addresses)
        // =========================================================================
        $addr1 = UserAddress::create([
            'user_id' => $customer1->id,
            'label' => 'Kos (Alamat Utama)',
            'recipient_name' => $customer1->name,
            'phone' => $customer1->phone,
            'full_address' => 'Jl. Tlogosari Kulon, Kec. Pedurungan, Kota Semarang, Jawa Tengah',
            'postal_code' => '50196',
            'is_primary' => true,
        ]);

        $addr2 = UserAddress::create([
            'user_id' => $customer2->id,
            'label' => 'Rumah',
            'recipient_name' => $customer2->name,
            'phone' => $customer2->phone,
            'full_address' => 'Jl. Pandanaran No. 45, Semarang Tengah, Kota Semarang',
            'postal_code' => '50134',
            'is_primary' => true,
        ]);

        // =========================================================================
        // 3. SEED 14 KATEGORI PRODUK PERSIS ALFAGIFT (categories)
        // =========================================================================
        $categoriesData = [
            ['name' => 'Kebutuhan Dapur', 'slug' => 'kebutuhan-dapur', 'icon_path' => 'ph-cooking-pot'],
            ['name' => 'Kebutuhan Ibu & Anak', 'slug' => 'kebutuhan-ibu-anak', 'icon_path' => 'ph-baby'],
            ['name' => 'Kebutuhan Rumah', 'slug' => 'kebutuhan-rumah', 'icon_path' => 'ph-house-line'],
            ['name' => 'Makanan', 'slug' => 'makanan', 'icon_path' => 'ph-bowl-food'],
            ['name' => 'Minuman', 'slug' => 'minuman', 'icon_path' => 'ph-coffee'],
            ['name' => 'Produk Segar & Beku', 'slug' => 'produk-segar-beku', 'icon_path' => 'ph-snowflake'],
            ['name' => 'Personal Care', 'slug' => 'personal-care', 'icon_path' => 'ph-sparkle'],
            ['name' => 'Kesehatan & Obat', 'slug' => 'kesehatan-obat', 'icon_path' => 'ph-first-aid-kit'],
            ['name' => 'Perawatan Hewan', 'slug' => 'perawatan-hewan', 'icon_path' => 'ph-paw-print'],
            ['name' => 'Sarapan & Roti', 'slug' => 'sarapan-roti', 'icon_path' => 'ph-bread'],
            ['name' => 'Alat Tulis & Kantor', 'slug' => 'alat-tulis-kantor', 'icon_path' => 'ph-pencil-line'],
            ['name' => 'Elektronik & Baterai', 'slug' => 'elektronik-baterai', 'icon_path' => 'ph-battery-charging'],
            ['name' => 'Produk Otomotif', 'slug' => 'produk-otomotif', 'icon_path' => 'ph-Wrench'],
            ['name' => 'Lifestyle & Hobi', 'slug' => 'lifestyle-hobi', 'icon_path' => 'ph-game-controller'],
        ];

        $catMap = [];
        foreach ($categoriesData as $cat) {
            $created = Category::create([
                'parent_id' => null,
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'icon_path' => $cat['icon_path'],
                'is_active' => true,
            ]);
            $catMap[$cat['slug']] = $created->id;
        }

        // =========================================================================
        // 4. SEED 16 PRODUK E-GROCERY ALA ALFAGIFT (products)
        // =========================================================================
        $products = [
            // Kebutuhan Dapur
            [
                'category_id' => $catMap['kebutuhan-dapur'],
                'sku' => 'GRS-BRS-001',
                'name' => 'Beras Anak Raja Kepala Super 5kg',
                'slug' => 'beras-anak-raja-5kg',
                'description' => 'Beras kepala super pulen kualitas premium kemasan 5kg.',
                'base_price' => 76500,
                'discount_price' => 72400,
                'stock' => 45,
                'unit' => 'sak',
                'bonus_points' => 150,
                'sold_count' => 320,
            ],
            [
                'category_id' => $catMap['kebutuhan-dapur'],
                'sku' => 'GRS-MNY-002',
                'name' => 'Minyak Goreng Kunci Mas 2 Liter',
                'slug' => 'minyak-goreng-kunci-mas-2-liter',
                'description' => 'Minyak goreng kelapa sawit jernih berkualitas kemasan pouch 2L.',
                'base_price' => 39900,
                'discount_price' => 34900,
                'stock' => 80,
                'unit' => 'pouch',
                'bonus_points' => 75,
                'sold_count' => 540,
            ],
            [
                'category_id' => $catMap['kebutuhan-dapur'],
                'sku' => 'GRS-GUL-003',
                'name' => 'Gulaku Gula Pasir Tebu Premium 1kg',
                'slug' => 'gulaku-gula-pasir-premium-1kg',
                'description' => 'Gula pasir putih bersih alami dari tebu pilihan 1kg.',
                'base_price' => 18500,
                'discount_price' => 17200,
                'stock' => 110,
                'unit' => 'pack',
                'bonus_points' => 30,
                'sold_count' => 410,
            ],
            [
                'category_id' => $catMap['kebutuhan-dapur'],
                'sku' => 'GRS-KCP-004',
                'name' => 'Kecap Bango Manis Refill 520ml',
                'slug' => 'kecap-bango-manis-refill-520ml',
                'description' => 'Kecap manis dari kedelai hitam Malika pilihan.',
                'base_price' => 26500,
                'discount_price' => 22900,
                'stock' => 65,
                'unit' => 'pouch',
                'bonus_points' => 40,
                'sold_count' => 275,
            ],
            // Makanan
            [
                'category_id' => $catMap['makanan'],
                'sku' => 'GRS-IDM-005',
                'name' => 'Indomie Mi Instan Goreng Spesial 85g (Paket 5 Pcs)',
                'slug' => 'indomie-goreng-spesial-paket-5-pcs',
                'description' => 'Paket hemat 5 bungkus Indomie Mi Goreng Spesial.',
                'base_price' => 16500,
                'discount_price' => 14900,
                'stock' => 150,
                'unit' => 'pack',
                'bonus_points' => 25,
                'sold_count' => 690,
            ],
            [
                'category_id' => $catMap['makanan'],
                'sku' => 'GRS-SNK-006',
                'name' => 'Chitato Potato Chips Sapi Panggang 68g',
                'slug' => 'chitato-sapi-panggang-68g',
                'description' => 'Keripik kentang bergelombang rasa sapi panggang.',
                'base_price' => 12500,
                'discount_price' => 10500,
                'stock' => 95,
                'unit' => 'pcs',
                'bonus_points' => 20,
                'sold_count' => 310,
            ],
            // Minuman
            [
                'category_id' => $catMap['minuman'],
                'sku' => 'GRS-ULT-007',
                'name' => 'Ultra Milk Susu UHT Full Cream 1 Liter',
                'slug' => 'ultra-milk-uht-full-cream-1-liter',
                'description' => 'Susu cair segar alami berkualitas tinggi kemasan 1L.',
                'base_price' => 21500,
                'discount_price' => 18900,
                'stock' => 70,
                'unit' => 'kotak',
                'bonus_points' => 35,
                'sold_count' => 480,
            ],
            [
                'category_id' => $catMap['minuman'],
                'sku' => 'GRS-KPI-008',
                'name' => 'Kapal Api Kopi Bubuk Special Mix 10 x 23g',
                'slug' => 'kapal-api-special-mix-10x23g',
                'description' => 'Kopi bubuk instan perpaduan biji kopi pilihan dan gula.',
                'base_price' => 17500,
                'discount_price' => 15500,
                'stock' => 85,
                'unit' => 'renceng',
                'bonus_points' => 30,
                'sold_count' => 390,
            ],
            [
                'category_id' => $catMap['minuman'],
                'sku' => 'GRS-AQU-009',
                'name' => 'Aqua Air Mineral 600ml (1 Karton / 24 Botol)',
                'slug' => 'aqua-air-mineral-600ml-karton',
                'description' => 'Air mineral pegunungan kemasan karton grosir isi 24 botol.',
                'base_price' => 58000,
                'discount_price' => 51500,
                'stock' => 14, // Stok sengaja < 20 untuk memicu indikator Stok Menipis di Dashboard
                'unit' => 'karton',
                'bonus_points' => 100,
                'sold_count' => 260,
            ],
            // Personal Care
            [
                'category_id' => $catMap['personal-care'],
                'sku' => 'GRS-PND-010',
                'name' => "Pond's Men Facial Wash Energy Charge 100g",
                'slug' => 'ponds-men-facial-wash-100g',
                'description' => 'Sabun cuci muka pria dengan ekstrak biji kopi untuk wajah cerah berenergi.',
                'base_price' => 46800,
                'discount_price' => 26900,
                'stock' => 60,
                'unit' => 'tube',
                'bonus_points' => 50,
                'sold_count' => 210,
            ],
            [
                'category_id' => $catMap['personal-care'],
                'sku' => 'GRS-LFB-011',
                'name' => 'Lifebuoy Sabun Mandi Cair Total 10 Refill 825ml',
                'slug' => 'lifebuoy-sabun-cair-total-10-825ml',
                'description' => 'Sabun mandi cair antibakteri perlindungan keluarga kemasan besar.',
                'base_price' => 48900,
                'discount_price' => 36900,
                'stock' => 50,
                'unit' => 'pouch',
                'bonus_points' => 60,
                'sold_count' => 340,
            ],
            [
                'category_id' => $catMap['personal-care'],
                'sku' => 'GRS-PEP-012',
                'name' => 'Pepsodent Pasta Gigi Pencegah Gigi Berlubang 225g',
                'slug' => 'pepsodent-pasta-gigi-225g',
                'description' => 'Pasta gigi perlindungan mikro kalsium aktif.',
                'base_price' => 16900,
                'discount_price' => 13500,
                'stock' => 90,
                'unit' => 'tube',
                'bonus_points' => 25,
                'sold_count' => 430,
            ],
            // Kebutuhan Rumah
            [
                'category_id' => $catMap['kebutuhan-rumah'],
                'sku' => 'GRS-TSU-013',
                'name' => 'Laras Facial Tissue 2 Ply 2 x 200s',
                'slug' => 'laras-facial-tissue-2x200s',
                'description' => 'Tisu wajah lembut 100% serat alami kemasan banded hemat.',
                'base_price' => 20900,
                'discount_price' => 15900,
                'stock' => 75,
                'unit' => 'pack',
                'bonus_points' => 35,
                'sold_count' => 510,
            ],
            [
                'category_id' => $catMap['kebutuhan-rumah'],
                'sku' => 'GRS-SNL-014',
                'name' => 'Sunlight Pencuci Piring Jeruk Nipis 100 Refill 650ml',
                'slug' => 'sunlight-jeruk-nipis-650ml',
                'description' => 'Cairan pencuci piring pengangkat lemak membandel.',
                'base_price' => 16500,
                'discount_price' => 12900,
                'stock' => 120,
                'unit' => 'pouch',
                'bonus_points' => 25,
                'sold_count' => 580,
            ],
            // Produk Segar & Beku
            [
                'category_id' => $catMap['produk-segar-beku'],
                'sku' => 'GRS- nugget-015',
                'name' => 'Fiesta Crispy Bubble Chicken Nugget 500g',
                'slug' => 'fiesta-crispy-bubble-nugget-500g',
                'description' => 'Nugget ayam renyah berkualitas tinggi siap goreng.',
                'base_price' => 56000,
                'discount_price' => 44900,
                'stock' => 9, // Stok < 20 (Stok Menipis)
                'unit' => 'pack',
                'bonus_points' => 90,
                'sold_count' => 295,
            ],
            // Kebutuhan Ibu & Anak
            [
                'category_id' => $catMap['kebutuhan-ibu-anak'],
                'sku' => 'GRS-MMP-016',
                'name' => 'MamyPoko Pants X-tra Kering M 32',
                'slug' => 'mamypoko-pants-xtra-kering-m32',
                'description' => 'Popok celana bayi daya serap tinggi hingga 12 jam.',
                'base_price' => 68500,
                'discount_price' => 54900,
                'stock' => 35,
                'unit' => 'bag',
                'bonus_points' => 300,
                'sold_count' => 185,
            ],
        ];

        $createdProducts = [];
        foreach ($products as $prod) {
            $createdProducts[] = Product::create(array_merge($prod, ['is_active' => true]));
        }

        // =========================================================================
        // 5. SEED VOUCHERS PROMO & LOYALTY (vouchers & user_vouchers)
        // =========================================================================
        $v1 = Voucher::create([
            'code' => 'JSMGROSIRIA',
            'title' => 'Promo JSM Potongan Rp 15.000',
            'description' => 'Potongan langsung Rp 15.000 minimal belanja Rp 100.000.',
            'discount_type' => 'fixed',
            'discount_value' => 15000,
            'max_discount_amount' => 15000,
            'min_purchase_amount' => 100000,
            'points_cost' => 0,
            'quota' => 100,
            'used_count' => 12,
            'start_date' => now()->subDays(3),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        $v2 = Voucher::create([
            'code' => 'TEBUS10RB',
            'title' => 'Tebus 1.000 G-Poin Diskon Rp 10.000',
            'description' => 'Tukarkan 1.000 G-Poin untuk voucher potongan belanja Rp 10.000.',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'max_discount_amount' => 10000,
            'min_purchase_amount' => 50000,
            'points_cost' => 1000,
            'quota' => 50,
            'used_count' => 5,
            'start_date' => now()->subDays(3),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        $v3 = Voucher::create([
            'code' => 'GAJIANHEMAT10',
            'title' => 'Diskon Gajian 10% s.d Rp 25.000',
            'description' => 'Diskon 10% maksimal Rp 25.000 dengan minimal belanja Rp 120.000.',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'max_discount_amount' => 25000,
            'min_purchase_amount' => 120000,
            'points_cost' => 500,
            'quota' => 75,
            'used_count' => 8,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        UserVoucher::create([
            'user_id' => $customer1->id,
            'voucher_id' => $v1->id,
            'status' => 'available',
            'claimed_at' => now()->subDay(),
        ]);

        // =========================================================================
        // 6. SEED CONTOH TRANSAKSI OMNICHANNEL (orders, order_items, payments, point_ledgers)
        // =========================================================================
        // Order 1: Omnichannel Pickup (Siap Ambil di Toko dengan PIN Pickup)
        $order1 = Order::create([
            'order_number' => 'INV-20261025-0001',
            'user_id' => $customer1->id,
            'user_address_id' => null,
            'voucher_id' => $v1->id,
            'fulfillment_method' => 'pickup',
            'pickup_code' => 'PIN-8842KP',
            'subtotal_amount' => 142200,
            'shipping_cost' => 0,
            'voucher_discount_amount' => 15000,
            'points_redeemed' => 0,
            'points_discount_amount' => 0,
            'grand_total' => 127200,
            'points_earned' => 300,
            'order_status' => 'ready_for_pickup',
            'notes' => 'Ambil di Toko Utama jam 16.00 WIB',
        ]);

        OrderItem::insert([
            [
                'order_id' => $order1->id,
                'product_id' => $createdProducts[0]->id,
                'product_name_snapshot' => $createdProducts[0]->name,
                'unit_price_snapshot' => 72400,
                'quantity' => 1,
                'subtotal' => 72400,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $order1->id,
                'product_id' => $createdProducts[1]->id,
                'product_name_snapshot' => $createdProducts[1]->name,
                'unit_price_snapshot' => 34900,
                'quantity' => 2,
                'subtotal' => 69800,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Payment::create([
            'order_id' => $order1->id,
            'midtrans_transaction_id' => 'MID-TRX-20261025-001',
            'snap_token' => 'snap-token-simulasi-001',
            'payment_type' => 'qris',
            'gross_amount' => 127200,
            'transaction_status' => 'settlement',
            'paid_at' => now()->subHours(2),
        ]);

        // Order 2: Omnichannel Delivery (Kurir Toko - Sedang Dikirim)
        $order2 = Order::create([
            'order_number' => 'INV-20261025-0002',
            'user_id' => $customer2->id,
            'user_address_id' => $addr2->id,
            'voucher_id' => null,
            'fulfillment_method' => 'delivery',
            'pickup_code' => null,
            'subtotal_amount' => 81700,
            'shipping_cost' => 10000,
            'voucher_discount_amount' => 0,
            'points_redeemed' => 200,
            'points_discount_amount' => 2000,
            'grand_total' => 89700,
            'points_earned' => 110,
            'order_status' => 'on_delivery',
            'notes' => 'Titip di pos satpam jika rumah kosong',
        ]);

        OrderItem::insert([
            [
                'order_id' => $order2->id,
                'product_id' => $createdProducts[9]->id,
                'product_name_snapshot' => $createdProducts[9]->name,
                'unit_price_snapshot' => 26900,
                'quantity' => 1,
                'subtotal' => 26900,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $order2->id,
                'product_id' => $createdProducts[15]->id,
                'product_name_snapshot' => $createdProducts[15]->name,
                'unit_price_snapshot' => 54900,
                'quantity' => 1,
                'subtotal' => 54900,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Payment::create([
            'order_id' => $order2->id,
            'midtrans_transaction_id' => 'MID-TRX-20261025-002',
            'snap_token' => 'snap-token-simulasi-002',
            'payment_type' => 'bank_transfer',
            'gross_amount' => 89700,
            'transaction_status' => 'settlement',
            'paid_at' => now()->subHour(),
        ]);

        PointLedger::create([
            'user_id' => $customer1->id,
            'order_id' => $order1->id,
            'transaction_type' => 'earn',
            'points_amount' => 300,
            'balance_after' => 4850,
            'description' => 'Perolehan G-Poin dari transaksi #INV-20261025-0001',
        ]);
    }
}
