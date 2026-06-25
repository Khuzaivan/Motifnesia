<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('status', 30)->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['status', 'name']);
            });
        }

        if (! Schema::hasTable('stock_procurements')) {
            Schema::create('stock_procurements', function (Blueprint $table) {
                $table->id();
                $table->string('procurement_number')->unique();
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 40)->default('pending');
                $table->text('note')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('in_delivery_at')->nullable();
                $table->timestamp('arrived_at')->nullable();
                $table->timestamp('stock_applied_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'created_at']);
            });
        }

        if (! Schema::hasTable('stock_procurement_items')) {
            Schema::create('stock_procurement_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_procurement_id')->constrained('stock_procurements')->cascadeOnDelete();
                $table->unsignedInteger('produk_id');
                $table->unsignedInteger('qty_s')->default(0);
                $table->unsignedInteger('qty_m')->default(0);
                $table->unsignedInteger('qty_l')->default(0);
                $table->unsignedInteger('qty_xl')->default(0);
                $table->unsignedInteger('total_qty')->default(0);
                $table->timestamps();

                $table->foreign('produk_id')->references('id')->on('produk')->cascadeOnDelete();
                $table->unique(['stock_procurement_id', 'produk_id']);
                $table->index(['produk_id']);
            });
        }

        if (! Schema::hasTable('warehouse_stocks')) {
            Schema::create('warehouse_stocks', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('produk_id');
                $table->string('ukuran', 20);
                $table->unsignedInteger('stok')->default(0);
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->foreign('produk_id')->references('id')->on('produk')->cascadeOnDelete();
                $table->unique(['produk_id', 'ukuran']);
                $table->index(['produk_id', 'stok']);
            });
        }

        if (
            Schema::hasTable('warehouse_stocks')
            && Schema::hasTable('product_size_stocks')
            && DB::table('warehouse_stocks')->count() === 0
        ) {
            DB::table('product_size_stocks')
                ->select(['produk_id', 'ukuran', 'stok'])
                ->orderBy('id')
                ->chunk(200, function ($stocks) {
                    $now = now();
                    $rows = $stocks->map(fn ($stock) => [
                        'produk_id' => $stock->produk_id,
                        'ukuran' => $stock->ukuran,
                        'stok' => (int) $stock->stok,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();

                    if (! empty($rows)) {
                        DB::table('warehouse_stocks')->insertOrIgnore($rows);
                    }
                });
        }

        if (! Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('produk_id');
                $table->string('ukuran', 20);
                $table->string('movement_type', 40);
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->integer('qty_change')->default(0);
                $table->integer('system_stock_before')->default(0);
                $table->integer('system_stock_after')->default(0);
                $table->integer('warehouse_stock_before')->default(0);
                $table->integer('warehouse_stock_after')->default(0);
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('produk_id')->references('id')->on('produk')->cascadeOnDelete();
                $table->index(['produk_id', 'ukuran', 'created_at']);
                $table->index(['reference_type', 'reference_id']);
            });
        }

        if (! Schema::hasTable('stock_opnames')) {
            Schema::create('stock_opnames', function (Blueprint $table) {
                $table->id();
                $table->string('opname_number')->unique();
                $table->unsignedInteger('produk_id');
                $table->string('ukuran', 20);
                $table->integer('system_stock_before')->default(0);
                $table->integer('warehouse_stock_before')->default(0);
                $table->integer('system_stock_after')->default(0);
                $table->integer('difference')->default(0);
                $table->foreignId('adjusted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('note')->nullable();
                $table->timestamps();

                $table->foreign('produk_id')->references('id')->on('produk')->cascadeOnDelete();
                $table->index(['produk_id', 'ukuran', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('warehouse_stocks');
        Schema::dropIfExists('stock_procurement_items');
        Schema::dropIfExists('stock_procurements');
        Schema::dropIfExists('suppliers');
    }
};
