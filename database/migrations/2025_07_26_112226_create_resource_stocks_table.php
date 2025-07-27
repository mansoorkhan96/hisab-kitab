<?php

use App\Models\CropSeason;
use App\Models\FarmingResource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resource_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CropSeason::class)->constrained();
            $table->foreignIdFor(FarmingResource::class)->constrained();
            $table->decimal('quantity', 10, 2);
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('date');
            $table->string('supplier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_stocks');
    }
};
