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
            // 링크 타입
            $table->string('type', 20)->default('button')->after('url')
                ->comment('button/product/image_card/embed/icon/text/contact/file');

            // 이미지/썸네일
            $table->string('thumbnail', 500)->nullable()->after('type')
                ->comment('상품 이미지, 썸네일 등');

            // 상품 관련 필드
            $table->decimal('price', 10, 2)->nullable()->after('thumbnail')
                ->comment('상품 가격');
            $table->decimal('sale_price', 10, 2)->nullable()->after('price')
                ->comment('할인 가격');
            $table->string('currency', 3)->default('KRW')->after('sale_price')
                ->comment('통화 단위');
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'pre_order'])->default('in_stock')->after('currency')
                ->comment('재고 상태');

            // 설명 (상품 설명, 카드 설명 등)
            $table->text('description')->nullable()->after('stock_status')
                ->comment('링크 설명');

            // 임베드 관련
            $table->string('embed_type', 20)->nullable()->after('description')
                ->comment('youtube/spotify/instagram');
            $table->string('embed_id', 200)->nullable()->after('embed_type')
                ->comment('임베드 ID');

            // 아이콘
            $table->string('icon', 50)->nullable()->after('embed_id')
                ->comment('아이콘 이름 (lucide, emoji 등)');

            // 파일 다운로드
            $table->string('file_path', 500)->nullable()->after('icon')
                ->comment('다운로드 파일 경로');
            $table->bigInteger('file_size')->nullable()->after('file_path')
                ->comment('파일 크기 (bytes)');

            // 특수 기능
            $table->boolean('password_protected')->default(false)->after('file_size')
                ->comment('비밀번호 보호 여부');
            $table->string('password', 255)->nullable()->after('password_protected')
                ->comment('링크 비밀번호');

            $table->datetime('scheduled_at')->nullable()->after('password')
                ->comment('예약 발행 일시');
            $table->datetime('expired_at')->nullable()->after('scheduled_at')
                ->comment('만료 일시');

            // 스타일링
            $table->string('button_style', 20)->nullable()->after('expired_at')
                ->comment('rounded/pill/sharp/soft');
            $table->string('button_size', 20)->nullable()->after('button_style')
                ->comment('small/medium/large');
            $table->string('button_color', 7)->nullable()->after('button_size')
                ->comment('개별 버튼 색상');
            $table->string('hover_effect', 20)->nullable()->after('button_color')
                ->comment('none/scale/lift/glow/wiggle/pulse');

            // 활성화 상태
            $table->boolean('is_active')->default(true)->after('hover_effect')
                ->comment('활성화 여부');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'thumbnail',
                'price',
                'sale_price',
                'currency',
                'stock_status',
                'description',
                'embed_type',
                'embed_id',
                'icon',
                'file_path',
                'file_size',
                'password_protected',
                'password',
                'scheduled_at',
                'expired_at',
                'button_style',
                'button_size',
                'button_color',
                'hover_effect',
                'is_active'
            ]);
        });
    }
};
