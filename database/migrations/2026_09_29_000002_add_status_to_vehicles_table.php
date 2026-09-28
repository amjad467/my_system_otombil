<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('vehicles') && !Schema::hasColumn('vehicles', 'status')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('status')->default('available')->after('active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicles') && Schema::hasColumn('vehicles', 'status')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
