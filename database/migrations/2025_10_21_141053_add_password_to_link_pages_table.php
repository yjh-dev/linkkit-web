<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            $table->string('password')->after('uuid');
        });
    }

    public function down(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};
