<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 2. Tabel user_addresses
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('label', 50);
            $table->string('recipient_name', 100);
            $table->string('phone', 20);
            $table->text('full_address');
            $table->string('postal_code', 10)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // 3. Tabel categories (Mendukung Self-Referencing Sub-Kategori)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('name', 100);
            $table->string('slug', 120)->unique('uq_categories_slug');
            $table->string('icon_path', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Tabel products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('sku', 50)->unique('uq_products_sku');
            $table->string('name', 150);
            $table->string('slug', 180)->unique('uq_products_slug');
            $table->text('description')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->decimal('base_price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('unit', 30)->default('pcs');
            $table->unsignedInteger('bonus_points')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Tabel carts (Relasi 1:1 dengan users)
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique('uq_carts_user')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });

        // 6. Tabel cart_items
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['cart_id', 'product_id'], 'uq_cart_product');
        });

        // 7. Tabel vouchers
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique('uq_vouchers_code');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('banner_path', 255)->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 12, 2);
            $table->decimal('max_discount_amount', 12, 2)->nullable();
            $table->decimal('min_purchase_amount', 12, 2)->default(0.00);
            $table->unsignedInteger('points_cost')->default(0);
            $table->unsignedInteger('quota');
            $table->unsignedInteger('used_count')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. Tabel user_vouchers
        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('voucher_id')
                ->constrained('vouchers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('status', ['available', 'used', 'expired'])->default('available');
            $table->timestamp('claimed_at')->useCurrent();
            $table->timestamp('used_at')->nullable();
        });

        // 9. Tabel orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique('uq_orders_number');
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_address_id')
                ->nullable()
                ->constrained('user_addresses')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('voucher_id')
                ->nullable()
                ->constrained('vouchers')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('fulfillment_method', ['delivery', 'pickup'])->default('delivery');
            $table->string('pickup_code', 20)->nullable();
            $table->decimal('subtotal_amount', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('voucher_discount_amount', 12, 2)->default(0.00);
            $table->unsignedInteger('points_redeemed')->default(0);
            $table->decimal('points_discount_amount', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2);
            $table->unsignedInteger('points_earned')->default(0);
            $table->enum('order_status', [
                'pending_payment',
                'processing',
                'ready_for_pickup',
                'on_delivery',
                'completed',
                'cancelled'
            ])->default('pending_payment');
            $table->string('receipt_pdf_path', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 10. Tabel order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('product_name_snapshot', 150);
            $table->decimal('unit_price_snapshot', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });

        // 11. Tabel payments (Relasi 1:1 dengan orders)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->unique('uq_payments_order')
                ->constrained('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('midtrans_transaction_id', 100)->nullable()->unique('uq_payments_midtrans_id');
            $table->string('snap_token', 255)->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->decimal('gross_amount', 12, 2);
            $table->enum('transaction_status', [
                'pending',
                'settlement',
                'capture',
                'deny',
                'cancel',
                'expire',
                'refund'
            ])->default('pending');
            $table->json('raw_webhook_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 12. Tabel point_ledgers
        Schema::create('point_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('transaction_type', ['earn', 'redeem', 'adjust', 'expire']);
            $table->integer('points_amount');
            $table->unsignedInteger('balance_after');
            $table->string('description', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_ledgers');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('user_vouchers');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('user_addresses');
    }
};
