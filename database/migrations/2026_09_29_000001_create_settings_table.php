<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group')->default('general');
                $table->timestamps();
            });

            // Insert default settings
            DB::table('settings')->insert([
                ['key' => 'org_name', 'value' => 'سیستەمی بەڕێوەبردنی ئۆتۆمبێل', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'attention_hours', 'value' => '2', 'group' => 'thresholds', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'warning_hours', 'value' => '4', 'group' => 'thresholds', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'dark_mode', 'value' => '0', 'group' => 'appearance', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
