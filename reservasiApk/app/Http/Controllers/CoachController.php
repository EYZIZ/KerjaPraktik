<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoachController extends Controller
{
    /**
     * Tampilkan semua coach
     */
    public function index()
    {
        $coaches = Coach::latest()->get();
        return view('coach.index', compact('coaches'));
    }

    /**
     * Form tambah coach
     */
    public function create()
    {
        return view('coach.create');
    }

    /**
     * Simpan coach baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'price' => 'required|integer|min:0',
        ]);

        $data = $request->only(['name', 'phone', 'price']);

        // upload photo jika ada
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('coach', 'public');
        }

        Coach::create($data);

        return redirect()->route('coach.index')
            ->with('success', 'Coach berhasil ditambahkan.');
    }

    /**
     * Form edit coach
     */
    public function edit(Coach $coach)
    {
        return view('coach.edit', compact('coach'));
    }

    /**
     * Update data coach
     */
    public function update(Request $request, Coach $coach)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'price' => 'required|integer|min:0',
        ]);

        $data = $request->only(['name', 'phone', 'price']);

        // jika ada foto baru → hapus lama → simpan baru
        if ($request->hasFile('photo')) {

            if ($coach->photo && Storage::disk('public')->exists($coach->photo)) {
                Storage::disk('public')->delete($coach->photo);
            }

            $data['photo'] = $request->file('photo')->store('coach', 'public');
        }

        $coach->update($data);

        return redirect()->route('coach.index')
            ->with('success', 'Coach berhasil diperbarui.');
    }

    /**
     * Hapus coach
     */
    public function destroy(Coach $coach)
    {
        if ($coach->photo && Storage::disk('public')->exists($coach->photo)) {
            Storage::disk('public')->delete($coach->photo);
        }

        $coach->delete();

        return redirect()->route('coach.index')
            ->with('success', 'Coach berhasil dihapus.');
    }
}
