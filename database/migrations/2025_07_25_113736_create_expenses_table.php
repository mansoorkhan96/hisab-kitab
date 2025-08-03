<?php

use App\Models\Team;
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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Team::class)->constrained();
            $table->nullableMorphs('expensable');
            $table->foreignId('crop_season_id')->constrained();
            $table->string('title');
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->text('details')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
