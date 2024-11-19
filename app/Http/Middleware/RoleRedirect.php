<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $panel = filament()->getCurrentPanel()->getId();


        // if ($user->role === 'admin') {
        //     return $next($request);
        // }

        // If the user is not authenticated, redirect to login
        // if (!$user) {
        //     return redirect('/login');
        // }


        switch ($user->role) {
            case 'admin':
                if ($panel !== 'admin') {
                    return redirect()->route('filament.admin.pages.dashboard'); // Manager Dashboard
                }
                break;
            case 'company':
                if ($panel !== 'company') {
                    return redirect()->route('filament.company.pages.dashboard'); // FrontDesk Dashboard
                }
                break;
            case 'household':
                if ($panel !== 'household') {
                    return redirect()->route('filament.household.pages.dashboard'); // Housekeeper Dashboard
                }
                break;
         
            default:
                return redirect('/login'); 
        }
        return $next($request);

    }
}
