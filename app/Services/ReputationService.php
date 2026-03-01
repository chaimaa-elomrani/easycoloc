<?php

namespace App\Services;

use App\Models\Membership;
use App\Services\BalanceService;

class ReputationService
{

   public function applyOnLeave(Membership $membership): void
    {
        $user = $membership->user;
        $coloc = $membership->colocation;

        $balanceService = new BalanceService();
        $settlements = $balanceService->calculateForColocation($coloc);

        $hasDebt = $settlements->contains('from_user_id', $user->id);

        $user->reputation += $hasDebt ? -1 : 1;
        $user->save();

        $membership->left_at = now();
        $membership->save();
    }
}