<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // RenderのTLS終端プロキシから渡されるHTTPS情報を認識する。
        // ローカル開発のリクエストは従来どおり扱う。
        if (getenv('RENDER') === 'true') {
            $middleware->trustProxies(
                at: '*',
                headers: Request::HEADER_X_FORWARDED_FOR
                    | Request::HEADER_X_FORWARDED_HOST
                    | Request::HEADER_X_FORWARDED_PORT
                    | Request::HEADER_X_FORWARDED_PROTO,
            );
        }

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // 未認証時（ゲスト時）のリダイレクト先を登録画面に設定
        $middleware->redirectTo(
            guests: '/register', // または route('register')
            users: '/dashboard'  // ログイン済みユーザーがゲスト専用ページ（/login 等）にアクセスした時のリダイレクト先
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
