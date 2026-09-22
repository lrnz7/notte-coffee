<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
            'materials' => 'nullable|array',
            'amounts' => 'nullable|array',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create([
            'name' => $request->name,
            'category' => $request->category,
            'selling_price' => $request->selling_price,
            'description' => $request->description,
            'image' => $imagePath ? Storage::url($imagePath) : null,
            'is_active' => true,
        ]);

        $syncData = [];
        if ($request->has('materials') && is_array($request->materials)) {
            foreach ($request->materials as $index => $materialId) {
                if (!empty($materialId) && isset($request->amounts[$index]) && $request->amounts[$index] > 0) {
                    $syncData[(int)$materialId] = ['quantity_required' => $request->amounts[$index]];
                }
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
            'materials' => 'nullable|array',
            'amounts' => 'nullable|array',
        ]);

        $imageUrl = $menu->image;

        // Cek jika user mengunggah foto produk baru
        if ($request->hasFile('image')) {
            // Hapus file lama jika disimpan lokal di storage (bukan URL unsplash)
            if ($menu->image && str_contains($menu->image, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $menu->image);
                Storage::disk('public')->delete($oldPath);
            }

            // Simpan foto baru
            $newPath = $request->file('image')->store('menus', 'public');
            $imageUrl = Storage::url($newPath);
        }

        // 1. Update Data Utama Menu
        $menu->update([
            'name' => $request->name,
            'category' => $request->category,
            'selling_price' => $request->selling_price,
            'description' => $request->description,
            'image' => $imageUrl,
        ]);

        // 2. Sinkronisasi Bahan Baku (Pivot Table)
        $syncData = [];
        if ($request->has('materials') && is_array($request->materials)) {
            foreach ($request->materials as $index => $materialId) {
                if (!empty($materialId) && isset($request->amounts[$index]) && $request->amounts[$index] > 0) {
                    $syncData[(int)$materialId] = ['quantity_required' => $request->amounts[$index]];
                }
            }
        }

        $menu->materials()->sync($syncData);

        return redirect()->route('menus.index')->with('success', 'Menu dan resep berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        // Hapus file gambar dari storage jika ada
        if ($menu->image && str_contains($menu->image, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $menu->image);
            Storage::disk('public')->delete($oldPath);
        }

        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus!');
    }
}