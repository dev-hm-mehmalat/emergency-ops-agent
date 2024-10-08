<?php



namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            return $next($request);  // Deaktiviere die CSRF-Prüfung in der Testumgebung
        }

        // CSRF-Token-Überprüfung in anderen Umgebungen
        if ($this->tokensMatch($request)) {
            return $next($request);
        }

        abort(419, 'CSRF Token Mismatch');
    }

    protected function tokensMatch($request)
    {
        return $request->session()->token() === $request->input('_token');
    }
}
