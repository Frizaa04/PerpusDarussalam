<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Kumpulkan data siswa dummy
        $allStudents = collect([
            (object)['nis' => '2411102441211', 'name' => 'Febri Hamzah Jemikan Nata', 'role' => 'Siswa'],
            (object)['nis' => '2411102441212', 'name' => 'Muhamad Aditya Nugroho', 'role' => 'Siswa'],
            (object)['nis' => '2411102441213', 'name' => 'Muhammad Al Baihaqi', 'role' => 'Siswa'],
            (object)['nis' => '2411102441214', 'name' => 'Rofi Raissa Adiyatma', 'role' => 'Siswa'],
            (object)['nis' => '2411102441215', 'name' => 'Muhammad Diky Anwar', 'role' => 'Siswa'],
            (object)['nis' => '2411102441216', 'name' => 'Aisha Hannah Heriawan', 'role' => 'Siswa'],
            (object)['nis' => '2411102441217', 'name' => 'Raditya Andromeda Barito', 'role' => 'Admin'],
            (object)['nis' => '2411102441218', 'name' => 'Lucky Putra Mahendra', 'role' => 'Siswa'],
        ]);

        // Logika filter pencarian
        if ($search) {
            $students = $allStudents->filter(function ($student) use ($search) {
                return false !== stripos($student->name, $search) || false !== stripos($student->nis, $search);
            });
        } else {
            $students = $allStudents;
        }

        return view('layouts.pages.admin.manajemen_siswa', compact('students', 'search'));
    }
}