<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE produk SET terjual = '0' WHERE terjual IS NULL OR terjual = '' OR terjual NOT REGEXP '^[0-9]+$'");
        } else {
            DB::table('produk')
                ->whereNull('terjual')
                ->orWhere('terjual', '')
                ->update(['terjual' => '0']);
        }

        if ($driver !== 'sqlite') {
            Schema::table('produk', function (Blueprint $table) {
                $table->integer('terjual')->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('produk', function (Blueprint $table) {
                $table->string('terjual', 255)->nullable()->change();
            });
        }
    }
};
