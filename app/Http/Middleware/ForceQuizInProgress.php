<?php

namespace App\Http\Middleware;

use App\Models\Applicant;
use App\Models\TestResult;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ForceQuizInProgress
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) return $next($request);

        if ($request->routeIs('quiz.*')) return $next($request);

        if ($request->is('storage/*') || $request->is('build/*') || $request->is('assets/*')
            || $request->is('css/*') || $request->is('js/*') || $request->is('images/*')) {
            return $next($request);
        }

        $applicantIds = Applicant::where('user_id', Auth::id())->pluck('id');
        if ($applicantIds->isEmpty()) return $next($request);

        $inProgress = TestResult::with('test')
            ->whereIn('applicant_id', $applicantIds)
            ->whereNull('finished_at')
            ->latest('started_at')
            ->first();

        if (!$inProgress || !$inProgress->test) return $next($request);

        $signed = URL::signedRoute('quiz.start', ['slug' => $inProgress->test->slug]);

        return redirect()->to($signed)
            ->with('status', 'Selesaikan Quiz terlebih dahulu sebelum membuka halaman lain.');
    }
}
