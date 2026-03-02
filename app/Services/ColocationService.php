<?php
namespace App\Services;
use App\Models\Colocation;
use Illuminate\Support\Facades\Auth;
class ColocationService
{

    public function create(array $data): Colocation{
        $data['owner_id'] = Auth::id();
        $data['status'] = 'active'; 

        return Colocation::create($data);
    }


    public function update(Colocation $colocation, array $data ){
        return $colocation->update($data); 
    }

    public function cancel(Colocation $colocation){
        return $colocation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function destroy(Colocation $colocation){
        return $colocation->delete();
    }

}