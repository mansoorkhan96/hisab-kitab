<?php

use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Team::class)->constrained();
            $table->string('title');
            $table->boolean('is_current')->default(false);
            $table->unsignedInteger('wheat_rate')->nullable();
            $table->unsignedInteger('wheat_straw_rate')->nullable();
            $table->unsignedInteger('cotton_rate_per_kg')->nullable();
            $table->unsignedInteger('cotton_labour_rate_per_kg')->nullable();
            $table->timestamps();

            $table->unique(
                ['team_id', 'title'],
                'unique_crop_season_title'
            );

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_seasons');
    }
};
