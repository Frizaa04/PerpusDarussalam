<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Fungsi untuk menampilkan semua notifikasi di halaman khusus
    public function index()
    {
        // Mengambil notifikasi terbaru
        $notifications = Notification::with('borrowing.user')
                            ->latest()
                            ->get();

        return view('layouts.pages.admin.notifikasi', compact('notifications'));
    }

    // Fungsi untuk menandai satu notifikasi sudah dibaca
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        $notification->update([
            'status'  => 'read',
            'read_at' => now(), // Mencatat waktu kapan dibaca
        ]);

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sudah dibaca.'
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    // Fungsi untuk menandai semua notifikasi sudah dibaca sekaligus 
    public function markAllAsRead()
    {
        \App\Models\Notification::where('status', 'unread')
            ->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}