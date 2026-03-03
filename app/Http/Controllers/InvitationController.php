<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ColocationInvitation;

class InvitationController extends Controller
{
    protected $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function store(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'email' => 'required|email'
        ]);

        $invitation = $this->invitationService->create($colocation, $request->email);

        // décommente quand le mail sera prêt
        // Mail::to($request->email)->queue(new ColocationInvitation($invitation));

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Invitation envoyée à ' . $request->email);
    }

    public function show($token)
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        return view('invitations.show', compact('invitation'));
    }

    public function accept(Invitation $invitation)
    {
        $user = Auth::user();

        if ($this->invitationService->accept($invitation, $user)) {
            return redirect()->route('colocations.show', $invitation->colocation)
                ->with('success', 'Vous avez rejoint la colocation !');
        }

        return redirect()->route('dashboard')
            ->with('error', 'Impossible d\'accepter cette invitation.');
    }

    public function refuse(Invitation $invitation)
    {
        $this->invitationService->refuse($invitation);

        return redirect()->route('dashboard')
            ->with('info', 'Invitation refusée.');
    }
}