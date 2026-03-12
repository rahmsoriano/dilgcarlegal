<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->boolean('is_saved')->default(false);
            $table->timestamp('saved_at')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'last_message_at']);
            $table->index(['user_id', 'is_saved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
