<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasCategoryColumn = Schema::hasColumn('laws', 'category');
        $hasLawNumberColumn = Schema::hasColumn('laws', 'law_number');

        if (! Schema::hasColumn('laws', 'year')) {
            Schema::table('laws', function (Blueprint $table) use ($hasCategoryColumn, $hasLawNumberColumn) {
                $column = $table->integer('year')->nullable();

                if ($hasCategoryColumn) {
                    $column->after('category');
                } elseif ($hasLawNumberColumn) {
                    $column->after('law_number');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('laws', 'year')) {
            Schema::table('laws', function (Blueprint $table) {
                $table->dropColumn('year');
            });
        }
    }
};
