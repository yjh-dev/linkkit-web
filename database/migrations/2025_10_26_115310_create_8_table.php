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
        // AI 제안 기록
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('link_page_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['color', 'layout', 'bio', 'link_title'])->comment('제안 타입');
            $table->json('input_data')->nullable()->comment('입력 데이터 (이미지 URL 등)');
            $table->json('suggestions')->comment('제안된 값들');
            $table->string('selected', 255)->nullable()->comment('선택된 값');
            $table->timestamps();

            $table->index('user_id');
            $table->index('link_page_id');
        });

        // 이미지 최적화 기록
        Schema::create('image_optimizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('original_url', 500)->comment('원본 URL');
            $table->string('optimized_url', 500)->comment('최적화된 URL');
            $table->bigInteger('original_size')->comment('원본 크기 (bytes)');
            $table->bigInteger('optimized_size')->comment('최적화 크기 (bytes)');
            $table->integer('width')->comment('너비');
            $table->integer('height')->comment('높이');
            $table->string('format', 10)->comment('포맷 (webp, jpg 등)');
            $table->timestamps();

            $table->index('link_page_id');
        });

        // 자동화 작업
        Schema::create('automations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('name', 100)->comment('자동화 이름');
            $table->enum('trigger', ['schedule', 'webhook', 'form_submit', 'click_count'])->comment('트리거');
            $table->json('trigger_config')->comment('트리거 설정');
            $table->enum('action', ['email_notify', 'update_link', 'export_data', 'webhook_call'])->comment('액션');
            $table->json('action_config')->comment('액션 설정');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->integer('execution_count')->default(0)->comment('실행 횟수');
            $table->datetime('last_executed_at')->nullable()->comment('마지막 실행 시간');
            $table->timestamps();

            $table->index('link_page_id');
        });

        // 자동화 실행 로그
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['success', 'failed', 'pending'])->comment('상태');
            $table->json('trigger_data')->nullable()->comment('트리거 데이터');
            $table->json('action_result')->nullable()->comment('액션 결과');
            $table->text('error_message')->nullable()->comment('에러 메시지');
            $table->timestamp('executed_at')->useCurrent()->comment('실행 시간');

            $table->index('automation_id');
        });

        // 스마트 링크 (URL 단축 + 추적)
        Schema::create('smart_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('short_code', 20)->unique()->comment('짧은 코드');
            $table->string('original_url', 1000)->comment('원본 URL');
            $table->string('title', 200)->nullable()->comment('제목');
            $table->json('utm_params')->nullable()->comment('UTM 파라미터');
            $table->integer('clicks')->default(0)->comment('클릭 수');
            $table->datetime('expires_at')->nullable()->comment('만료 시간');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();

            $table->index('short_code');
            $table->index('user_id');
        });

        // 템플릿 (사용자가 만든 페이지를 템플릿으로 공유)
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('name', 100)->comment('템플릿 이름');
            $table->text('description')->nullable()->comment('설명');
            $table->string('thumbnail', 500)->nullable()->comment('썸네일');
            $table->json('preview_data')->comment('미리보기 데이터');
            $table->string('category', 50)->nullable()->comment('카테고리');
            $table->boolean('is_public')->default(false)->comment('공개 여부');
            $table->integer('use_count')->default(0)->comment('사용 횟수');
            $table->decimal('rating', 3, 2)->default(0)->comment('평점');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['is_public', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
        Schema::dropIfExists('smart_links');
        Schema::dropIfExists('automation_logs');
        Schema::dropIfExists('automations');
        Schema::dropIfExists('image_optimizations');
        Schema::dropIfExists('ai_suggestions');
    }
};
