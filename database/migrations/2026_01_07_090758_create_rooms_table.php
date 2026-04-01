<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->unsignedInteger('bed_capacity')->default(3);
            $table->enum('status', ['available', 'occupied'])->default('available');
            $table->timestamps();
        });

        foreach (range(1, 40) as $num) {
            DB::table('rooms')->insert([
                'room_number' => (string) $num,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
