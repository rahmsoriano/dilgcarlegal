<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('conversations', 'is_pinned')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->boolean('is_pinned')->default(false)->after('is_saved');
            });
        }

        if (! Schema::hasColumn('conversations', 'pinned_at')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->timestamp('pinned_at')->nullable()->after('is_pinned');
            });
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'sqlite') {
            Schema::table('conversations', function (Blueprint $table) {
                $table->index(['user_id', 'is_pinned', 'pinned_at', 'last_message_at'], 'conversations_user_pinned_sort');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('conversations', 'is_pinned')) {
            Schema::table('conversations', function (Blueprint $table) {
                $driver = Schema::getConnection()->getDriverName();
                if ($driver !== 'sqlite') {
                    $table->dropIndex('conversations_user_pinned_sort');
                }
                $table->dropColumn(['is_pinned', 'pinned_at']);
            });
        }
    }
};
