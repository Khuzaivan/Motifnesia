<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE notifications MODIFY type VARCHAR(50) NOT NULL DEFAULT 'system'");
        } elseif ($driver !== 'sqlite') {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('type', 50)->default('system')->change();
            });
        }

        if (! Schema::hasColumn('notifications', 'data')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->json('data')->nullable()->after('is_read');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        if (Schema::hasColumn('notifications', 'data')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropColumn('data');
            });
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("UPDATE notifications SET type = 'system' WHERE type NOT IN ('order', 'stock', 'review', 'system')");
            DB::statement("ALTER TABLE notifications MODIFY type ENUM('order', 'stock', 'review', 'system') NOT NULL DEFAULT 'system'");
        }
    }
};
