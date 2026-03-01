<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Colocation extends Model
{
        
    protected $fillable = [
        'name',
        'owner_id',
        'status',
        'description',
    ];

    public function generateInvitationToken(){
        return Str::random(32);
    }

    public function createInvitation(string $email): Invitation{
        return $this->invitations()->create([
            'token' => $this->generateInvitationToken(),
            'email' => $email,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);    
        }

    public function calculateBalances()
    {
        $activeMembers = $this->activeMembers()->get();

        if ($activeMembers->isEmpty()) {
            return collect(); 
        }

        $paidByUser = $this->expenses()
            ->selectRaw('payer_id, SUM(amount) as total_paid')
            ->groupBy('payer_id')
            ->pluck('total_paid', 'payer_id');

        $totalExpenses = $this->expenses()->sum('amount');
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
                    'to_user_id'   => $creditor->id,
                    'amount'       => round($amount, 2),
                ]);

                
                $balances[$debtor->id] += $amount;
                $balances[$creditor->id] -= $amount;
            }
        }

        return $settlements;
    }

    public function saveCalculatedSettlements(){
        $settlements = $this->calculateBalances(); 
        $this->settlements()->whereNull('paid_at')->delete();
        foreach($settlements as $settlement){
            $this->settlements()->whereNull('paid_at')->create([
                'from_user_id' => $settlement['from_user_id'],
                'to_user_id' => $settlement['to_user_id'],
                'amount' => $settlement['amount'],
                'paid_at' =>null,
            ]);
        }
    }

    public function owner(){
        return $this->belongsTo(User::class , 'owner_id');
    }

    public function members(){
        return $this->belongsToMany(User::class , 'memberships')->withPivot('role', 'joined_at' , 'left_at');
    }

    public function activeMembers()
{
    return $this->members()
        ->whereNull('left_at')
        ->where('memberships.role', 'member') 
        ->orWhere('memberships.user_id', $this->owner_id); 
}

    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    public function categories(){
        return $this->hasMany(Category::class);
    }

    public function settlements(){
        return $this->hasMany(Settlement::class);
    }

    public function invitations(){
        return $this->hasMany(Invitation::class);
    }

    public function getActiveMembersCount(): int
    {
        return $this->activeMembers()->count();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function memberships(){
        return $this->hasMany(Membership::class);
    }


    }
