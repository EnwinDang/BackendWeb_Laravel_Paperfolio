<?php

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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('direction'); // 'long' or 'short'
            $table->unsignedInteger('leverage'); // 5, 10, or 100
            $table->decimal('margin_usd', 20, 8); // cash locked as collateral
            $table->decimal('entry_price', 20, 8);
            $table->decimal('close_price', 20, 8)->nullable();
            $table->string('status')->default('open'); // 'open', 'closed', 'liquidated'
            $table->decimal('realized_pnl', 20, 8)->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
