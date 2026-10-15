<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->redirectGuestsTo('/login');
    })

    ->withExceptions(function (Exceptions $exceptions) {

        /*
        |--------------------------------------------------------------------------
        | Strategi Penanganan Error (bebas halaman error untuk request web)
        |--------------------------------------------------------------------------
        | Request WEB  -> redirect kembali + flash error (tidak menampilkan
        |                 halaman 403/404/500 bawaan Laravel).
        | Request JSON -> tetap mengembalikan status code + JSON, karena
        |                 JavaScript halaman pengerjaan (attempt/simulasi)
        |                 membaca response.ok / data.success untuk notifikasi.
        */

        $fallbackUrl = fn () => Auth::check() ? route('dashboard') : route('login');

        // 419: sesi/CSRF kadaluarsa -> paksa login ulang.
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Session habis, silakan login kembali.',
                ], 419);
            }

            return redirect('/login')->with('error', 'Session habis, silakan login kembali.');
        });

        // 403 Spatie (middleware permission/role).
        $exceptions->render(function (UnauthorizedException $e, $request) use ($fallbackUrl) {
            if (! Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return $request->expectsJson()
                    ? response()->json(['success' => false, 'error' => 'Session habis, silakan login kembali.'], 401)
                    : redirect('/login')->with('error', 'Session habis, silakan login kembali.');
            }

            $message = 'Anda tidak memiliki akses.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'error' => $message], 403)
                : redirect()->back(fallback: $fallbackUrl())->with('error', $message);
        });

        // 404: route/data tidak ditemukan (termasuk findOrFail & firstOrFail).
        // Pesan kustom dari abort(404, '...') diteruskan; pesan teknis
        // ModelNotFoundException ("No query results...") diganti generik.
        $exceptions->render(function (NotFoundHttpException $e, $request) use ($fallbackUrl) {
            $message = $e->getMessage();

            if ($message === '' || str_contains($message, 'No query results')) {
                $message = 'Data tidak ditemukan.';
            }

            return $request->expectsJson()
                ? response()->json(['success' => false, 'error' => $message], 404)
                : redirect()->back(fallback: $fallbackUrl())->with('error', $message);
        });

        // 403 dari abort(403)/abort_unless(..., 403) di controller.
        $exceptions->render(function (AccessDeniedHttpException $e, $request) use ($fallbackUrl) {
            $message = $e->getMessage() ?: 'Anda tidak memiliki akses.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'error' => $message], 403)
                : redirect()->back(fallback: $fallbackUrl())->with('error', $message);
        });

        // Sisa HttpException lain (405, 429, dst) -> pola yang sama.
        $exceptions->render(function (HttpExceptionInterface $e, $request) use ($fallbackUrl) {
            $status = $e->getStatusCode();

            if ($status < 400) {
                return null;
            }

            $message = $e->getMessage() ?: match (true) {
                $status === 429 => 'Terlalu banyak permintaan. Coba lagi nanti.',
                default => 'Permintaan tidak dapat diproses.',
            };

            return $request->expectsJson()
                ? response()->json(['success' => false, 'error' => $message], $status)
                : redirect()->back(fallback: $fallbackUrl())->with('error', $message);
        });

        // 500 tak terduga: redirect kembali bila ada referer aman (hindari loop).
        // Tanpa referer, halaman error default ditampilkan sebagai upaya terakhir.
        $exceptions->render(function (Throwable $e, $request) {
            // Exception dengan alur sendiri -> biarkan framework menangani.
            if ($e instanceof ValidationException
                || $e instanceof AuthenticationException
                || $e instanceof HttpResponseException
                || $e instanceof HttpExceptionInterface
                || $e instanceof UnauthorizedException) {
                return null;
            }

            $message = config('app.debug')
                ? 'Terjadi kesalahan: ' . Str::limit($e->getMessage(), 180)
                : 'Terjadi kesalahan. Silakan coba lagi.';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => $message], 500);
            }

            $referer = $request->headers->get('referer');

            if ($referer && $referer !== $request->fullUrl()) {
                return redirect()->to($referer)->with('error', $message);
            }

            return null;
        });
    })

    ->create();
