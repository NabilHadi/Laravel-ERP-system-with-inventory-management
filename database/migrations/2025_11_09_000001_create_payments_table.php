<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type'); // sale_payment, purchase_payment
            $table->unsignedBigInteger('reference_id'); // sale_id or purchase_id
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // cash, bank_transfer, check, card
            $table->date('payment_date');
            $table->string('status')->default('completed'); // completed, pending, failed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('check_number')->nullable();
            $table->date('check_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_details');
        Schema::dropIfExists('payments');
    }
};