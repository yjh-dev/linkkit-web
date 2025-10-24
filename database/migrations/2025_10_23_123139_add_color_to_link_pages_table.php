<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            // 메인 컬러 추가 (HEX 코드 저장: #2B7FFF)
            $table->string('color', 7)->default('#2B7FFF')->after('preset');
        });
    }

    public function down(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
