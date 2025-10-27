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
        Schema::table('links', function (Blueprint $table) {
            // 버튼 색상 필드 추가
            $table->string('button_bg_color', 7)->default('#FFFFFF')->after('hover_effect')->comment('버튼 배경색');
            $table->string('button_text_color', 7)->default('#1F2937')->after('button_bg_color')->comment('버튼 텍스트 색상');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['button_bg_color', 'button_text_color']);
        });
    }
};
