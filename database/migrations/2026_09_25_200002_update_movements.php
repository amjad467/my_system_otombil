<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('vehicle_movements', function(Blueprint $table){ $table->text('purpose')->nullable()->after('destination'); $table->text('notes')->nullable()->after('status'); }); } public function down(): void { Schema::table('vehicle_movements', function(Blueprint $table){ $table->dropColumn(['purpose','notes']); }); } };
