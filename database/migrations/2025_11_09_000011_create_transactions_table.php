<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference_number')->unique();
            $table->string('type'); // purchase, sale, expense, income, transfer
            $table->foreignId('account_id')->constrained();
            $table->decimal('amount', 15, 2);
            $table->string('entry_type'); // debit, credit
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};