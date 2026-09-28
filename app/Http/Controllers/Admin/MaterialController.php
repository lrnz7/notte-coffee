<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::latest()->get();
        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'min_stock_alert' => 'required|numeric|min:0',
        ]);

        Material::create($request->all());

        return redirect()->route('materials.index')->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function edit(Material $material)
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'min_stock_alert' => 'required|numeric|min:0',
        ]);

        $oldStock = (float) $material->stock_quantity;
        $oldUnitPrice = (float) $material->unit_price;

        $inputStock = (float) $request->stock_quantity;
        $inputPrice = (float) $request->unit_price;

        // Determine added stock quantity
        $addedQty = $inputStock - $oldStock;

        if ($oldStock <= 0 && $inputStock > 0) {
            // First time stock initialisation
            $newUnitPrice = $inputPrice;
        } elseif ($addedQty > 0 && ($oldStock + $addedQty) > 0) {
            // Moving Average Cost Formula: ((Old Qty * Old Price) + (Added Qty * Added Price)) / Total Qty
            $totalQty = $oldStock + $addedQty;
            $newUnitPrice = (($oldStock * $oldUnitPrice) + ($addedQty * $inputPrice)) / $totalQty;
        } else {
            // Stock reduction, adjustment, or info edit -> PRESERVE existing unit price!
            $newUnitPrice = $oldUnitPrice > 0 ? $oldUnitPrice : $inputPrice;
        }

        $material->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'unit_price' => $newUnitPrice,
            'stock_quantity' => $inputStock,
            'min_stock_alert' => $request->min_stock_alert,
        ]);

        return redirect()->route('materials.index')->with('success', 'Bahan baku berhasil diperbarui dengan Moving Average Cost!');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Bahan baku berhasil dihapus!');
    }
}