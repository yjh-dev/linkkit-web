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
            // 텍스트 스타일 (신규!)
            $table->string('text_color', 7)->default('#FFFFFF')->after('badges')
                ->comment('텍스트 색상');
            $table->string('text_size', 20)->default('medium')->after('text_color')
                ->comment('텍스트 크기: small/medium/large');
            $table->string('text_weight', 20)->default('bold')->after('text_size')
                ->comment('텍스트 두께: normal/medium/semibold/bold/extrabold');

            // 커버 배경색 (신규!)
            $table->string('cover_bg_color', 7)->default('#3B82F6')->after('cover_image')
                ->comment('커버 배경색 (이미지 없을 때)');

            // 배경 영상 (신규!)
            $table->string('background_video_url', 500)->nullable()->after('background_image')
                ->comment('배경 영상 URL');
            $table->string('background_video_file', 500)->nullable()->after('background_video_url')
                ->comment('배경 영상 파일 경로');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('link_pages', function (Blueprint $table) {
            $table->dropColumn([
                'text_color',
                'text_size',
                'text_weight',
                'cover_bg_color',
                'background_video_url',
                'background_video_file'
            ]);
        });
    }
};
