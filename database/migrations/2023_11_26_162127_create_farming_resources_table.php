<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farming_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('name');
            $table->string('type'); // For example: Seed, Fertilizer, Machinery
            $table->string('quantity_unit'); // For example: hours, acres, sacks
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farming_resources');
    }
};
