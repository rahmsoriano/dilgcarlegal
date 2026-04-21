<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq_responses', function (Blueprint $table) {
            $table->id();
            $table->text('inquiry');
            $table->string('inquiry_normalized', 255)->index();
            $table->text('response');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_responses');
    }
};

