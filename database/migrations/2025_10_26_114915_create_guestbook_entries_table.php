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
        // 방명록/댓글
        Schema::create('guestbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('name', 50)->comment('작성자 이름');
            $table->text('message')->comment('메시지');
            $table->string('avatar', 500)->nullable()->comment('아바타 URL');
            $table->string('ip_address', 45)->nullable()->comment('IP 주소');
            $table->boolean('is_approved')->default(true)->comment('승인 여부');
            $table->boolean('is_pinned')->default(false)->comment('고정 여부');
            $table->timestamps();

            $table->index('link_page_id');
            $table->index(['link_page_id', 'is_approved']);
        });

        // 익명 질문
        Schema::create('anonymous_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->text('question')->comment('질문');
            $table->text('answer')->nullable()->comment('답변');
            $table->datetime('answered_at')->nullable()->comment('답변 시간');
            $table->boolean('is_public')->default(false)->comment('공개 여부');
            $table->string('ip_address', 45)->nullable()->comment('IP 주소');
            $table->timestamps();

            $table->index('link_page_id');
            $table->index(['link_page_id', 'is_public']);
        });

        // 좋아요/리액션
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('type', 20)->comment('heart/fire/thumbsup/clap/star');
            $table->string('ip_address', 45)->nullable()->comment('IP 주소');
            $table->string('session_id', 100)->nullable()->comment('세션 ID');
            $table->timestamps();

            $table->index('link_page_id');
            $table->index(['link_page_id', 'type']);
            $table->unique(['link_page_id', 'ip_address', 'type']); // 중복 방지
        });

        // SNS 피드 임베드 설정
        Schema::create('social_feeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->enum('platform', ['instagram', 'twitter', 'youtube', 'tiktok'])->comment('플랫폼');
            $table->string('username', 100)->comment('사용자명');
            $table->string('access_token', 500)->nullable()->comment('API 액세스 토큰');
            $table->enum('display_type', ['grid', 'carousel', 'list'])->default('grid')->comment('표시 방식');
            $table->integer('post_count')->default(6)->comment('표시할 게시물 수');
            $table->integer('order')->default(0)->comment('표시 순서');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->datetime('last_synced_at')->nullable()->comment('마지막 동기화');
            $table->timestamps();

            $table->index('link_page_id');
        });

        // 방문자 카운터
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->nullable()->comment('IP 주소');
            $table->string('session_id', 100)->nullable()->comment('세션 ID');
            $table->text('user_agent')->nullable()->comment('User Agent');
            $table->string('referrer', 500)->nullable()->comment('유입 경로');
            $table->string('country', 2)->nullable()->comment('국가 코드');
            $table->string('device_type', 20)->nullable()->comment('mobile/tablet/desktop');
            $table->timestamp('viewed_at')->useCurrent()->comment('조회 시간');

            $table->index('link_page_id');
            $table->index(['link_page_id', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('social_feeds');
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('anonymous_questions');
        Schema::dropIfExists('guestbook_entries');
    }
};
