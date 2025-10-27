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
        // 이메일 수집 폼
        Schema::create('lead_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('title', 100)->comment('폼 제목');
            $table->text('description')->nullable()->comment('폼 설명');
            $table->enum('type', ['email', 'survey', 'contact'])->default('email')->comment('폼 타입');
            $table->json('fields')->nullable()->comment('필드 설정');
            $table->string('submit_button_text', 50)->default('제출하기')->comment('제출 버튼 텍스트');
            $table->text('success_message')->nullable()->comment('제출 성공 메시지');
            $table->string('redirect_url', 500)->nullable()->comment('제출 후 리다이렉트 URL');
            $table->integer('order')->default(0)->comment('표시 순서');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();

            $table->index('link_page_id');
        });

        // 폼 제출 데이터
        Schema::create('lead_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_form_id')->constrained()->onDelete('cascade');
            $table->json('data')->comment('제출된 데이터');
            $table->string('ip_address', 45)->nullable()->comment('IP 주소');
            $table->text('user_agent')->nullable()->comment('User Agent');
            $table->string('referrer', 500)->nullable()->comment('유입 경로');
            $table->timestamps();

            $table->index('lead_form_id');
            $table->index('created_at');
        });

        // 쿠폰 코드
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('code', 50)->comment('쿠폰 코드');
            $table->text('description')->nullable()->comment('쿠폰 설명');
            $table->integer('discount_percent')->nullable()->comment('할인율 (%)');
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('할인 금액');
            $table->datetime('valid_from')->nullable()->comment('유효 시작일');
            $table->datetime('valid_until')->nullable()->comment('유효 종료일');
            $table->integer('max_uses')->nullable()->comment('최대 사용 횟수');
            $table->integer('used_count')->default(0)->comment('사용된 횟수');
            $table->integer('order')->default(0)->comment('표시 순서');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();

            $table->index('link_page_id');
            $table->index('code');
        });

        // 타이머/카운트다운
        Schema::create('countdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained()->onDelete('cascade');
            $table->string('title', 100)->comment('타이머 제목');
            $table->datetime('target_date')->comment('목표 날짜');
            $table->enum('style', ['minimal', 'card', 'banner'])->default('card')->comment('스타일');
            $table->string('color', 7)->nullable()->comment('색상');
            $table->integer('order')->default(0)->comment('표시 순서');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();

            $table->index('link_page_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countdowns');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('lead_submissions');
        Schema::dropIfExists('lead_forms');
    }
};
