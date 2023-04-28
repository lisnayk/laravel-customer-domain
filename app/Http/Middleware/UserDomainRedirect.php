<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserDomainRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domainLevel = count(explode(".", $request->getHost()));
        $user = Auth::user();
        $protocol = $request->secure() ? "https" : "http";
        $domain = config("app.root_domain");

        if (!$user && $domainLevel == 3) {
            return Redirect::to("$protocol://" . $domain);
            //return $next($request);
        }

        if (!$user && $domainLevel == 2) {
            return $next($request);
        }

        if ($user && $domainLevel == 3) {
            return $next($request);
        }

        $subdomain = Str::slug($user->name);
        return Redirect::to("$protocol://$subdomain.$domain");
    }
}
