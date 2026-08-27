<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('BookLogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete(); 
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); 
            $table->enum('activity', [
                'borrow',
                'return',
                'stock_in',
                'stock_out',
                'damaged',
                'lost'
            ])->index();
            $table->integer('quantity')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('BookLogs');
    }
};