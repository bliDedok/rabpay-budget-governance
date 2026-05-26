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
        Schema::create('field_unit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_unit_id')->constrained()->cascadeOnDelete();
            $table->string('card_code')->unique();
            $table->string('rfid_uid')->unique();
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_unit_cards');
    }
};
