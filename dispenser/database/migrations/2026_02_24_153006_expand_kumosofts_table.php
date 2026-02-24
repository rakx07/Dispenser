<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kumosofts', function (Blueprint $table) {
            // New structure (safe additions)
            if (!Schema::hasColumn('kumosofts', 'student_id')) {
                $table->unsignedBigInteger('student_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('kumosofts', 'eis_school_id')) {
                $table->string('eis_school_id', 255)->nullable()->after('student_id');
            }

            if (!Schema::hasColumn('kumosofts', 'kumosoft_school_id')) {
                $table->string('kumosoft_school_id', 255)->nullable()->after('eis_school_id');
            }

            if (!Schema::hasColumn('kumosofts', 'lastname')) {
                $table->string('lastname', 255)->nullable()->after('kumosoft_school_id');
            }
            if (!Schema::hasColumn('kumosofts', 'firstname')) {
                $table->string('firstname', 255)->nullable()->after('lastname');
            }
            if (!Schema::hasColumn('kumosofts', 'middlename')) {
                $table->string('middlename', 255)->nullable()->after('firstname');
            }
            if (!Schema::hasColumn('kumosofts', 'suffix')) {
                $table->string('suffix', 255)->nullable()->after('middlename');
            }

            if (!Schema::hasColumn('kumosofts', 'email')) {
                $table->string('email', 255)->nullable()->after('suffix');
            }
            if (!Schema::hasColumn('kumosofts', 'username')) {
                $table->string('username', 255)->nullable()->after('email');
            }
            if (!Schema::hasColumn('kumosofts', 'password')) {
                // You requested as-is (plain). If you later want encrypted, we’ll change in Batch 2.
                $table->string('password', 255)->nullable()->after('username');
            }

            if (!Schema::hasColumn('kumosofts', 'match_status')) {
                $table->string('match_status', 50)->nullable()->after('password');
            }
            if (!Schema::hasColumn('kumosofts', 'match_reason')) {
                $table->string('match_reason', 255)->nullable()->after('match_status');
            }
            if (!Schema::hasColumn('kumosofts', 'matched_at')) {
                $table->timestamp('matched_at')->nullable()->after('match_reason');
            }

            // Indexes
            // We DO NOT touch your existing UNIQUE school_id right now.
            // We'll add a NEW unique on eis_school_id after we backfill it.

            $table->index('kumosoft_school_id', 'kumosofts_kumosoft_school_id_idx');
            $table->index('student_id', 'kumosofts_student_id_idx');
        });

        /**
         * Backfill (safe):
         * If you already have live rows where `kumosofts.school_id` contains the "student ID",
         * we copy it to eis_school_id and kumosoft_school_id initially.
         */
        if (Schema::hasColumn('kumosofts', 'school_id')) {
            DB::table('kumosofts')
                ->whereNull('eis_school_id')
                ->update([
                    'eis_school_id'       => DB::raw('school_id'),
                    'kumosoft_school_id'  => DB::raw('school_id'),
                ]);
        }

        /**
         * Now enforce "1 record per student" via UNIQUE eis_school_id.
         * This will succeed as long as eis_school_id has no duplicates.
         */
        Schema::table('kumosofts', function (Blueprint $table) {
            // Guard: only add if not exists. MySQL doesn't have a native "if not exists" for indexes,
            // so we rely on consistent deployment (run migration once).
            $table->unique('eis_school_id', 'kumosofts_eis_school_id_unique');
        });

        /**
         * Foreign key (optional but you said YES):
         * Add FK if your students table uses bigIncrements.
         */
        Schema::table('kumosofts', function (Blueprint $table) {
            // If you prefer no FK constraints in production, tell me and I’ll remove this.
            $table->foreign('student_id')
                ->references('id')->on('students')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('kumosofts', function (Blueprint $table) {
            // Drop FK first
            if (Schema::hasColumn('kumosofts', 'student_id')) {
                try { $table->dropForeign(['student_id']); } catch (\Throwable $e) {}
            }

            // Drop unique/indexes
            try { $table->dropUnique('kumosofts_eis_school_id_unique'); } catch (\Throwable $e) {}
            try { $table->dropIndex('kumosofts_kumosoft_school_id_idx'); } catch (\Throwable $e) {}
            try { $table->dropIndex('kumosofts_student_id_idx'); } catch (\Throwable $e) {}

            // Drop columns
            $cols = [
                'student_id','eis_school_id','kumosoft_school_id',
                'lastname','firstname','middlename','suffix',
                'email','username','password',
                'match_status','match_reason','matched_at',
            ];

            foreach ($cols as $c) {
                if (Schema::hasColumn('kumosofts', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};