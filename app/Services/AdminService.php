<?php

namespace App\Services;

use App\Models\User;
use App\Models\Expense;
use App\Models\Colocation;

class AdminService {

public function getGlobalStats(): array{
    return [
        'total_users' => User::count(),
        'total_coloc' => Colocation::count(),
        'total_expenses' => Expense::sum('amount'),
        'banned_users' => User::where('is_banned', true)->count(),
    ];
}

public function banUser(int $userId){
    $user = User::find($userId);
    if(!$user)
        return false ; 
    $user->is_banned = true ; 
    $user->save();
    return true ; 
}

public function unbanUser(int $userId): bool
    {
        $user = User::find($userId);
        if (!$user) return false;

        $user->is_banned = false;
        $user->save();

        return true;
    }
}