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
            // SEO 설정
            $table->string('meta_title', 100)->nullable()->after('bio')->comment('메타 제목');
            $table->text('meta_description')->nullable()->after('meta_title')->comment('메타 설명');
            $table->string('meta_keywords', 255)->nullable()->after('meta_description')->comment('메타 키워드');
            $table->string('og_image', 500)->nullable()->after('meta_keywords')->comment('OG 이미지 URL');

            // 커스텀 도메인
            $table->string('custom_domain', 100)->nullable()->unique()->after('og_image')->comment('커스텀 도메인');
            $table->boolean('domain_verified')->default(false)->after('custom_domain')->comment('도메인 인증 여부');

            // QR 코드 커스터마이징
            $table->string('qr_logo', 500)->nullable()->after('domain_verified')->comment('QR 코드 로고');
            $table->string('qr_color', 7)->default('#000000')->after('qr_logo')->comment('QR 코드 색상');
            $table->string('qr_bg_color', 7)->default('#FFFFFF')->after('qr_color')->comment('QR 코드 배경색');
            $table->enum('qr_style', ['square', 'dots', 'rounded'])->default('square')->after('qr_bg_color')->comment('QR 코드 스타일');

            // 공개 설정
            $table->boolean('is_public')->default(true)->after('qr_style')->comment('공개 여부');
            $table->boolean('searchable')->default(true)->after('is_public')->comment('검색 노출 여부');
            $table->boolean('show_branding')->default(true)->after('searchable')->comment('LinkKit 브랜딩 표시');

            // 성인 콘텐츠 경고
            $table->boolean('adult_content')->default(false)->after('show_branding')->comment('성인 콘텐츠 여부');
        });

        // 페이지 백업/버전 관리
        Schema::create('page_backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->json('data')->comment('페이지 전체 데이터 스냅샷');
            $table->string('note', 255)->nullable()->comment('백업 메모');
            $table->timestamps();

            $table->index('link_page_id');
        });

        // 슬러그/별칭 (프리미엄)
        Schema::create('page_slugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('slug', 50)->unique()->comment('짧은 URL 별칭');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();

            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_slugs');
        Schema::dropIfExists('page_backups');

        Schema::table('link_pages', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'meta_keywords',
                'og_image',
                'custom_domain',
                'domain_verified',
                'qr_logo',
                'qr_color',
                'qr_bg_color',
                'qr_style',
                'is_public',
                'searchable',
                'show_branding',
                'adult_content'
            ]);
        });
    }
};
