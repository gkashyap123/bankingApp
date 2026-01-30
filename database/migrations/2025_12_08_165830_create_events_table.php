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
        Schema::create('events', function (Blueprint $table) {
          $table->id();
          $table->foreignId('customer_id')->constrained();
          $table->string('type'); // SIP_renewal, FD_maturity, FOLLOW_UP
          $table->date('event_date');
          $table->text('note')->nullable();
          $table->boolean('notified')->default(false);
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
