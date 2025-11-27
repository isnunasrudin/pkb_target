<?php

use App\Models\Rt;
use App\Models\Tps;
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
        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nik')->nullable();
            $table->string('gender')->nullable();
            $table->unsignedInteger('age');
            $table->foreignIdFor(Rt::class)->constrained();
            $table->foreignIdFor(Tps::class)->constrained();
            $table->timestamps();
            
            $table->index(['rt_id', 'tps_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};
