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
        Schema::table('exam_subject', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_subject', 'qty')) {
                $table->unsignedInteger('qty')->default(10)->after('subject_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_subject', function (Blueprint $table) {
            if (Schema::hasColumn('exam_subject', 'qty')) {
                $table->dropColumn('qty');
            }
        });
    }
};
