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
        Schema::create('rfid_taps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_unit_card_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rfid_uid');
            $table->string('status')->default('invalid'); // valid / invalid
            $table->string('message')->nullable();
            $table->timestamp('tapped_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_taps');
    }
};
