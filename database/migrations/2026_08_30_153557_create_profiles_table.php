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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // ユーザーとの紐付け（ユーザー削除時にプロフィールも削除）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 基本プロフィール
            $table->string('display_name');           // 表示名（ライバー名）
            $table->string('oshi_mark')->nullable();   // 推しマーク（絵文字等）
            $table->string('fan_name')->nullable();    // ファンネーム
            $table->text('bio')->nullable();           // 自己紹介文
            
            // 画像・デザイン
            $table->string('avatar_url')->nullable();  // 立ち絵・アイコン画像URL
            $table->string('theme_color')->default('#5A67D8'); // カードのテーマカラー
            
            // リンク・配信情報
            $table->string('iriam_url')->nullable();   // IRIAMプロフィールURL
            $table->string('twitter_id')->nullable();  // X(Twitter) ID
            $table->string('marshmallow_url')->nullable(); // マシュマロ等の質問箱URL
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
