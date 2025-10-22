<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 소셜 로그인 컬럼 추가
            $table->string('provider')->nullable()->after('id'); // kakao, google, naver
            $table->string('provider_id')->nullable()->after('provider'); // 소셜 서비스 ID
            $table->string('profile_image')->nullable()->after('email'); // 프로필 사진

            // password를 nullable로 변경 (소셜 로그인은 비밀번호 없음)
            $table->string('password')->nullable()->change();

            // 소셜 ID 조합으로 unique 인덱스
            $table->unique(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['provider', 'provider_id']);
            $table->dropColumn(['provider', 'provider_id', 'profile_image']);
        });
    }
};
