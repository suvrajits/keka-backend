<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations to add new fields.
     */
    public function up()
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->string('verification_code')->nullable()->after('password');
            $table->boolean('is_verified')->default(false)->after('verification_code');
            $table->boolean('is_approved')->default(false)->after('is_verified');
            $table->enum('access_level', ['super_admin', 'admin'])->default('admin')->after('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn('verification_code');
            $table->dropColumn('is_verified');
            $table->dropColumn('is_approved');
            $table->dropColumn('access_level');
        });
    }
};
