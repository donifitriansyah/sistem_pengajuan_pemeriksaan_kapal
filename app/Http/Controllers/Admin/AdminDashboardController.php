<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPemeriksaanKapal;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all the data you need
        $pengajuanPemeriksaans = PengajuanPemeriksaanKapal::all();

        // Return the view with the data
        return view('pages.admin.daftar-pengajuan', compact('pengajuanPemeriksaans'));
    }
}
