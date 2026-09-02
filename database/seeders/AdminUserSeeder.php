<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // すでに存在しない場合のみ作成（重複エラー防止）
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者アカウント',
                'password' => Hash::make('adminpassword'), // お好みの初期パスワードに変更してください
                'email_verified_at' => now(),
            ]
        );
    }
}
