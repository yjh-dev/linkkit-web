<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            // 배경 타입 (solid, gradient)
            $table->string('background_type')->default('gradient')->after('color');

            // 배경 메인 색상
            $table->string('background_color', 7)->default('#F3F4F6')->after('background_type');

            // 배경 보조 색상 (그라데이션용)
            $table->string('background_secondary_color', 7)->nullable()->after('background_color');
        });
    }

    public function down(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'background_color', 'background_secondary_color']);
        });
    }
};
