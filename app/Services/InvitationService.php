<?php
namespace App\Services;
use App\Models\Invitation;
use App\Models\Colocation;
use App\Models\User;
use Illuminate\Support\Str;

class InvitationService
{
    public function create(Colocation $coloc, string $email): Invitation
    {
        return $coloc->invitations()->create([
            'token' => Str::random(32),
            'email' => $email,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function accept(Invitation $inv, User $user): bool
    {
        if ($inv->status !== 'pending' || $inv->email !== $user->email || $user->hasActiveColocation()) {
            return false;
        }

        $inv->update(['status' => 'accepted', 'expires_at' => now()]);
        $inv->colocation->memberships()->create([
            'user_id' => $user->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);
        return true;
    }

    public function refuse(Invitation $inv): void
    {
        $inv->update(['status' => 'refused', 'expires_at' => now()]);
    }
}