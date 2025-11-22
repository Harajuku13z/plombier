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
        Schema::table('submissions', function (Blueprint $table) {
            // Colonnes pour les urgences
            if (!Schema::hasColumn('submissions', 'name')) {
                $table->string('name')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('submissions', 'work_type')) {
                $table->string('work_type')->nullable()->after('work_types');
            }
            if (!Schema::hasColumn('submissions', 'emergency_type')) {
                $table->string('emergency_type')->nullable()->after('work_type');
            }
            if (!Schema::hasColumn('submissions', 'address')) {
                $table->text('address')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('submissions', 'message')) {
                $table->text('message')->nullable()->after('form_data');
            }
            if (!Schema::hasColumn('submissions', 'is_emergency')) {
                $table->boolean('is_emergency')->default(false)->after('message');
            }
            if (!Schema::hasColumn('submissions', 'urgency_level')) {
                $table->string('urgency_level')->nullable()->after('is_emergency');
            }
            if (!Schema::hasColumn('submissions', 'photos')) {
                $table->json('photos')->nullable()->after('urgency_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'photos')) {
                $table->dropColumn('photos');
            }
            if (Schema::hasColumn('submissions', 'urgency_level')) {
                $table->dropColumn('urgency_level');
            }
            if (Schema::hasColumn('submissions', 'is_emergency')) {
                $table->dropColumn('is_emergency');
            }
            if (Schema::hasColumn('submissions', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('submissions', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('submissions', 'emergency_type')) {
                $table->dropColumn('emergency_type');
            }
            if (Schema::hasColumn('submissions', 'work_type')) {
                $table->dropColumn('work_type');
            }
            if (Schema::hasColumn('submissions', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
