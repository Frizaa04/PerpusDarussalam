<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('borrowing_id')->nullable()->after('id');
            $table->foreign('borrowing_id')->references('id')->on('borrowings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['borrowing_id']);
            $table->dropColumn('borrowing_id');
        });
    }
};