<?php

use App\Models\Farmer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Farmer::class)->constrained();
            $table->decimal('amount', 10, 2);
            $table->text('purpose');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_loans');
    }
};
