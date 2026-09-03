<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Allow validation and authentication exceptions to be handled natively
        if ($e instanceof ValidationException || $e instanceof AuthenticationException) {
            return parent::render($request, $e);
        }

        // Generate a unique, traceable auto error code
        $errorId = 'ERR-' . strtoupper(Str::random(6));

        // Log the full diagnostic information to storage/logs/laravel.log with clear search tag
        Log::error("[AUTO_ERROR_CODE: {$errorId}] [Error ID: {$errorId}] " . $e->getMessage(), [
            'auto_error_code' => $errorId,
            'error_id' => $errorId,
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'inputs' => $request->except(['password', 'password_confirmation', '_token']),
            'trace' => $e->getTraceAsString(),
        ]);

        // If this is an AJAX / XMLHttpRequest (such as table searches or dynamic partials)
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            $errorMessage = __('حدث خطأ غير متوقع أثناء معالجة الطلب. يرجى مراجعة سجل الأخطاء برمز: :id', ['id' => $errorId]);
            $html = view()->exists('content.Debt._debtsError')
                ? view('content.Debt._debtsError', ['errorId' => $errorId])->render()
                : null;

            return response()->json([
                'status' => false,
                'error_code' => $errorId,
                'error_id' => $errorId,
                'message' => $errorMessage,
                'html' => $html,
            ], 500);
        }

        return parent::render($request, $e);
    }
}
