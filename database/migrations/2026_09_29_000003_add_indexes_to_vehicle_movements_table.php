<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_movements', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_movements_user_status');
            $table->index(['vehicle_id', 'status'], 'idx_movements_vehicle_status');
            $table->index('departure_time', 'idx_movements_departure_time');
            $table->index('status', 'idx_movements_status');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_movements', function (Blueprint $table) {
            $table->dropIndex('idx_movements_user_status');
            $table->dropIndex('idx_movements_vehicle_status');
            $table->dropIndex('idx_movements_departure_time');
            $table->dropIndex('idx_movements_status');
        });
    }
};
