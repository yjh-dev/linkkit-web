<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            // user_id 추가 (nullable - 비회원도 만들 수 있으니까)
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');

            // password도 nullable로 변경 (회원은 비밀번호 없이도 가능)
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
