<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushNotif;
use Illuminate\Support\Facades\Auth;

class PushNotificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required',
        ]);

        // Simpan atau update token berdasarkan user yang sedang login
        PushNotif::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'endpoint' => $request->endpoint,
                'public_key' => $request->publicKey,
                'auth_token' => $request->authToken,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Token notifikasi berhasil disimpan.']);
    }

    public function testNotification()
    {
        // Cari data push notif milik user yang sedang login
        $subscription = \App\Models\PushNotif::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();

        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan. Aktifkan notifikasi dulu!']);
        }

        // Catatan: Untuk pengiriman nyata via Web Push API, biasanya menggunakan package seperti minishlink/web-push.
        // Tapi untuk memastikan alurnya, kita bisa cek dulu apakah datanya ada.
        return response()->json([
            'success' => true, 
            'message' => 'Token ditemukan!',
            'endpoint' => $subscription->endpoint
        ]);
    }
}
