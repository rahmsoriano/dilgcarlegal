<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dilg_opinions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('reference_no')->nullable();
            $table->date('opinion_date')->nullable();
            $table->string('tags')->nullable();
            $table->string('slug')->unique();
            $table->longText('body');
            $table->timestamps();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'sqlite') {
            Schema::table('dilg_opinions', function (Blueprint $table) {
                $table->fullText(['title', 'body']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dilg_opinions');
    }
};
