<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('legal_opinions_library', function (Blueprint $table) {
            if (! Schema::hasColumn('legal_opinions_library', 'keywords')) {
                $table->text('keywords')->nullable()->after('opinion_number');
            }

            if (! Schema::hasColumn('legal_opinions_library', 'opinion_no')) {
                $table->unsignedInteger('opinion_no')->nullable()->after('keywords');
            }

            if (! Schema::hasColumn('legal_opinions_library', 'opinion_year')) {
                $table->unsignedInteger('opinion_year')->nullable()->after('opinion_no');
            }
        });
    }

    public function down(): void
    {
        // Intentionally left blank. These columns may predate this safeguard migration
        // in existing installations, so rolling it back should not remove live data.
    }
};
