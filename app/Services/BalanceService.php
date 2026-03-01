<?php

namespace App\Services;

use App\Models\Colocation;
use Illuminate\Support\Collection;

class BalanceService
{
    public function calculateForColocation(Colocation $colocation): Collection
    {
        $activeMembers = $colocation->activeMembers()->get();

        if ($activeMembers->isEmpty()) {
            return collect();
        }

        $paidByUser = $colocation->expenses()
            ->selectRaw('payer_id, SUM(amount) as total_paid')
            ->groupBy('payer_id')
            ->pluck('total_paid', 'payer_id');

        $totalExpenses = $colocation->expenses()->sum('amount');
        $memberCount = $activeMembers->count();
        $sharePerMember = $memberCount > 0 ? $totalExpenses / $memberCount : 0;

        $settlements = collect();
        $balances = [];

        foreach ($activeMembers as $member) {
            $paid = $paidByUser->get($member->id, 0);
            $balances[$member->id] = $paid - $sharePerMember;
        }

        foreach ($activeMembers as $debtor) {
            $debtorBalance = $balances[$debtor->id];
            if ($debtorBalance >= 0) continue;

            foreach ($activeMembers as $creditor) {
                if ($creditor->id === $debtor->id) continue;

                $creditorBalance = $balances[$creditor->id];
                if ($creditorBalance <= 0) continue;

                $amount = min(abs($debtorBalance), $creditorBalance);

                $settlements->push([
                    'from_user_id' => $debtor->id,
                    'to_user_id' => $creditor->id,
                    'amount' => round($amount, 2),
                ]);

                $balances[$debtor->id] += $amount;
                $balances[$creditor->id] -= $amount;
            }
        }

        return $settlements;
    }


    public function saveSettlementsForColocation(Colocation $colocation): void
{
    $settlements = $this->calculateForColocation($colocation);

    // Supprime anciens non payés (optionnel, selon besoin)
    $colocation->settlements()->whereNull('paid_at')->delete();

    foreach ($settlements as $settlement) {
        $colocation->settlements()->create([
            'from_user_id' => $settlement['from_user_id'],
            'to_user_id'   => $settlement['to_user_id'],
            'amount'       => $settlement['amount'],
            'paid_at'      => null,
        ]);
    }
}

public function markSettlementAsPaid(int $settlementId): bool
{
    $settlement = \App\Models\Settlement::find($settlementId);

    if (!$settlement || $settlement->paid_at !== null) {
        return false;
    }

    $settlement->paid_at = now();
    $settlement->save();

    return true;
}
}