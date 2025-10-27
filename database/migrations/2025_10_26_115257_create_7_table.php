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
        Schema::table('users', function (Blueprint $table) {
            // 프리미엄 플랜
            $table->enum('plan', ['free', 'basic', 'pro', 'enterprise'])->default('free')->after('profile_image')->comment('플랜');
            $table->datetime('plan_expires_at')->nullable()->after('plan')->comment('플랜 만료일');
            $table->boolean('is_lifetime')->default(false)->after('plan_expires_at')->comment('평생 플랜 여부');

            // 제한
            $table->integer('max_pages')->default(1)->after('is_lifetime')->comment('최대 페이지 수');
            $table->integer('max_links_per_page')->default(10)->after('max_pages')->comment('페이지당 최대 링크 수');
        });

        // 구독 내역
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('plan', ['basic', 'pro', 'enterprise'])->comment('플랜');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->comment('결제 주기');
            $table->decimal('amount', 10, 2)->comment('결제 금액');
            $table->string('currency', 3)->default('KRW')->comment('통화');
            $table->enum('status', ['active', 'cancelled', 'expired', 'paused'])->default('active')->comment('상태');
            $table->datetime('started_at')->comment('시작일');
            $table->datetime('expires_at')->comment('만료일');
            $table->datetime('cancelled_at')->nullable()->comment('취소일');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'status']);
        });

        // 결제 내역
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('payment_method', 50)->comment('tosspayments/kakaopay/card');
            $table->string('transaction_id', 100)->unique()->comment('결제 고유 ID');
            $table->decimal('amount', 10, 2)->comment('결제 금액');
            $table->string('currency', 3)->default('KRW')->comment('통화');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending')->comment('결제 상태');
            $table->text('description')->nullable()->comment('결제 설명');
            $table->json('metadata')->nullable()->comment('추가 정보');
            $table->datetime('paid_at')->nullable()->comment('결제 완료 시간');
            $table->datetime('refunded_at')->nullable()->comment('환불 시간');
            $table->timestamps();

            $table->index('user_id');
            $table->index('transaction_id');
        });

        // 쿠폰 (할인)
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('프로모션 코드');
            $table->text('description')->nullable()->comment('설명');
            $table->integer('discount_percent')->nullable()->comment('할인율 (%)');
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('할인 금액');
            $table->integer('max_uses')->nullable()->comment('최대 사용 횟수');
            $table->integer('used_count')->default(0)->comment('사용된 횟수');
            $table->datetime('valid_from')->nullable()->comment('유효 시작일');
            $table->datetime('valid_until')->nullable()->comment('유효 종료일');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->timestamps();

            $table->index('code');
        });

        // 프로모션 코드 사용 내역
        Schema::create('promo_code_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index('promo_code_id');
            $table->index('user_id');
        });

        // 추천인 프로그램
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->comment('추천인 user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_id')->comment('추천받은 user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'rewarded'])->default('pending')->comment('상태');
            $table->decimal('reward_amount', 10, 2)->nullable()->comment('리워드 금액');
            $table->datetime('rewarded_at')->nullable()->comment('리워드 지급 시간');
            $table->timestamps();

            $table->index('referrer_id');
            $table->index('referred_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('promo_code_usages');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'plan',
                'plan_expires_at',
                'is_lifetime',
                'max_pages',
                'max_links_per_page'
            ]);
        });
    }
};
