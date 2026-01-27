<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Autoreply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoreplyController extends Controller
{
    public function index()
    {
        // Ambil semua device milik user ini
        $devices = Device::where('user_id', Auth::id())->get();

        // Ambil semua autoreply milik user ini (melalui relasi device)
        $autoreplies = Autoreply::whereIn('device_id', $devices->pluck('id'))->with('device')->get();

        return view('autoreply.index', compact('devices', 'autoreplies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
            'keyword' => 'required',
            'response' => 'required',
            'search_type' => 'required'
        ]);

        // Pastikan device milik user yang login (Security)
        $device = Device::where('id', $request->device_id)->where('user_id', Auth::id())->firstOrFail();

        Autoreply::create([
            'device_id' => $device->id,
            'keyword' => strtolower($request->keyword), // Simpan huruf kecil biar seragam
            'response' => $request->response,
            'search_type' => $request->search_type
        ]);

        return back()->with('success', 'Keyword baru berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $reply = Autoreply::findOrFail($id);

        // Cek kepemilikan
        $device = Device::where('id', $reply->device_id)->where('user_id', Auth::id())->firstOrFail();

        $reply->delete();
        return back()->with('success', 'Autoreply dihapus.');
    }
}
