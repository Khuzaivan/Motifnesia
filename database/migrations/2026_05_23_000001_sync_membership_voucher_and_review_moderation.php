<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'membership_redemption_id')) {
                    $table->foreignId('membership_redemption_id')
                        ->nullable()
                        ->after('payment_number')
                        ->constrained('user_reward_redemptions')
                        ->nullOnDelete();
                }

                if (! Schema::hasColumn('orders', 'voucher_code')) {
                    $table->string('voucher_code')->nullable()->after('membership_redemption_id');
                }

                if (! Schema::hasColumn('orders', 'voucher_discount')) {
                    $table->decimal('voucher_discount', 15, 2)->default(0)->after('voucher_code');
                }
            });
        }

        if (Schema::hasTable('order_reviews')) {
            Schema::table('order_reviews', function (Blueprint $table) {
                if (! Schema::hasColumn('order_reviews', 'moderation_status')) {
                    $table->string('moderation_status', 20)->default('approved')->after('deskripsi_ulasan');
                }

                if (! Schema::hasColumn('order_reviews', 'moderation_note')) {
                    $table->text('moderation_note')->nullable()->after('moderation_status');
                }

                if (! Schema::hasColumn('order_reviews', 'moderated_at')) {
                    $table->timestamp('moderated_at')->nullable()->after('moderation_note');
                }

                if (! Schema::hasColumn('order_reviews', 'moderated_by')) {
                    $table->foreignId('moderated_by')
                        ->nullable()
                        ->after('moderated_at')
                        ->constrained('users')
                        ->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'membership_redemption_id')) {
                    $table->dropForeign(['membership_redemption_id']);
                    $table->dropColumn('membership_redemption_id');
                }

                foreach (['voucher_code', 'voucher_discount'] as $column) {
                    if (Schema::hasColumn('orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('order_reviews')) {
            Schema::table('order_reviews', function (Blueprint $table) {
                if (Schema::hasColumn('order_reviews', 'moderated_by')) {
                    $table->dropForeign(['moderated_by']);
                    $table->dropColumn('moderated_by');
                }

                foreach (['moderation_status', 'moderation_note', 'moderated_at'] as $column) {
                    if (Schema::hasColumn('order_reviews', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
