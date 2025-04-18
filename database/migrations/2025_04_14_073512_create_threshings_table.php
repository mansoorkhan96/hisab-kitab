<?php

use App\Models\Calculation;
use App\Models\Tractor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threshings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Calculation::class)->constrained();
            $table->foreignIdFor(Tractor::class)->constrained();
            $table->decimal('total_wheat_sacks', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threshings');
    }
};
