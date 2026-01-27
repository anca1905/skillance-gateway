<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\MessageHistory;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        // Ambil ID semua device milik user ini
        $deviceIds = Device::where('user_id', Auth::id())->pluck('id');

        // Ambil history dari device-device tersebut, urutkan dari yang terbaru
        // Pakai paginate(10) biar kalau datanya ribuan gak berat loadingnya
        $histories = MessageHistory::whereIn('device_id', $deviceIds)
            ->with('device')
            ->latest()
            ->paginate(10);

        return view('history.index', compact('histories'));
    }
}
