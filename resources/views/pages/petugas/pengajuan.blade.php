@extends('layouts.app')
@section('header-title')
    Dashboard Petugas
@endsection
@section('content')
    <!-- Scorecard -->
    <div class="scorecard-container">
        <div class="scorecard total">
            <div class="scorecard-label">Total Pengajuan</div>
            <div class="scorecard-value" id="totalPengajuan">0</div>
        </div>
        <div class="scorecard belum-tagihan">
            <div class="scorecard-label">Belum Ada Tagihan</div>
            <div class="scorecard-value" id="totalBelumTagihan">0</div>
        </div>
        <div class="scorecard belum-bayar">
            <div class="scorecard-label">Belum Bayar</div>
            <div class="scorecard-value" id="totalBelumBayar">0</div>
        </div>
        <div class="scorecard menunggu">
            <div class="scorecard-label">Menunggu Verifikasi</div>
            <div class="scorecard-value" id="totalMenunggu">0</div>
        </div>
        <div class="scorecard lunas">
            <div class="scorecard-label">Lunas</div>
            <div class="scorecard-value" id="totalLunas">0</div>
        </div>
    </div>
    <div class="content-card">
        <div class="content-header">
            <h2>Semua Daftar Pengajuan</h2>
        </div>

        <!-- Filter -->
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
                    <option value="PHQC">PHQC</option>
                    <option value="SSCEC">SSCEC</option>
                    <option value="COP">COP</option>
                </select>
            </div>
            <button class="btn btn-outline" id="resetFilter">Reset</button>
        </div>

        <!-- Tabel -->
        <table id="pengajuanTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Lokasi</th>
                    <th>Jenis Dokumen</th>
                    <th>Status</th>
                    <th>Wilker</th>
                    <th>Kode Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengajuan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('Y-m-d') }}</td>
                        <td>{{ $item->nama_kapal }}</td>
                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $item->lokasi_kapal }}</td>
                        <td>{{ $item->jenis_dokumen }}</td>
                        <td>
                            @if (!$item->penagihan)
                                Belum Ada Tagihan
                            @else
                                @if ($item->penagihan->status_bayar === 'belum_bayar')
                                    Belum Bayar
                                @elseif ($item->penagihan->status_bayar === 'menunggu')
                                    Menunggu Verifikasi
                                @elseif ($item->penagihan->status_bayar === 'ditolak')
                                    Ditolak
                                @elseif ($item->penagihan->status_bayar === 'diterima')
                                    Lunas
                                @endif
                            @endif
                        </td>
                        <td>{{ $item->wilayah_kerja }}</td>

                        <td><span class="badge bg-secondary">{{ $item->kode_bayar }}</span></td>

                        <td>
                            {{-- tombol aksi sesuai status --}}
                            @if (!$item->penagihan)
                                <span class="badge bg-warning">Belum Buat Tagihan</span>
                            @else
                                @if ($item->penagihan->status_bayar === 'belum_bayar')
                                    <span class="badge bg-danger">Belum Bayar</span>
                                @elseif($item->penagihan->status_bayar === 'ditolak')
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalBayar{{ $item->id }}">Upload Ulang</button>
                                @elseif($item->penagihan->status_bayar === 'diterima')
                                    <a href="{{ route('invoice.show', $item->penagihan->id) }}" target="_blank"
                                        class="btn btn-sm btn-success">Lihat Invoice</a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

        <script>
            $(document).ready(function() {
                var table = $('#pengajuanTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    columnDefs: [{
                            orderable: false,
                            targets: 7
                        } // Kolom Aksi tidak bisa di-sort
                    ]
                });

                // Populasi filter Tahun, Bulan, Perusahaan
                var years = new Set();
                var months = new Set();
                var perusahaanSet = new Set();

                table.column(1).data().each(function(date) {
                    var d = new Date(date);
                    years.add(d.getFullYear());
                    months.add(d.getMonth() + 1);
                });

                table.column(3).data().each(function(perusahaan) {
                    perusahaanSet.add(perusahaan);
                });

                years = Array.from(years).sort();
                months = Array.from(months).sort((a, b) => a - b);
                perusahaanSet = Array.from(perusahaanSet).sort();

                years.forEach(y => $('#filterTahun').append(`<option value="${y}">${y}</option>`));
                months.forEach(m => $('#filterBulan').append(`<option value="${m}">${m}</option>`));
                perusahaanSet.forEach(p => $('#filterPerusahaan').append(`<option value="${p}">${p}</option>`));

                // Filter kustom
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var tahun = $('#filterTahun').val();
                    var bulan = $('#filterBulan').val();
                    var perusahaan = $('#filterPerusahaan').val();
                    var dokumen = $('#filterJenisDokumen').val();

                    var rowDate = new Date(data[1]);
                    var rowTahun = rowDate.getFullYear();
                    var rowBulan = rowDate.getMonth() + 1;
                    var rowPerusahaan = data[3];
                    var rowDokumen = data[5];

                    return (!tahun || rowTahun == tahun) &&
                        (!bulan || rowBulan == bulan) &&
                        (!perusahaan || rowPerusahaan == perusahaan) &&
                        (!dokumen || rowDokumen == dokumen);
                });

                $('#filterTahun, #filterBulan, #filterPerusahaan, #filterJenisDokumen').on('change', function() {
                    table.draw();
                });

                // Reset filter
                $('#resetFilter').on('click', function() {
                    $('#filterTahun, #filterBulan, #filterPerusahaan, #fi   lterJenisDokumen').val('');
                    table.draw();
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                let total = 0;
                let belumTagihan = 0;
                let belumBayar = 0;
                let menunggu = 0;
                let lunas = 0;

                // LOOP SEMUA ROW ASLI (BUKAN HASIL FILTER)
                document.querySelectorAll('#pengajuanTable tbody tr').forEach(row => {

                    total++;

                    const statusCell = row.children[6]; // kolom STATUS
                    const status = statusCell.innerText.trim();

                    if (status === 'Belum Ada Tagihan') {
                        belumTagihan++;
                    } else if (status === 'Belum Bayar') {
                        belumBayar++;
                    } else if (status === 'Menunggu Verifikasi') {
                        menunggu++;
                    } else if (status === 'Lunas') {
                        lunas++;
                    }
                });

                // SET KE SCORECARD
                document.getElementById('totalPengajuan').innerText = total;
                document.getElementById('totalBelumTagihan').innerText = belumTagihan;
                document.getElementById('totalBelumBayar').innerText = belumBayar;
                document.getElementById('totalMenunggu').innerText = menunggu;
                document.getElementById('totalLunas').innerText = lunas;

            });
        </script>




    </div>
@endsection
