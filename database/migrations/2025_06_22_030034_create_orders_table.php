<?php

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('identifier')->unique();
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->boolean('tax_included')->default(false);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('tipping_percentage', 5, 2)->default(0.00);
            $table->decimal('tipping', 10, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->enum('status', OrderStatusEnum::keys())->default(OrderStatusEnum::PENDING->value);
            $table->enum('order_type', OrderTypeEnum::keys())->default(OrderTypeEnum::DINE_IN->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
