<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;

class MembershipService
{
    public function hasActiveMembership(?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        return $user->colocations()
            ->wherePivotNull('left_at')
            ->exists();
    }

    public function getActiveMembership(?User $user = null)
    {
        $user = $user ?? Auth::user();

        return $user->colocations()
            ->wherePivotNull('left_at')
            ->first();
    }

    public function leave(Membership $membership): bool
{
    return $membership->update([
        'left_at' => now(),
    ]);
}
}