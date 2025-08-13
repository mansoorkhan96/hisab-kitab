<?php

use App\Models\CottonPickingRound;
use App\Models\Labourer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotton_picking_dailies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CottonPickingRound::class)->constrained();
            $table->foreignIdFor(Labourer::class)->constrained();
            $table->date('picking_date');
            $table->unsignedTinyInteger('kgs_picked')->default(0);
            $table->timestamps();

            $table->unique(
                [
                    'cotton_picking_round_id',
                    'labourer_id',
                    'picking_date',
                ],
                'unique_cotton_picking_daily'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotton_picking_dailies');
    }
};
