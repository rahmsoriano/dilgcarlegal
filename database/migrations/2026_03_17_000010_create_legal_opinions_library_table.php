<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_opinions_library', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('opinion_number');
            $table->longText('context');
            $table->date('date');
            $table->timestamps();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'sqlite') {
            Schema::table('legal_opinions_library', function (Blueprint $table) {
                $table->fullText(['title', 'opinion_number', 'context']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_opinions_library');
    }
};
