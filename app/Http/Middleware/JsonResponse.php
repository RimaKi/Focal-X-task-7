<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonResponse
{
    /**
     *  Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $result = $next($request);
        if ($result->exception == null) {
            $data = $result->getOriginalContent();
            $message = "trans.method." . $request->getMethod() . ".success";
            $success = true;
            $status = 200;
            if (gettype($result->getOriginalContent()) == 'array') {
                if (array_key_exists('message', $data)) {
                    $message = $data['message'];
                }
                if (array_key_exists("success", $data)) {
                    $success = $data["success"];
                }
                if (array_key_exists("status", $data)) {
                    $status = $data["status"];
                }
                $data = collect($result->getOriginalContent())->except(['message', 'success', 'status']);
            } elseif (gettype($result->getOriginalContent()) == 'string') {
                $message = $result->getOriginalContent();
                $data = null;
            }
            $result = response()->json([
                "success" => $success,
                "data" => $data,
                "message" => $message
            ], $status);
        }

        // Remove Finger Print Headers
        $result->headers->remove('X-Powered-By');
        $result->headers->remove('Server');
        $result->headers->remove('x-turbo-charged-by');

        // Add Security Headers
        $result->headers->set('X-Frame-Options', 'deny');
        $result->headers->set('X-Content-Type-Options', 'nosniff');
        $result->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $result->headers->set('Referrer-Policy', 'no-referrer');
        $result->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $result->headers->set('Content-Security-Policy', "default-src 'none'; style-src 'self'; form-action 'self'");
        $result->headers->set('X-XSS-Protection', '1; mode=block');

        if (config('app.env') === 'production') {
            $result->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $result;
    }
}
