<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('password');
            }
        });

        // If there are existing rows, copy `name` into `username` when username is null.
        DB::table('users')->whereNull('username')->update(['username' => DB::raw('name')]);

        // For existing users with null role, default to 'staff' (adjust if you prefer a different default).
        DB::table('users')->whereNull('role')->update(['role' => 'staff']);
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
