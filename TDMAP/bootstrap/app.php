<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([

            // Spatie Permission
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,

            // OTP middleware
            'otp.active' => \App\Http\Middleware\CheckOtpActive::class,


    // VIP Upload middleware
    'vip.upload' => \App\Http\Middleware\CheckVipUpload::class,

        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (HttpException $e, $request) {

            // nếu bị lỗi 403 (không có quyền)
            if ($e->getStatusCode() === 403) {

                return redirect('/')
                    ->with('error','Bạn không có quyền truy cập trang này');

            }

        });

    })

    ->create();