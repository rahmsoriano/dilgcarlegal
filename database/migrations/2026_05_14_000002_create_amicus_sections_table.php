<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amicus_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_title');
            $table->string('category')->nullable();
            $table->longText('section_content');
            $table->timestamps();

            $table->index(['category', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amicus_sections');
    }
};
