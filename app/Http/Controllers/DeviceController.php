<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function index()
    {
        // Tampilkan semua device milik user yang login
        $devices = Device::where('user_id', Auth::id())->get();
        return view('devices.index', compact('devices'));
    }

    public function store(Request $request)
    {
        $request->validate(['label' => 'required']);

        Device::create([
            'user_id' => Auth::id(),
            'label' => $request->label,
            'api_token' => Str::random(20),
            'status' => 'disconnected',
            // --- SETTINGAN PAKET TRIAL ---
            'quota' => 10, // Kasih 10 pesan gratis buat tes
            'expired_date' => now()->addDays(3) // Aktif 3 hari aja
        ]);
        return back()->with('success', 'Device berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required',
            'webhook_url' => 'nullable|url' // Validasi harus format URL
        ]);

        $device = Device::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        $device->update([
            'label' => $request->label,
            'webhook_url' => $request->webhook_url
        ]);

        return back()->with('success', 'Data device berhasil diupdate!');
    }

    public function destroy($id)
    {
        // Hapus device
        Device::where('user_id', Auth::id())->where('id', $id)->delete();

        // (PR: Nanti tambahkan request ke Node.js buat logout sesi juga)
        return back()->with('success', 'Device dihapus.');
    }
}
