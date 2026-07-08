<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PraPendaftaranPerkara;
use Illuminate\View\View;

class PraPendaftaranController extends Controller
{
    public function show(PraPendaftaranPerkara $praPendaftaranPerkara): View
    {
        $praPendaftaranPerkara->load([
            'klien.profilKlien',
            'kategori',
            'dokumenPerkara.catatanVerifikasi',
            'verifikasiTerakhir.catatanVerifikasi',
            'riwayatStatus.user'
        ]);

        return view('admin.pra-pendaftaran.show', compact('praPendaftaranPerkara'));
    }
}
