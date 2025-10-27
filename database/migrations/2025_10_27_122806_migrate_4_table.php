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
        Schema::table('link_pages', function (Blueprint $table) {
            // 폰트 설정 필드
            $table->string('font_family', 100)->default('Pretendard')->after('animation_speed')->comment('폰트 패밀리');
            $table->string('custom_font_path')->nullable()->after('font_family')->comment('커스텀 폰트 파일 경로');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            $table->dropColumn(['font_family', 'custom_font_path']);
        });
    }
};
