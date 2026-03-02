<?php
namespace App\Services;
use App\Models\Colocation;
use Illuminate\Support\Facades\Auth;
class ColocationService
{

 public function create(array $data): Colocation
{
    $data['owner_id'] = Auth::id();
    $data['status']   = 'active';

    $colocation = Colocation::create($data);

    $colocation->members()->attach(Auth::id(), [
        'role'      => 'owner',
        'joined_at' => now(),
    ]);

    return $colocation;
}

    public function update(Colocation $colocation, array $data ):bool{
        return $colocation->update($data); 
    }

    public function cancel(Colocation $colocation){
        return $colocation->update([
            'status' => 'cancelled',
        ]);
    }

    public function destroy(Colocation $colocation){
        return $colocation->delete();
    }

}