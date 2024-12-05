<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Chave primária auto-increment
            $table->string('name'); // Nome do produto
            $table->string('image_src')->nullable(); // Imagem do produto
            $table->string('image_alt')->nullable(); // Texto alternativo da imagem
            $table->string('price')->nullable(); // Preço do produto
            $table->string('price_off')->nullable(); // Preço com desconto
            $table->text('description')->nullable(); // Descrição do produto
            $table->string('brand')->nullable(); // Marca do produto
            $table->timestamps(); // Timestamps de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
