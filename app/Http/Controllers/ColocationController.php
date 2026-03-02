<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Services\ColocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ColocationController extends Controller
{
    protected $colocationService;

    public function __construct(ColocationService $colocationService)
    {
        $this->colocationService = $colocationService;
        
    }

    public function index(){

        $colocations = Auth::user()->colocations()->wherePivotNull("left_at")->get();
        return view('colocations.index', compact('colocations'));
    }
    

    public function create(){
        return view('colocations.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        $colocation = $this->colocationService->create($validated);
        return redirect()->route('colocations.show', $colocation)->with('success', 'Colocation created successfully');
    }

   public function show(Colocation $colocation)
    {
        if ($colocation->members()->where('user_id', Auth::id())->whereNull('left_at')->doesntExist()) {
            abort(403);
        }

        return view('colocations.show', compact('colocation'));
    }

    public function edit(Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('colocations.edit', compact('colocation'));
    }

    public function update(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $this->colocationService->update($colocation, $validated);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation modifiée.');
    }

    public function cancel(Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403);
        }

        $this->colocationService->cancel($colocation);

        return redirect()->route('colocations.index')
            ->with('success', 'Colocation annulée.');
    }

     public function destroy(Colocation $colocation)
     {
         if ($colocation->owner_id !== Auth::id()) {
             abort(403);
         }
    
         $this->colocationService->destroy($colocation);
    
         return redirect()->route('colocations.index')
             ->with('success', 'Colocation supprimée.');
     }
}
