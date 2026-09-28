<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('vehicle_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('override_by')->nullable()->after('notes');
            $table->unsignedInteger('duration_minutes')->nullable()->after('status');
            $table->foreign('override_by')->references('id')->on('users')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('vehicle_movements', function (Blueprint $table) {
            $table->dropForeign(['override_by']);
            $table->dropColumn(['override_by', 'duration_minutes']);
        });
    }
};
