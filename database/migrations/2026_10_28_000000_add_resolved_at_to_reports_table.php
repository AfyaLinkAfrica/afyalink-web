<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->timestamp('resolved_at')->nullable()->index()->after('status');
        });

        // Reports resolved before this column existed: the last update was the resolution.
        DB::table('reports')
            ->where('status', 'resolved')
            ->whereNull('resolved_at')
            ->update(['resolved_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['resolved_at']);
            $table->dropColumn('resolved_at');
        });
    }
};
