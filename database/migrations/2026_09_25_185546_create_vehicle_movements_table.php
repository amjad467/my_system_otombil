
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    Schema::dropIfExists('vehicle_movements');



Schema::create('vehicle_movements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('destination');
    $table->timestamp('departure_time');
    $table->timestamp('return_time')->nullable();
    $table->string('status')->default('out');
    $table->timestamps();


});
}

public function down()
{
    Schema::dropIfExists('vehicle_movements');
}};
