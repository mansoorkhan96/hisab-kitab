<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crop_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('name');
            $table->boolean('is_current')->default(false);
            $table->timestamps();

            $table->unique(['name', 'user_id']); // TODO:

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_seasons');
    }
};
