@extends('layouts.app')
@section('title')
Verifikasi Pembayaran
@endsection
@section('content')
    <div class="content-card">
        <div class="content-header">
            <h2>Daftar Pengajuan Belum Diverifikasi</h2>

        </div>

        <div class="filter-container">
            <div class="filter-group">
                <label>Tahun</label>
                <select id="filterTahun">
                    <option value="">Semua Tahun</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Bulan</label>
                <select id="filterBulan">
                    <option value="">Semua Bulan</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Perusahaan</label>
                <select id="filterPerusahaan">
                    <option value="">Semua Perusahaan</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Jenis Dokumen</label>
                <select id="filterJenisDokumen">
                    <option value="">Semua Dokumen</option>

                </select>
            </div>
            <div class="filter-group">
                <label>Status Pembayaran</label>
                <select id="filterStatusPembayaran">
                    <option value="">Semua Status Pembayaran</option>
                    <option value="Belum Bayar">Belum Bayar</option>
                    <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                </select>
            </div>
            <button class="btn btn-outline" onclick="resetFilter()">Reset</button>



        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <table id="tablePengajuan" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Lokasi</th>
                    <th>Jenis Dokumen</th>
                    <th>Nomor Surat Tugas</th>
                    <th>Kode Bayar</th>
                    <th>Bukti Bayar</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($pengajuan as $item)
                    @php
                        if (!$item->penagihan || !$item->penagihan->pembayaran) {
                            $statusFilter = 'belum_bayar';
                        } elseif ($item->penagihan->pembayaran->status === 'menunggu') {
                            $statusFilter = 'menunggu';
                        } elseif ($item->penagihan->pembayaran->status === 'diterima') {
                            $statusFilter = 'lunas';
                        } else {
                            $statusFilter = 'lainnya';
                        }
                    @endphp
                    <tr data-status="{{ $statusFilter }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}</td>
                        <td>{{ $item->nama_kapal }}</td>
                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $item->lokasi_kapal }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $item->jenis_dokumen }}</span>
                        </td>
                        <td>{{ $item->agendaSuratPengajuan->nomor_surat_keluar ?? '-' }}</td>
                        <td>{{ $item->kode_bayar }}</td>
                        <td>
                            @if ($item->penagihan && $item->penagihan->pembayaran)
                                <a href="{{ asset('storage/' . $item->penagihan->pembayaran->file) }}" target="_blank"
                                    class="btn btn-sm btn-primary mb-2">
                                    Lihat Bukti
                                </a>
                                <span class="badge bg-primary">Rp. {{ number_format($item->penagihan->total_tarif ?? 0, 0, ',', '.') }}</span>
                            @else
                                <span class="badge bg-secondary">Belum Upload</span>
                            @endif
                        </td>

                        <td>
                            {{-- Menampilkan status pembayaran --}}
                            @if ($item->penagihan && $item->penagihan->pembayaran)
                                @if ($item->penagihan->pembayaran->status === 'menunggu')
                                    <span class="badge bg-warning text-dark d-block mb-2">
                                        Menunggu Verifikasi
                                    </span>
                                    <a href="{{ route('kwitansi.show', $item->penagihan->id) }}" target="_blank"
                                        class="btn btn-sm btn-success mb-2">
                                        Lihat Invoice
                                    </a>
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                        data-bs-target="#verifikasiModal{{ $item->id }}">
                                        Verifikasi
                                    </button>
                                    {{-- @elseif ($item->penagihan->pembayaran->status === 'diterima')
                                    <span class="badge bg-success d-block mb-2">Lunas</span>
                                @elseif ($item->penagihan->pembayaran->status === 'ditolak')
                                    <span class="badge bg-danger d-block mb-2">Ditolak</span> --}}
                                @endif
                            @else
                                <span class="badge bg-secondary d-block mb-2">Belum Bayar</span>
                            @endif

                            {{-- Menampilkan status tagihan --}}
                            @if ($item->penagihan)
                                @if ($item->penagihan->status_bayar === 'belum_bayar' && !$item->penagihan->pembayaran)
                                @elseif($item->penagihan->status_bayar === 'menunggu')

                                @elseif($item->penagihan->status_bayar === 'ditolak')
                                    {{-- <span class="badge bg-danger d-block mb-2">Pembayaran Ditolak</span>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalBayar{{ $item->id }}">
                                        Upload Ulang Bukti
                                    </button> --}}
                                @elseif($item->penagihan->status_bayar === 'diterima')
                                    <span class="badge bg-success d-block mb-2">Lunas</span>
                                    <a href="{{ route('invoice.show', $item->penagihan->id) }}" target="_blank"
                                        class="btn btn-sm btn-success mb-2">
                                        Lihat Kwitansi
                                    </a>
                                    <a href="{{ route('kwitansi.show', $item->penagihan->id) }}" target="_blank"
                                        class="btn btn-sm btn-success ">
                                        Lihat Invoice
                                    </a>
                                @endif
                            @else
                                <span class="badge bg-secondary d-block mb-2">Belum Ada Tagihan</span>
                            @endif

                            {{-- Tombol Edit Pengajuan hanya jika status pengajuan Ditolak --}}
                            @if ($item->status === 'Ditolak')
                                <button class="btn btn-warning btn-sm mt-2" data-bs-toggle="modal"
                                    data-bs-target="#editPengajuanModal{{ $item->id }}">
                                    Edit Pengajuan
                                </button>
                            @endif
                        </td>



                    </tr>
                    @if ($item->penagihan && $item->penagihan->pembayaran)
                        <div class="modal fade" id="verifikasiModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <form
                                        action="{{ route('admin.pembayaran.verifikasi', $item->penagihan->pembayaran->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Verifikasi Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p>
                                                <strong>Kapal:</strong> {{ $item->nama_kapal }} <br>
                                                <strong>Perusahaan:</strong> {{ $item->user->nama_perusahaan ?? '-' }} <br>
                                                <strong>Wilker:</strong> {{ $item->wilayah_kerja }} <br>
                                                <strong>Lokasi:</strong> {{ $item->jenis_dokumen }} <br>
                                                <strong>Bukti Bayar:</strong> <a
                                                    href="{{ asset('storage/' . $item->penagihan->pembayaran->file) }}"
                                                    target="_blank" class="btn btn-sm btn-primary">
                                                    Lihat Bukti
                                                </a> <br>
                                            </p>

                                            <div class="mb-3">
                                                <label>Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="diterima">Terima (Lunas)</option>
                                                    <option value="ditolak">Tolak</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label>Keterangan (opsional)</label>
                                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </tbody>
        </table>

    </div>

<script>
$(document).ready(function() {

    const table = $('#tablePengajuan').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthChange: true,
        pageLength: 10,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "›",
                previous: "‹"
            },
            emptyTable: "Tidak ada data pengajuan"
        },
        drawCallback: function(settings) {
            var api = this.api();
            api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1 + settings._iDisplayStart;
            });
        }
    });

    // ===================================
    // AUTO GENERATE FILTER OPTIONS
    // ===================================
    function generateFilterOptions() {

        let tahunSet = new Set();
        let bulanSet = new Set();
        let perusahaanSet = new Set();
        let jenisSet = new Set();

        table.rows().every(function() {

            let data = this.data();

            let tanggal = data[1];
            let perusahaan = data[4];
            let jenisHtml = data[6];

            if (!tanggal || !tanggal.includes('-')) return;

            let [day, month, year] = tanggal.split('-');

            tahunSet.add(year);
            bulanSet.add(month);

            if (perusahaan && perusahaan !== '-') {
                perusahaanSet.add(perusahaan.trim());
            }

            if (jenisHtml) {
                let jenisText = $('<div>').html(jenisHtml).text().trim();
                if (jenisText !== '-') {
                    jenisSet.add(jenisText);
                }
            }

        });

        tahunSet.forEach(t => {
            $('#filterTahun').append(`<option value="${t}">${t}</option>`);
        });

        bulanSet.forEach(b => {
            $('#filterBulan').append(`<option value="${b}">${b}</option>`);
        });

        perusahaanSet.forEach(p => {
            $('#filterPerusahaan').append(`<option value="${p}">${p}</option>`);
        });

        jenisSet.forEach(j => {
            $('#filterJenisDokumen').append(`<option value="${j}">${j}</option>`);
        });
    }

    generateFilterOptions();

    // ===================================
    // CUSTOM FILTER DATATABLES
    // ===================================
    $.fn.dataTable.ext.search.push(function(settings, data) {

        let tahun = $('#filterTahun').val();
        let bulan = $('#filterBulan').val();
        let perusahaan = $('#filterPerusahaan').val().toLowerCase();
        let jenis = $('#filterJenisDokumen').val().toLowerCase();
        let statusPembayaran = $('#filterStatusPembayaran').val().toLowerCase();

        let tanggal = data[1] || '';
        let perusahaanText = (data[4] || '').toLowerCase();

        // 🔥 Bersihkan HTML badge
        let jenisText = $('<div>').html(data[6] || '').text().toLowerCase();
        let statusText = $('<div>').html(data[10] || '').text().toLowerCase();

        if (tanggal.includes('-')) {
            let [day, month, year] = tanggal.split('-');

            if (tahun && year !== tahun) return false;
            if (bulan && month !== bulan) return false;
        }

        if (perusahaan && !perusahaanText.includes(perusahaan)) return false;
        if (jenis && !jenisText.includes(jenis)) return false;
        if (statusPembayaran && !statusText.includes(statusPembayaran)) return false;

        return true;
    });

    // ===================================
    // TRIGGER FILTER
    // ===================================
    $('#filterTahun, #filterBulan, #filterPerusahaan, #filterJenisDokumen, #filterStatusPembayaran')
        .on('change', function() {
            table.draw();
        });

});


// ===================================
// RESET FILTER
// ===================================
function resetFilter() {

    $('#filterTahun').val('');
    $('#filterBulan').val('');
    $('#filterPerusahaan').val('');
    $('#filterJenisDokumen').val('');
    $('#filterStatusPembayaran').val('');

    $('#tablePengajuan').DataTable().search('').draw();
}
</script>
@endsection
