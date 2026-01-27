<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BroadcastController extends Controller
{
    public function index()
    {
        $devices = Device::where('user_id', Auth::id())->where('status', 'connected')->get();
        return view('broadcast.index', compact('devices'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
            'numbers' => 'required', // Textarea isi nomor
            'message' => 'required'
        ]);

        $device = Device::where('id', $request->device_id)->where('user_id', Auth::id())->firstOrFail();
        // dd($device);

        // 1. Parsing Nomor (Pisahkan berdasarkan baris baru atau koma)
        // User bisa input: 
        // 081234
        // 085678
        $rawNumbers = preg_split('/[\r\n,]+/', $request->numbers);
        $targets = [];

        foreach ($rawNumbers as $num) {
            $clean = trim($num);
            if (!empty($clean)) {
                $targets[] = $clean;
            }
        }

        if (count($targets) == 0) return back()->withErrors(['numbers' => 'Tidak ada nomor valid.']);

        // 2. Cek Kuota (Opsional: Kalau mau ketat, hitung dulu cukup gak kuotanya)
        if ($device->quota < count($targets)) {
            return back()->withErrors(['numbers' => 'Kuota tidak cukup untuk ' . count($targets) . ' nomor.']);
        }

        // 3. Kirim ke Node.js
        try {
            Http::post('http://localhost:3000/broadcast', [
                'token' => $device->api_token,
                'targets' => $targets,
                'message' => $request->message
            ]);

            // Potong Kuota (Estimasi)
            $device->decrement('quota', count($targets));

            return back()->with('success', 'Broadcast sedang diproses di latar belakang! Estimasi selesai dalam ' . (count($targets) * 5) . ' detik.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal koneksi ke server WA.']);
        }
    }
}
