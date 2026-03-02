<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    protected $membershipService;

    public function __construct(MembershipService $membershipService)
    {
        $this->membershipService = $membershipService;

    }

    
    public function leave(Colocation $colocation)
    {
        $membership = $colocation->memberships()
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->first();

        if (!$membership) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas membre actif.');
        }

        $this->membershipService->leave($membership);

        return redirect()->route('colocations.index')
            ->with('success', 'Vous avez quitté la colocation.');
    }

    
    public function remove(Colocation $colocation, $userId)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $membership = $colocation->memberships()
            ->where('user_id', $userId)
            ->whereNull('left_at')
            ->first();
            
            if (!$membership) {
                return redirect()->back()->with('error', 'Membre non trouvé ou déjà parti.');
            }
            
            $this->membershipService->leave($membership);
            
            return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Membre retiré.');
            
    }
}