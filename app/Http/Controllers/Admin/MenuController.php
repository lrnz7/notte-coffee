<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Material;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('materials')->latest()->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $materials = Material::all();
        return view('admin.menus.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'materials' => 'required|array',
            'amounts' => 'required|array',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'category' => $request->category,
            'selling_price' => $request->selling_price,
            'description' => $request->description,
            'is_active' => true,
        ]);

        $syncData = [];
        foreach ($request->materials as $index => $materialId) {
            if (!empty($materialId) && isset($request->amounts[$index]) && $request->amounts[$index] > 0) {
                $syncData[$materialId] = ['quantity_required' => $request->amounts[$index]];
            }
        }
        
        if (!empty($syncData)) {
            $menu->materials()->sync($syncData);
        }

        return redirect()->route('menus.index')->with('success', 'Menu dan resep HPP berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        $menu->load('materials');
        $materials = Material::all();
        return view('admin.menus.edit', compact('menu', 'materials'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'materials' => 'required|array',
            'amounts' => 'required|array',
        ]);

        $menu->update([
            'name' => $request->name,
            'category' => $request->category,
            'selling_price' => $request->selling_price,
            'description' => $request->description,
        ]);

        $syncData = [];
        foreach ($request->materials as $index => $materialId) {
            if (!empty($materialId) && isset($request->amounts[$index]) && $request->amounts[$index] > 0) {
                $syncData[$materialId] = ['quantity_required' => $request->amounts[$index]];
            }
        }
        
        $menu->materials()->sync($syncData);

        return redirect()->route('menus.index')->with('success', 'Menu dan resep berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus!');
    }
}