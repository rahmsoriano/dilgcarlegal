<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faq_responses', function (Blueprint $table) {
            if (! Schema::hasColumn('faq_responses', 'aliases')) {
                $table->text('aliases')->nullable()->after('inquiry_normalized');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faq_responses', function (Blueprint $table) {
            if (Schema::hasColumn('faq_responses', 'aliases')) {
                $table->dropColumn('aliases');
            }
        });
    }
};
