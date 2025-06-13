<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Cage;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function index()
    {
        $animals = Animal::with('cage')->get();
        return view('animals.index', compact('animals'));
    }

    public function create()
    {
        $cages = Cage::whereRaw('capacity > (SELECT COUNT(*) FROM animals WHERE cage_id = cages.id)')->get();
        return view('animals.create', compact('cages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'species' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'description' => 'required|string',
            'cage_id' => 'required|exists:cages,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('animal_images', 'public');
            $validated['image'] = $path;
        }

        Animal::create($validated);

        return redirect()->route('animals.index')->with('success', 'Животное успешно добавлено!');
    }

    public function show(Animal $animal)
    {
        return view('animals.show', compact('animal'));
    }

    public function edit(Animal $animal)
    {
        $cages = Cage::whereRaw('capacity > (SELECT COUNT(*) FROM animals WHERE cage_id = cages.id)')
            ->orWhere('id', $animal->cage_id)
            ->get();
        return view('animals.edit', compact('animal', 'cages'));
    }

    public function update(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'species' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'description' => 'required|string',
            'cage_id' => 'required|exists:cages,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Удалить старое изображение, если есть
            if ($animal->image) {
                Storage::disk('public')->delete($animal->image);
            }
            
            $path = $request->file('image')->store('animal_images', 'public');
            $validated['image'] = $path;
        }

        $animal->update($validated);

        return redirect()->route('animals.index')->with('success', 'Животное успешно обновлено!');
    }

    public function destroy(Animal $animal)
    {
        if ($animal->image) {
            Storage::disk('public')->delete($animal->image);
        }
        
        $animal->delete();
        return redirect()->route('animals.index')->with('success', 'Животное успешно удалено!');
    }
}