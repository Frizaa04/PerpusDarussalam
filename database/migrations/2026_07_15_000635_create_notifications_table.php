<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')
                ->constrained('borrowings')
                ->cascadeOnDelete(); 
            $table->string('title');
            $table->text('message');
            $table->enum('status', [
                'unread',
                'read'
            ])->default('unread')->index(); 
            $table->string('type', 50)->index(); 
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};