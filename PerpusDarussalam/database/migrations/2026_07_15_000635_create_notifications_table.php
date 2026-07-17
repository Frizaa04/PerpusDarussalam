<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void{
        Schema::create('Notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('status', [
                'unread',
                'read'
            ])->default('unread');
            $table->enum('type', [
                'overdue',
                'return',
                'borrow'
            ])->default('overdue');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
