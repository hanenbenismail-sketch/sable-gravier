<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::orderBy('name')->get();

        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        return view('sites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:carriere,depot',
            'gouvernorat' => 'required|string|max:100',
            'delegation' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'tracks_stock' => 'required|boolean',
        ]);

        $validated['tracks_stock'] = (bool) $validated['tracks_stock'];

        Site::create($validated);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site ajouté avec succès.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $site = Site::findOrFail($id);

        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, string $id)
    {
        $site = Site::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:carriere,depot',
            'gouvernorat' => 'required|string|max:100',
            'delegation' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'tracks_stock' => 'required|boolean',
        ]);

        $validated['tracks_stock'] = (bool) $validated['tracks_stock'];

        $site->update($validated);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site modifié avec succès.');
    }

    public function destroy(string $id)
    {
        //
    }
}
