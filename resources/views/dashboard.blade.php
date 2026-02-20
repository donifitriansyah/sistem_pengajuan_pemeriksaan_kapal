@extends('layouts.app')
@section('title')
@endsection
@section('header-title')
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

    <!-- Content Card -->
    <div class="content-card">
        <div class="content-header">
            <h2>Daftar Pengajuan</h2>
            <div class="header-actions">

                {{-- BUTTON --}}
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalPengajuan">
                    + Tambah Pengajuan
                </button>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif



                {{-- MODAL --}}
                <div class="modal fade" id="modalPengajuan" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <form action="{{ route('pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Pengajuan Pemeriksaan Kapal</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    {{-- Tanggal --}}
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Estimasi Pemeriksaan</label>
                                        <input type="date" name="tgl_estimasi_pemeriksaan" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Waktu Kedatangan Kapal</label>
                                        <input type="date" name="waktu_kedatangan_kapal" class="form-control" required>
                                    </div>


                                    {{-- Nama Kapal --}}
                                    <div class="mb-3">
                                        <label class="form-label">Nama Kapal</label>
                                        <input type="text" name="nama_kapal" class="form-control" required>
                                    </div>

                                    {{-- Lokasi --}}
                                    <div class="mb-3">
                                        <label class="form-label">Lokasi Kapal</label>
                                        <input type="text" name="lokasi_kapal" class="form-control" required>
                                    </div>

                                    {{-- Jenis Dokumen --}}
                                    <div class="mb-3">
                                        <label class="form-label">Jenis Dokumen</label>
                                        <select name="jenis_dokumen" class="form-select" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="PHQC">PHQC</option>
                                            <option value="SSCEC">SSCEC</option>
                                            <option value="COP">COP</option>
                                            <option value="P3K">P3K</option>
                                        </select>
                                    </div>

                                    {{-- Wilayah --}}
                                    <div class="mb-3">
                                        <label class="form-label">Wilayah Kerja</label>
                                        <select name="wilayah_kerja" class="form-select" required>
                                            <option value="">-- Pilih --</option>
                                            <option>Dwikora</option>
                                            <option>Kijing</option>
                                            <option>Padang Tikar</option>
                                            <option>Ketapang</option>
                                            <option>Kendawangan</option>
                                        </select>
                                    </div>

                                    {{-- Upload --}}
                                    <div class="mb-3">
                                        <label class="form-label">Surat Permohonan</label>
                                        <input type="file" name="surat_permohonan" class="form-control" required>
                                    </div>

                                </div>


                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">
                                        Simpan
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Filter -->
        <div class="filter-container">
            <div class="filter-group">
                <select id="filterTahun">
                    <option value="">Pilih Tahun</option>
                </select>
            </div>
            <div class="filter-group">
                <select id="filterBulan">
                    <option value="">Pilih Bulan</option>
                </select>
            </div>
            <div class="filter-group">
                <select id="filterStatus">
                    <option value="">Pilih Status</option>
                    <option value="Belum Ada Tagihan">Belum Ada Tagihan</option>
                    <option value="Belum Bayar">Belum Bayar</option>
                    <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                    <option value="Lunas">Lunas</option>
                </select>
            </div>
            <div class="filter-group">
                <select id="filterJenisDokumen">
                    <option value="">Pilih Dokumen</option>
                    <option value="PHQC">PHQC</option>
                    <option value="SSCEC">SSCEC</option>
                    <option value="COP">COP</option>
                </select>
            </div>
            <div class="filter-group">
                <select id="filterWilker">
                    <option value="">Pilih Wilker</option>
                    <option value="Dwikora">Dwikora</option>
                    <option value="Kijing">Kijing</option>
                    <option value="Padang Tikar">Padang Tikar</option>
                    <option value="Ketapang">Ketapang</option>
                    <option value="Kendawangan">Kendawangan</option>
                </select>
            </div>
            <button class="btn btn-outline" id="resetFilter">Reset</button>
            <button class="btn btn-success" id="exportExcel" onclick="exportToExcel()">📥 Excel</button>
        </div>

        <script>
            function exportToExcel() {
                // Ambil user_id dari session atau data lain
                const userId = {{ auth()->user()->id }}; // Ganti dengan cara mengambil user_id yang benar

                // Kirimkan request untuk mengunduh Excel
                window.location.href = `/export-excel?user_id=${userId}`;
            }
        </script>

        <table id="pengajuanTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Waktu Kedatangan Kapal</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Wilayah</th>
                    <th>Jenis Dokumen</th>
                    <th>Kode Bayar</th>
                    <th>Status Pengajuan</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengajuan as $item)
                    <tr data-status="{{ $item->penagihan ? $item->penagihan->status_bayar : 'belum_ada_tagihan' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->waktu_kedatangan_kapal)->format('Y-m-d') }}</td>
                        <td>{{ $item->nama_kapal }}</td>
                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $item->wilayah_kerja }}</td>
                        <td>{{ $item->jenis_dokumen }}</td>
                        <td><span class="badge bg-secondary">{{ $item->kode_bayar }}</span></td>
                        <td>{{ $item->status }}</td>
                        <td>
                            {{-- Aksi sesuai status --}}
                            @if (!$item->penagihan)
                                <span class="badge bg-secondary">Belum Ada Tagihan</span>
                            @elseif($item->penagihan->status_bayar === 'belum_bayar')
                                <span class="badge bg-warning text-dark mb-2">Belum Bayar</span>
                                <a href="{{ route('kwitansi.show', $item->penagihan->id) }}" target="_blank"
                                        class="btn btn-sm btn-success ">
                                        Lihat Invoice
                                    </a>
                                <div class="mt-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalBayar{{ $item->id }}">Bayar Tagihan</button>

                                </div>
                            @elseif($item->penagihan->status_bayar === 'menunggu')
                                <span class="badge bg-info">Menunggu Verifikasi</span>
                                <a href="{{ route('kwitansi.show', $item->penagihan->id) }}" target="_blank"
                                    class="btn btn-sm btn-success">
                                    Lihat Invoice
                                </a>
                            @elseif($item->penagihan->status_bayar === 'ditolak')
                                <span class="badge bg-danger">Pembayaran Ditolak</span>
                                <div class="mt-1">
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalBayar{{ $item->id }}">Upload Ulang Bukti</button>
                                </div>
                            @elseif($item->penagihan->status_bayar === 'diterima')
                                <span class="badge bg-success">Lunas</span>
                                <div class="mt-1">
                                    <a href="{{ route('invoice.show', $item->penagihan->id) }}" target="_blank"
                                        class="btn btn-sm btn-success mb-2">Lihat Kwitansi</a>
                                    
                                </div>
                            @endif

                            @if ($item->status === 'Ditolak')
                                <!-- Button to Edit "Ditolak" status -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editPengajuanModal{{ $item->id }}">
                                    Edit Pengajuan
                                </button>
                            @endif
                        </td>
                    </tr>
                    <div class="modal fade" id="editPengajuanModal{{ $item->id }}" tabindex="-1"
                        aria-labelledby="editPengajuanModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <form action="{{ route('user.pengajuan.update', $item->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPengajuanModalLabel{{ $item->id }}">Edit
                                            Pengajuan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <!-- Nama Kapal -->
                                        <div class="mb-3">
                                            <label class="form-label">Nama Kapal</label>
                                            <input type="text" name="nama_kapal" class="form-control"
                                                value="{{ $item->nama_kapal }}" required>
                                        </div>

                                        <!-- Tanggal Estimasi Pemeriksaan -->
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Estimasi Pemeriksaan</label>
                                            <input type="date" name="tgl_estimasi_pemeriksaan" class="form-control"
                                                value="{{ old('tgl_estimasi_pemeriksaan', $item->tgl_estimasi_pemeriksaan) }}"
                                                required>
                                        </div>

                                        <!-- Wilayah Kerja -->
                                        <div class="mb-3">
                                            <label class="form-label">Wilayah Kerja</label>
                                            <select name="wilayah_kerja" class="form-select" required>
                                                <option value="">-- Pilih Wilayah --</option>
                                                <option value="Dwikora"
                                                    {{ $item->wilayah_kerja === 'Dwikora' ? 'selected' : '' }}>Dwikora
                                                </option>
                                                <option value="Kijing"
                                                    {{ $item->wilayah_kerja === 'Kijing' ? 'selected' : '' }}>Kijing
                                                </option>
                                                <option value="Padang Tikar"
                                                    {{ $item->wilayah_kerja === 'Padang Tikar' ? 'selected' : '' }}>Padang
                                                    Tikar</option>
                                                <option value="Ketapang"
                                                    {{ $item->wilayah_kerja === 'Ketapang' ? 'selected' : '' }}>Ketapang
                                                </option>
                                                <option value="Kendawangan"
                                                    {{ $item->wilayah_kerja === 'Kendawangan' ? 'selected' : '' }}>
                                                    Kendawangan</option>
                                            </select>
                                        </div>

                                        <!-- Lokasi Kapal -->
                                        <div class="mb-3">
                                            <label class="form-label">Lokasi Kapal</label>
                                            <input type="text" name="lokasi_kapal" class="form-control"
                                                value="{{ $item->lokasi_kapal }}" required>
                                        </div>

                                        <!-- Jenis Dokumen -->
                                        <div class="mb-3">
                                            <label class="form-label">Jenis Dokumen</label>
                                            <select name="jenis_dokumen" class="form-select" required>
                                                <option value="PHQC"
                                                    {{ $item->jenis_dokumen === 'PHQC' ? 'selected' : '' }}>PHQC</option>
                                                <option value="SSCEC"
                                                    {{ $item->jenis_dokumen === 'SSCEC' ? 'selected' : '' }}>SSCEC</option>
                                                <option value="COP"
                                                    {{ $item->jenis_dokumen === 'COP' ? 'selected' : '' }}>COP</option>
                                                <option value="P3K"
                                                    {{ $item->jenis_dokumen === 'P3K' ? 'selected' : '' }}>P3K</option>
                                            </select>
                                        </div>

                                        <!-- Waktu Kedatangan Kapal -->
                                        <div class="mb-3">
                                            <label class="form-label">Waktu Kedatangan Kapal</label>
                                            <input type="datetime-local" name="waktu_kedatangan_kapal"
                                                class="form-control"
                                                value="{{ \Carbon\Carbon::parse($item->waktu_kedatangan_kapal)->format('Y-m-d\TH:i') }}"
                                                required>
                                        </div>

                                        <!-- Surat Permohonan (Upload) -->
                                        <div class="mb-3">
                                            <label class="form-label">Upload Surat Permohonan</label>
                                            <input type="file" name="surat_permohonan" class="form-control">
                                            @if ($item->surat_permohonan_dan_dokumen)
                                                <p>Current File: <a
                                                        href="{{ asset('storage/' . $item->surat_permohonan_dan_dokumen) }}"
                                                        target="_blank">View Current File</a></p>
                                            @endif
                                        </div>

                                        <!-- Alasan Penolakan (Keterangan) -->
                                        <div class="mb-3">
                                            <label for="keterangan" class="form-label">Alasan Penolakan</label>
                                            <textarea name="keterangan" class="form-control" rows="4" readonly>{{ $item->keterangan }}</textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>

        </table>


        <div id="emptyState" class="empty-state" style="display:none;">
            <p>📋 Belum ada pengajuan. Klik tombol "Tambah Pengajuan" untuk memulai.</p>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="pagination-container" style="display:none;"></div>
    </div>

    @foreach ($pengajuan as $item)
        @if ($item->penagihan && in_array($item->penagihan->status_bayar, ['belum_bayar', 'ditolak']))
            <div class="modal fade" id="modalBayar{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        {{-- INFO --}}


                        <form action="{{ route('user.pembayaran.store', $item->penagihan->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    {{ $item->penagihan->status_bayar === 'ditolak' ? 'Upload Ulang Bukti Pembayaran' : 'Bayar Tagihan' }}
                                </h5>
                                <button class="btn-close" data-bs-dismiss="modal"></button>
                            </div>


                            <div class="modal-body">
                                <strong>Kapal :</strong> {{ $item->nama_kapal }} <br>
                                <strong>Lokasi :</strong> {{ $item->lokasi_kapal }} <br>
                                <strong>Tanggal :</strong>
                                {{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }} <br>
                                <strong>Kode Bayar :</strong> {{ $item->kode_bayar }} <br>
                                <a href="{{ asset('storage/' . $item->surat_permohonan_dan_dokumen) }}"
                                    target="_blank">Dokumen Surat Permohonan</a> <br>

                                <strong style="color: red">
                                    Total Tagihan : Rp {{ number_format($item->penagihan->total_tarif, 0, ',', '.') }}
                                </strong> <br>


                                @if ($item->penagihan->status_bayar === 'ditolak')
                                    <div class="alert alert-danger">
                                        <strong>Pembayaran Ditolak</strong><br>

                                        <span class="d-block mt-1">
                                            Alasan:
                                            <em>
                                                {{ $item->penagihan->pembayaran->keterangan ?? 'Tidak ada keterangan' }}
                                            </em>
                                        </span>

                                        <hr class="my-2">

                                        <small>
                                            Silakan upload ulang bukti pembayaran yang sesuai.
                                        </small>
                                    </div>
                                @endif


                                <div class="mb-3 mt-2">
                                    <label class="form-label">Upload Bukti Pembayaran</label>
                                    <input type="file" name="bukti_bayar" class="form-control" required>
                                    <small style="color: red">
                                        * Mohon masukan kode bayar di berita transfer
                                    </small>
                                </div>


                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary">
                                    Kirim
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        @endif
    @endforeach
    <script>
        // Mencegah input manual pada datetime-local input
        document.querySelectorAll('input[type="date"]').forEach(function(input) {
            input.addEventListener('keydown', function(event) {
                event.preventDefault(); // Mencegah input manual
            });
        });
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {

            const table = $('#pengajuanTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                pageLength: 10,

                // Kolom Aksi & No tidak bisa di-sort
                columnDefs: [{
                    orderable: false
                }],

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
                    // Menambahkan nomor urut yang sesuai dengan data yang ditampilkan
                    var api = this.api();
                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1 + settings
                            ._iDisplayStart; // Menampilkan nomor urut sesuai halaman dan filter
                    });
                }
            });

        });
    </script>
    <script>
        $(document).ready(function() {


            // Populasi Tahun & Bulan
            var years = new Set();
            var months = new Set();
            table.column(1).data().each(function(date) {
                var d = new Date(date);
                years.add(d.getFullYear());
                months.add(d.getMonth() + 1);
            });
            years = Array.from(years).sort();
            months = Array.from(months).sort((a, b) => a - b);
            years.forEach(y => $('#filterTahun').append('<option value="' + y + '">' + y + '</option>'));
            months.forEach(m => $('#filterBulan').append('<option value="' + m + '">' + m + '</option>'));

            // Filter kustom
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var tahun = $('#filterTahun').val();
                var bulan = $('#filterBulan').val();
                var status = $('#filterStatus').val();
                var dokumen = $('#filterJenisDokumen').val();
                var wilker = $('#filterWilker').val();

                var rowDate = new Date(data[1]);
                var rowTahun = rowDate.getFullYear();
                var rowBulan = rowDate.getMonth() + 1;
                var rowStatus = data[6];
                var rowDokumen = data[5];
                var rowWilker = data[4];

                return (!tahun || rowTahun == tahun) &&
                    (!bulan || rowBulan == bulan) &&
                    (!status || rowStatus == status) &&
                    (!dokumen || rowDokumen == dokumen) &&
                    (!wilker || rowWilker == wilker);
            });

            $('#filterTahun, #filterBulan, #filterStatus, #filterJenisDokumen, #filterWilker').on('change',
                function() {
                    table.draw();
                });

            $('#resetFilter').on('click', function() {
                $('#filterTahun, #filterBulan, #filterStatus, #filterJenisDokumen, #filterWilker').val('');
                table.draw();
            });

            $('#exportExcel').on('click', function() {
                var wb = XLSX.utils.book_new();
                var ws = XLSX.utils.table_to_sheet(document.getElementById('pengajuanTable'));
                XLSX.utils.book_append_sheet(wb, ws, 'Pengajuan');
                XLSX.writeFile(wb, 'pengajuan.xlsx');
            });
        });
    </script>
    <script>
        function hitungScorecard() {
            // Reset semua
            const totalPengajuan = document.getElementById('totalPengajuan');
            const totalBelumTagihan = document.getElementById('totalBelumTagihan');
            const totalBelumBayar = document.getElementById('totalBelumBayar');
            const totalMenunggu = document.getElementById('totalMenunggu');
            const totalLunas = document.getElementById('totalLunas');

            let countTotal = 0,
                countBelumTagihan = 0,
                countBelumBayar = 0,
                countMenunggu = 0,
                countLunas = 0;

            document.querySelectorAll('table tbody tr').forEach(row => {
                countTotal++;
                const status = row.dataset.status;

                switch (status) {
                    case 'belum_ada_tagihan':
                        countBelumTagihan++;
                        break;
                    case 'belum_bayar':
                        countBelumBayar++;
                        break;
                    case 'menunggu':
                        countMenunggu++;
                        break;
                    case 'diterima':
                        countLunas++;
                        break;
                    default:
                        break;
                }
            });

            totalPengajuan.textContent = countTotal;
            totalBelumTagihan.textContent = countBelumTagihan;
            totalBelumBayar.textContent = countBelumBayar;
            totalMenunggu.textContent = countMenunggu;
            totalLunas.textContent = countLunas;
        }

        // Jalankan saat load
        document.addEventListener('DOMContentLoaded', hitungScorecard);
    </script>
@endsection
