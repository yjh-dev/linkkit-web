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
            // 배너 커스터마이징 필드
            $table->integer('banner_radius')->default(24)->after('cover_image')->comment('배너 모서리 둥글기 (0-50px)');
            $table->integer('banner_height')->default(128)->after('banner_radius')->comment('배너 높이 (80-250px)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            $table->dropColumn(['banner_radius', 'banner_height']);
        });
    }
};
