<?php

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
        Schema::create('ai_learned_knowledge', function (Blueprint $table) {
            $table->id();
            $table->text('query');
            $table->text('response');
            $table->json('metadata')->nullable(); // To store source info, model, etc.
            $table->timestamps();
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('ai_learned_knowledge', function (Blueprint $table) {
                $table->fullText(['query']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_learned_knowledge');
    }
};
