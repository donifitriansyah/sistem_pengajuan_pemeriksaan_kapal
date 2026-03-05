@extends('layouts.app')
@section('title')
Semua Data Pengajuan
@endsection
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
            <div class="filter-group">
                <label>Status</label>
                <select id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Belum Ada Tagihan">Belum Ada Tagihan</option>
                    <option value="Belum Bayar">Belum Bayar</option>
                    <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                    <option value="Ditolak">Ditolak</option>
                    <option value="Lunas">Lunas</option>
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
                    <th>Surat Permohonan</th>
                    <th>Kode Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengajuan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}</td>
                        <td>{{ $item->nama_kapal }}</td>
                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $item->lokasi_kapal }}</td>
                        <td>{{ $item->jenis_dokumen }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $item->surat_permohonan_dan_dokumen) }}" target="_blank"
                                class="btn btn-sm btn-info">
                                Lihat File
                            </a>
                        </td>
                        <td><span class="badge bg-secondary">{{ $item->kode_bayar }}</span>
                        </td>

                        <td>

                            @if (!$item->penagihan)
                                <span class="badge bg-secondary">Belum Ada Tagihan</span>
                                <br>
                            @else
                                {{-- STATUS --}}
                                @if ($item->penagihan->status_bayar === 'belum_bayar')
                                    <span class="badge bg-danger">Belum Bayar</span>
                                @elseif ($item->penagihan->status_bayar === 'menunggu')
                                    <span class="badge bg-primary">Menunggu Verifikasi</span>
                                @elseif ($item->penagihan->status_bayar === 'ditolak')
                                    <span class="badge bg-dark">Ditolak</span>
                                @elseif ($item->penagihan->status_bayar === 'diterima')
                                    <span class="badge bg-success">Lunas</span>
                                @endif

                                {{-- AKSI --}}
                                <div class="">
                                    @if ($item->penagihan->status_bayar === 'belum_bayar')
                                        {{-- <span class="badge bg-danger">Menunggu Pembayaran</span> --}}
                                    @elseif($item->penagihan->status_bayar === 'diterima')
                                        <a href="{{ route('invoice.show', $item->penagihan->id) }}" target="_blank"
                                            class="btn btn-sm btn-success">
                                            Lihat Kwitansi
                                        </a>
                                    @endif

                                </div>
                            @endif
                            @if ($item->penagihan?->pembayaran?->file)
                                <br>
                                <a href="{{ asset('storage/' . $item->penagihan->pembayaran->file) }}" target="_blank"
                                    class="btn btn-sm btn-success">
                                    Lihat Bukti Bayar
                                </a>
                            @else
                                <br>
                                <span class="text-muted">Belum Upload Bukti</span>
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
                        } // kolom Aksi tidak bisa sort
                    ]
                });

                // ============================
                // POPULASI FILTER
                // ============================
                var years = new Set();
                var months = new Set();
                var perusahaanSet = new Set();

                table.rows().every(function() {
                    var data = this.data();

                    // tanggal format dd-mm-yyyy
                    var parts = data[1].split('-');
                    if (parts.length === 3) {
                        years.add(parts[2]);
                        months.add(parts[1]);
                    }

                    perusahaanSet.add(data[3]);
                });

                Array.from(years).sort().forEach(y =>
                    $('#filterTahun').append(`<option value="${y}">${y}</option>`)
                );

                Array.from(months).sort().forEach(m =>
                    $('#filterBulan').append(`<option value="${m}">${m}</option>`)
                );

                Array.from(perusahaanSet).sort().forEach(p =>
                    $('#filterPerusahaan').append(`<option value="${p}">${p}</option>`)
                );

                // ============================
                // CUSTOM FILTER (SEMUA DIGABUNG)
                // ============================
                $.fn.dataTable.ext.search.push(function(settings, data) {

                    var tahun = $('#filterTahun').val();
                    var bulan = $('#filterBulan').val();
                    var perusahaan = $('#filterPerusahaan').val();
                    var dokumen = $('#filterJenisDokumen').val();
                    var status = $('#filterStatus').val();

                    var tanggal = data[1] || '';
                    var perusahaanRow = data[3] || '';
                    var dokumenRow = data[5] || '';
                    var statusRow = data[7] || ''; // ✅ kolom aksi (status ada di sini)

                    var parts = tanggal.split('-');
                    var rowTahun = parts[2];
                    var rowBulan = parts[1];

                    if (tahun && rowTahun !== tahun) return false;
                    if (bulan && rowBulan !== bulan) return false;
                    if (perusahaan && perusahaanRow !== perusahaan) return false;
                    if (dokumen && dokumenRow !== dokumen) return false;

                    // Filter Status (pakai includes karena ada button + text lain)
                    if (status && !statusRow.includes(status)) return false;

                    return true;
                });

                // ============================
                // TRIGGER FILTER
                // ============================
                $('#filterTahun, #filterBulan, #filterPerusahaan, #filterJenisDokumen, #filterStatus')
                    .on('change', function() {
                        table.draw();
                    });

                // ============================
                // RESET FILTER
                // ============================
                $('#resetFilter').on('click', function() {
                    $('#filterTahun').val('');
                    $('#filterBulan').val('');
                    $('#filterPerusahaan').val('');
                    $('#filterJenisDokumen').val('');
                    $('#filterStatus').val('');
                    table.search('').draw();
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
