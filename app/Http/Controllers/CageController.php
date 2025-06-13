<?php

namespace App\Http\Controllers;

use App\Models\Cage;
use Illuminate\Http\Request;

class CageController extends Controller
{
    public function index()
    {
        $cages = Cage::with('animals')->get();
        return view('cages.index', compact('cages'));
    }

    public function create()
    {
        return view('cages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        Cage::create($validated);

        return redirect()->route('cages.index')->with('success', 'Клетка успешно добавлена!');
    }

    public function show(Cage $cage)
    {
        return view('cages.show', compact('cage'));
    }

    public function edit(Cage $cage)
    {
        return view('cages.edit', compact('cage'));
    }

    public function update(Request $request, Cage $cage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:' . $cage->animals->count(),
        ]);

        $cage->update($validated);

        return redirect()->route('cages.index')->with('success', 'Клетка успешно обновлена!');
    }

    public function destroy(Cage $cage)
    {
        if ($cage->animals->count() > 0) {
            return back()->with('error', 'Нельзя удалить клетку с животными!');
        }

        $cage->delete();
        return redirect()->route('cages.index')->with('success', 'Клетка успешно удалена!');
    }
}