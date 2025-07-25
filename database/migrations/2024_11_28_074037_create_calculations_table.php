<?php

use App\Models\CropSeason;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CropSeason::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->decimal('kudhi_in_kgs', 12, 2)->nullable()->default(0); // TODO: rename
            $table->decimal('kamdari_in_kgs', 12, 2)->nullable()->default(0); // TODO: rename
            $table->unsignedInteger('wheat_rate');
            $table->unsignedInteger('wheat_straw_rate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
