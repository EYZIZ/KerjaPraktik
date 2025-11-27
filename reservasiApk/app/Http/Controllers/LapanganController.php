<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    // Tampilkan semua lapangan
    public function index()
    {
        $lapangans = Lapangan::latest()->get();
        return view('lapangan.index', compact('lapangans'));
    }

    // Form tambah lapangan
    public function create()
    {
        return view('lapangan.create');
    }

    // Simpan lapangan baru
    public function store(Request $request)
    {
        $request->validate([
            'location'       => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price_per_hour' => 'required|integer|min:0',
            'status'         => 'required|in:Tersedia,Perbaikan,Terisi',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            // Disimpan di storage/app/public/lapangan
            $photoPath = $request->file('photo')->store('lapangan', 'public');
        }

        Lapangan::create([
            'photo'          => $photoPath,
            'location'       => $request->location,
            'description'    => $request->description,
            'price_per_hour' => $request->price_per_hour,
            'status'         => $request->status,
        ]);

        return redirect()->route('lapangan.index')
            ->with('success', 'Data lapangan berhasil ditambahkan.');
    }

    // Form edit lapangan
    public function edit(Lapangan $lapangan)
    {
        return view('lapangan.edit', compact('lapangan'));
    }

    // Update lapangan
    public function update(Request $request, Lapangan $lapangan)
    {
        $request->validate([
            'location'       => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price_per_hour' => 'required|integer|min:0',
            'status'         => 'required|in:aktif,nonaktif,Terisi',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $photoPath = $lapangan->photo;

        // Jika upload foto baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            $photoPath = $request->file('photo')->store('lapangan', 'public');
        }

        $lapangan->update([
            'photo'          => $photoPath,
            'location'       => $request->location,
            'description'    => $request->description,
            'price_per_hour' => $request->price_per_hour,
            'status'         => $request->status,
        ]);

        return redirect()->route('lapangan.index')
            ->with('success', 'Data lapangan berhasil diperbarui.');
    }

    // Hapus lapangan
    public function destroy(Lapangan $lapangan)
    {
        if ($lapangan->photo && Storage::disk('public')->exists($lapangan->photo)) {
            Storage::disk('public')->delete($lapangan->photo);
        }

        $lapangan->delete();

        return redirect()->route('lapangan.index')
            ->with('success', 'Data lapangan berhasil dihapus.');
    }
}
