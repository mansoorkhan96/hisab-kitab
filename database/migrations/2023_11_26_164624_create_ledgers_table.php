<?php

use App\Models\CropSeason;
use App\Models\FarmingResource;
use App\Models\Tractor;
use App\Models\User;
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
            $table->foreignIdFor(User::class)->comment('Farmer')->constrained();
            $table->foreignIdFor(FarmingResource::class)->constrained();
            $table->foreignIdFor(Tractor::class)->nullable()->constrained();
            $table->decimal('quantity', 10, 2);
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->virtualAs('quantity * rate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
