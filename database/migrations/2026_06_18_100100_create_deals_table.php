<?php

use App\Models\Deal;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Negócios (oportunidades) de um cliente dentro do funil de vendas.
     */
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('value', 12, 2)->default(0);
            $table->enum('stage', array_keys(Deal::STAGES))->default(Deal::STAGE_PROSPECTING);
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['stage', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
