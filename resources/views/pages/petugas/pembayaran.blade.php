@extends('layouts.app')
@section('title')
    Verifikasi Pembayaran Petugas
@endsection
@section('content')
    <style>
        #pengajuaTable th,
        #pengajuanTable td {
            white-space: normal !important;
            word-break: break-word;
            font-size: 11px;
            /* opsional biar muat */
        }
    </style>
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
                    <option value="belum bayar">Belum Bayar</option>
                    <option value="menunggu verifikasi">Menunggu Verifikasi</option>
                </select>
            </div>
            <div class="filter-group">

            </div>
            <div class="filter-group">
                <label>Tanggal Mulai</label>
                <input type="date" id="filterMulai" class="form-control">
            </div>

            <div class="filter-group">
                <label>Tanggal Selesai</label>
                <input type="date" id="filterSelesai" class="form-control">
            </div>
            <button class="btn btn-outline" onclick="resetFilter()">Reset</button>
            <div class="filter-group">
                <button class="btn btn-success" onclick="exportExcel()">
                    Export Excel
                </button>
            </div>



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
                            $statusFilter = 'belum bayar';
                        } elseif ($item->penagihan->pembayaran->status === 'menunggu') {
                            $statusFilter = 'menunggu verifikasi';
                        } elseif ($item->penagihan->pembayaran->status === 'diterima') {
                            $statusFilter = 'lunas';
                        } else {
                            $statusFilter = 'lainnya';
                        }
                    @endphp
                    <tr data-tanggal="{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('Y-m-d') }}"
                        data-tahun="{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('Y') }}"
                        data-bulan="{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('m') }}"
                        data-perusahaan="{{ strtolower($item->user->nama_perusahaan ?? '') }}"
                        data-jenis="{{ strtolower($item->jenis_dokumen) }}" data-status="{{ $statusFilter }}">
                        <td>{{ $loop->iteration }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('Y-m-d') }}">
                            {{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}
                        </td>
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
                                <span class="badge bg-primary">Rp.
                                    {{ number_format($item->penagihan->total_tarif ?? 0, 0, ',', '.') }}</span>
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
                                                <strong>Tanggal Bukti Bayar Diupload:</strong>
                                                {{ \Carbon\Carbon::parse($item->penagihan->pembayaran->created_at)->format('d-m-Y H:i') }}
                                                <br>

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
        let table;

        // =========================
        // FILTER GLOBAL (PAKAI DATA ATTRIBUTE)
        // =========================
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {

            if (settings.nTable.id !== 'tablePengajuan') return true;

            let rowNode = settings.aoData[dataIndex].nTr;

            let tahun = $('#filterTahun').val();
            let bulan = $('#filterBulan').val();
            let perusahaan = ($('#filterPerusahaan').val() || '').toLowerCase();
            let jenis = ($('#filterJenisDokumen').val() || '').toLowerCase();
            let status = ($('#filterStatusPembayaran').val() || '').toLowerCase();
            let mulai = $('#filterMulai').val();
            let selesai = $('#filterSelesai').val();

            let rowTahun = $(rowNode).data('tahun');
            let rowBulan = $(rowNode).data('bulan');
            let rowPerusahaan = ($(rowNode).data('perusahaan') || '').toLowerCase();
            let rowJenis = ($(rowNode).data('jenis') || '').toLowerCase();
            let rowStatus = ($(rowNode).data('status') || '').toLowerCase();
            let rowTanggal = $(rowNode).data('tanggal');

            // =====================
            // FILTER TANGGAL RANGE
            // =====================
            if (mulai && rowTanggal < mulai) return false;
            if (selesai && rowTanggal > selesai) return false;

            // =====================
            // FILTER TAHUN & BULAN
            // =====================
            if (tahun && rowTahun != tahun) return false;
            if (bulan && rowBulan != bulan) return false;

            // =====================
            // FILTER PERUSAHAAN
            // =====================
            if (perusahaan && !rowPerusahaan.includes(perusahaan)) return false;

            // =====================
            // FILTER JENIS
            // =====================
            if (jenis && !rowJenis.includes(jenis)) return false;

            // =====================
            // FILTER STATUS
            // =====================
            if (status && rowStatus !== status) return false;

            return true;
        });

        // =========================
        // INIT DATATABLE
        // =========================
        $(document).ready(function() {

            table = $('#tablePengajuan').DataTable();

            generateFilterOptions();

            $('#filterTahun, #filterBulan, #filterPerusahaan, #filterJenisDokumen, #filterStatusPembayaran')
                .on('change', function() {
                    table.draw();
                });

            $('#filterMulai, #filterSelesai')
                .on('change keyup', function() {
                    table.draw();
                });

        });

        // =========================
        // GENERATE FILTER OPTIONS
        // =========================
        function generateFilterOptions() {

            let tahunSet = new Set();
            let bulanSet = new Set();
            let perusahaanSet = new Set();
            let jenisSet = new Set();

            $('#tablePengajuan tbody tr').each(function() {

                let el = $(this);

                tahunSet.add(el.data('tahun'));
                bulanSet.add(el.data('bulan'));

                if (el.data('perusahaan')) {
                    perusahaanSet.add(el.data('perusahaan'));
                }

                if (el.data('jenis')) {
                    jenisSet.add(el.data('jenis'));
                }
            });

            $('#filterTahun').html('<option value="">Semua Tahun</option>');
            tahunSet.forEach(v => $('#filterTahun').append(`<option value="${v}">${v}</option>`));

            $('#filterBulan').html('<option value="">Semua Bulan</option>');
            bulanSet.forEach(v => $('#filterBulan').append(`<option value="${v}">${v}</option>`));

            $('#filterPerusahaan').html('<option value="">Semua Perusahaan</option>');
            perusahaanSet.forEach(v => $('#filterPerusahaan').append(`<option value="${v}">${v}</option>`));

            $('#filterJenisDokumen').html('<option value="">Semua Dokumen</option>');
            jenisSet.forEach(v => $('#filterJenisDokumen').append(`<option value="${v}">${v}</option>`));
        }

        // =========================
        // RESET FILTER
        // =========================
        function resetFilter() {

            $('#filterTahun').val('');
            $('#filterBulan').val('');
            $('#filterPerusahaan').val('');
            $('#filterJenisDokumen').val('');
            $('#filterStatusPembayaran').val('');
            $('#filterMulai').val('');
            $('#filterSelesai').val('');

            table.search('');
            table.draw();
        }
    </script>

        <script>
function exportExcel() {

    let rows = table.rows({ search: 'applied' }).nodes();

    let dataRows = [];

    $(rows).each(function() {

        let row = $(this);

        let tanggal = row.data('tanggal') || '';
        let perusahaan = row.data('perusahaan') || '';
        let jenis = row.data('jenis') || '';
        let status = row.data('status') || '';

        let cols = row.find('td');

        dataRows.push({
            tanggal: tanggal,
            row: [
                formatTanggal(tanggal).toUpperCase(),
                $(cols[2]).text().trim().toUpperCase(),
                perusahaan.toUpperCase(),
                $(cols[4]).text().trim().toUpperCase(),
                jenis.toUpperCase(),
                $(cols[6]).text().trim().toUpperCase(),
                $(cols[7]).text().trim().toUpperCase(),
                formatStatus(status) // ❌ TIDAK CAPSLOCK
            ]
        });
    });

    // =========================
    // SORT BY TANGGAL (TERLAMA)
    // =========================
    dataRows.sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));

    // =========================
    // SIAPKAN DATA FINAL
    // =========================
    let data = [];

    data.push([
        "NO",
        "TANGGAL",
        "NAMA KAPAL",
        "PERUSAHAAN",
        "LOKASI",
        "JENIS DOKUMEN",
        "NOMOR SURAT",
        "KODE BAYAR",
        "STATUS"
    ]);

    dataRows.forEach((item, index) => {
        data.push([
            index + 1,
            ...item.row
        ]);
    });

    // =========================
    // EXPORT
    // =========================
    let ws = XLSX.utils.aoa_to_sheet(data);
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Verifikasi Pembayaran");

    XLSX.writeFile(wb, "Verifikasi_Pembayaran.xlsx");
}


// =========================
// FORMAT TANGGAL
// =========================
function formatTanggal(tgl) {
    if (!tgl) return '';
    let [year, month, day] = tgl.split('-');
    return `${day}-${month}-${year}`;
}

// =========================
// FORMAT STATUS (TIDAK CAPS)
// =========================
function formatStatus(status) {
    switch (status) {
        case 'belum bayar':
            return 'Belum Bayar';
        case 'menunggu verifikasi':
            return 'Menunggu Verifikasi';
        case 'lunas':
            return 'Lunas';
        default:
            return status;
    }
}
</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endsection
