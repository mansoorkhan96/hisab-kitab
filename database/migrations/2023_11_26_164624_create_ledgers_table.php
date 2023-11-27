<?php

use App\Models\CropSeason;
use App\Models\Farmer;
use App\Models\FarmingResource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CropSeason::class)->constrained();
            $table->foreignIdFor(Farmer::class)->constrained();
            $table->foreignIdFor(FarmingResource::class)->constrained();
            $table->integer('quantity');
            $table->decimal('rate', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
