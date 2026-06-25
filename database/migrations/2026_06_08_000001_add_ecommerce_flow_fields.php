<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status', 40)->default('waiting_verification')->after('payment_number');
            }

            if (! Schema::hasColumn('orders', 'payment_deadline_at')) {
                $table->timestamp('payment_deadline_at')->nullable()->after('payment_status');
            }

            if (! Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_deadline_at');
            }

            if (! Schema::hasColumn('orders', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('orders', 'payment_rejected_at')) {
                $table->timestamp('payment_rejected_at')->nullable()->after('payment_verified_at');
            }

            if (! Schema::hasColumn('orders', 'payment_note')) {
                $table->text('payment_note')->nullable()->after('payment_rejected_at');
            }

            if (! Schema::hasColumn('orders', 'customer_confirmed_at')) {
                $table->timestamp('customer_confirmed_at')->nullable()->after('payment_note');
            }

            if (! Schema::hasColumn('orders', 'sold_counted_at')) {
                $table->timestamp('sold_counted_at')->nullable()->after('customer_confirmed_at');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'account_status')) {
                $table->string('account_status', 30)->default('active')->after('role');
            }

            if (! Schema::hasColumn('users', 'account_status_reason')) {
                $table->text('account_status_reason')->nullable()->after('account_status');
            }

            if (! Schema::hasColumn('users', 'account_status_changed_at')) {
                $table->timestamp('account_status_changed_at')->nullable()->after('account_status_reason');
            }

            if (! Schema::hasColumn('users', 'account_status_changed_by')) {
                $table->foreignId('account_status_changed_by')
                    ->nullable()
                    ->after('account_status_changed_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('produk', function (Blueprint $table) {
            if (! Schema::hasColumn('produk', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('terjual');
            }

            if (! Schema::hasColumn('produk', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('is_active');
            }
        });

        if (! Schema::hasTable('product_size_stocks')) {
            Schema::create('product_size_stocks', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('produk_id');
                $table->string('ukuran', 20);
                $table->string('sku')->nullable();
                $table->unsignedInteger('stok')->default(0);
                $table->unsignedInteger('low_stock_threshold')->default(5);
                $table->timestamps();

                $table->foreign('produk_id')->references('id')->on('produk')->cascadeOnDelete();
                $table->unique(['produk_id', 'ukuran']);
                $table->index(['produk_id', 'stok']);
            });
        }

        Schema::table('returns', function (Blueprint $table) {
            if (! Schema::hasColumn('returns', 'return_stage')) {
                $table->string('return_stage', 40)->default('request_submitted')->after('refund_status');
            }

            if (! Schema::hasColumn('returns', 'courier_photo')) {
                $table->string('courier_photo')->nullable()->after('return_stage');
            }

            if (! Schema::hasColumn('returns', 'courier_note')) {
                $table->text('courier_note')->nullable()->after('courier_photo');
            }

            if (! Schema::hasColumn('returns', 'courier_submitted_at')) {
                $table->timestamp('courier_submitted_at')->nullable()->after('courier_note');
            }

            if (! Schema::hasColumn('returns', 'return_deadline_at')) {
                $table->timestamp('return_deadline_at')->nullable()->after('courier_submitted_at');
            }

            if (! Schema::hasColumn('returns', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('return_deadline_at');
            }

            if (! Schema::hasColumn('returns', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }

            if (! Schema::hasColumn('returns', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('rejected_at');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('product_size_stocks')) {
            Schema::dropIfExists('product_size_stocks');
        }

        Schema::table('returns', function (Blueprint $table) {
            foreach ([
                'return_stage',
                'courier_photo',
                'courier_note',
                'courier_submitted_at',
                'return_deadline_at',
                'approved_at',
                'rejected_at',
                'completed_at',
            ] as $column) {
                if (Schema::hasColumn('returns', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('produk', function (Blueprint $table) {
            foreach (['is_active', 'archived_at'] as $column) {
                if (Schema::hasColumn('produk', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'account_status_changed_by')) {
                $table->dropForeign(['account_status_changed_by']);
            }

            foreach ([
                'account_status',
                'account_status_reason',
                'account_status_changed_at',
                'account_status_changed_by',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            foreach ([
                'payment_status',
                'payment_deadline_at',
                'paid_at',
                'payment_verified_at',
                'payment_rejected_at',
                'payment_note',
                'customer_confirmed_at',
                'sold_counted_at',
            ] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
