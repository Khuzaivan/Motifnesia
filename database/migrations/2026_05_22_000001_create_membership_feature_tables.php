<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'is_member')) {
                    $table->boolean('is_member')->default(false);
                }

                if (! Schema::hasColumn('users', 'membership_status')) {
                    $table->string('membership_status', 20)->nullable();
                }

                if (! Schema::hasColumn('users', 'membership_joined_at')) {
                    $table->timestamp('membership_joined_at')->nullable();
                }

                if (! Schema::hasColumn('users', 'reward_points')) {
                    $table->integer('reward_points')->default(0);
                }
            });
        }

        if (! Schema::hasTable('point_transactions')) {
            Schema::create('point_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->string('type', 20);
                $table->integer('points');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'type']);
                $table->index(['order_id', 'type']);
            });
        }

        if (! Schema::hasTable('membership_rewards')) {
            Schema::create('membership_rewards', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('points_required');
                $table->string('discount_type', 30);
                $table->integer('discount_value')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'points_required']);
            });
        }

        if (! Schema::hasTable('user_reward_redemptions')) {
            Schema::create('user_reward_redemptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('reward_id')->constrained('membership_rewards')->onDelete('cascade');
                $table->string('voucher_code')->unique();
                $table->integer('points_used');
                $table->string('status', 20)->default('active');
                $table->timestamp('redeemed_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reward_redemptions');
        Schema::dropIfExists('membership_rewards');
        Schema::dropIfExists('point_transactions');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                foreach (['is_member', 'membership_status', 'membership_joined_at', 'reward_points'] as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
