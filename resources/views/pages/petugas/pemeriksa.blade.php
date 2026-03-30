@extends('layouts.app')
@section('title')
    Petugas Pemeriksa
@endsection
@section('content')
    <style>
        .content-card {
            /* Untuk menghindari tabel meluap ke luar card */
            padding: 20px;
        }

        #pengajuanTable {
            width: 100% !important;
            table-layout: fixed;
        }

        #pengajuanTable th,
        #pengajuanTable td {
            white-space: normal !important;
            word-break: break-word;
            font-size: 11px;
            /* opsional biar muat */
        }

        .table {
            overflow-x: auto;
            /* Agar teks tidak meluap */
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* Untuk scroll lebih halus pada perangkat mobile */
        }

        .table td,
        .table th {
            word-wrap: break-word;
            /* Membungkus teks jika terlalu panjang */
            overflow-wrap: break-word;
            /* Membungkus teks jika diperlukan */
            white-space: normal;
            /* Pastikan teks dibungkus dengan baik */
        }

        /* Menambahkan padding yang lebih baik pada kolom */
        .table td {
            padding: 8px;
        }

        .filter-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .filter-container label {
            margin-right: 10px;
        }

        .filter-container input {
            padding: 5px;
            margin-right: 10px;
        }

        .filter-container button {
            padding: 6px 12px;
            margin-left: 10px;
            cursor: pointer;
            font-size: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .filter-container button:hover {
            background-color: #0056b3;
        }
    </style>
    <div class="content-card">
        <div class="content-header">
            <h2>Daftar Petugas Pemeriksa</h2>

        </div>

        <div class="filter-container">
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate">

            <label for="filterTarif">Jenis Tarif:</label>
            <select id="filterTarif">
                <option value="">Semua</option>
                <option value="170000">Dalam Kota < 8 Jam</option>
                <option value="320000">Dalam Kota > 8 Jam</option>
                <option value="380000">Luar Kota</option>
            </select>

            <label for="filterDokumen">Jenis Dokumen:</label>
            <select id="filterDokumen">
                <option value="">Semua</option>
                <option value="PHQC">PHQC</option>
                <option value="SSCEC">SSCEC</option>
                <option value="COP">COP</option>
                <option value="P3K">P3K</option>
            </select>

            <button onclick="resetFilter()">Reset Filter</button>
            <button onclick="downloadTableAsExcel()">Download Excel</button>


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

        <div class="table-responsive"> <!-- Membungkus tabel dengan .table-responsive -->
            <table id="pengajuanTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pemeriksaan</th>
                        <th>Surat Tugas</th>
                        <th>Nama Kapal</th>
                        <th>Nama Perusahaan</th>
                        <th>Petugas 1</th>
                        <th>Petugas 2</th>
                        <th>Petugas 3</th>
                        <th>Jenis Dokumen</th>
                        <th>Lokasi Pemeriksaan</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Jenis Tarif</th>
                        <th>Biaya</th>

                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuan as $key => $item)
                        <tr data-kode-bayar="{{ $item->kode_bayar }}"
                            data-tanggal-bayar="{{ optional($item->penagihan->pembayaran)->tanggal_bayar }}">

                            <td></td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}</td>
                            <td>{{ $item->agendaSuratPengajuan->nomor_surat_keluar ?? '-' }}</td>
                            <td>{{ $item->nama_kapal }}</td>
                            <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                            <!-- Display Petugas -->
                            <td>
                                {{ isset($item->penagihan->petugas[0]) ? $item->penagihan->petugas[0]->nama_petugas : '-' }}
                            </td>
                            <td>
                                {{ isset($item->penagihan->petugas[1]) ? $item->penagihan->petugas[1]->nama_petugas : '-' }}
                            </td>
                            <td>
                                {{ isset($item->penagihan->petugas[2]) ? $item->penagihan->petugas[2]->nama_petugas : '-' }}
                            </td>
                            <td>{{ $item->jenis_dokumen }}</td>
                            <td>{{ $item->lokasi_kapal }}</td>
                            <td>
                                @if (isset($item->penagihan->waktu_mulai))
                                    {{ \Carbon\Carbon::parse($item->penagihan->waktu_mulai)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if (isset($item->penagihan->waktu_selesai))
                                    {{ \Carbon\Carbon::parse($item->penagihan->waktu_selesai)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                @php
                                    $tarif = $item->penagihan->jenis_tarif ?? 0;
                                @endphp

                                @if ($tarif == 170000)
                                Dalam Kota < 8 Jam @elseif($tarif == 320000) Dalam Kota> 8 Jam
                                    @elseif($tarif == 380000)
                                        Luar Kota
                                    @else
                                        -
                                @endif
                            </td>

                            <!-- Display Jenis Tarif from Penagihan -->
                            <td>
                                {{ number_format($item->penagihan->total_tarif ?? 0, 0, ',', '.') }}
                            </td>


                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->id }}"
                                    data-id="{{ $item->id }}">Edit</button>
                            </td>
                        </tr>

                        <!-- Modal for Editing -->
                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('pengajuan.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel{{ $item->id }}">Edit Pengajuan
                                                Pemeriksaan Kapal</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <!-- Nama Kapal -->
                                            <div class="mb-3">
                                                <label for="nama_kapal" class="form-label">Nama Kapal</label>
                                                <input type="text" class="form-control" id="nama_kapal" name="nama_kapal"
                                                    value="{{ $item->nama_kapal }}" required>
                                            </div>

                                            <!-- Lokasi Kapal -->
                                            <div class="mb-3">
                                                <label for="lokasi_kapal" class="form-label">Lokasi Kapal</label>
                                                <input type="text" class="form-control" id="lokasi_kapal"
                                                    name="lokasi_kapal" value="{{ $item->lokasi_kapal }}" required>
                                            </div>

                                            <!-- Jenis Dokumen -->
                                            <div class="mb-3">
                                                <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                                                <select class="form-control" id="jenis_dokumen" name="jenis_dokumen"
                                                    required>
                                                    <option value="PHQC"
                                                        {{ $item->jenis_dokumen == 'PHQC' ? 'selected' : '' }}>PHQC
                                                    </option>
                                                    <option value="SSCEC"
                                                        {{ $item->jenis_dokumen == 'SSCEC' ? 'selected' : '' }}>SSCEC
                                                    </option>
                                                    <option value="COP"
                                                        {{ $item->jenis_dokumen == 'COP' ? 'selected' : '' }}>COP</option>
                                                    <option value="P3K"
                                                        {{ $item->jenis_dokumen == 'P3K' ? 'selected' : '' }}>P3K</option>
                                                </select>
                                            </div>

                                            <!-- Petugas 1 -->
                                            <div class="mb-3">
                                                <label for="petugas1" class="form-label">Petugas 1</label>
                                                <select class="form-control" id="petugas1" name="petugas1" required>
                                                    <option value="">Pilih Petugas</option>
                                                    @foreach ($petugas as $petugasItem)
                                                        <option value="{{ $petugasItem->id }}"
                                                            {{ isset($item->penagihan->petugas[0]) && $item->penagihan->petugas[0]->id == $petugasItem->id ? 'selected' : '' }}>
                                                            {{ $petugasItem->nama_petugas }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Petugas 2 -->
                                            <div class="mb-3">
                                                <label for="petugas2" class="form-label">Petugas 2</label>
                                                <select class="form-control" id="petugas2" name="petugas2" required>
                                                    <option value="">Pilih Petugas</option>
                                                    @foreach ($petugas as $petugasItem)
                                                        <option value="{{ $petugasItem->id }}"
                                                            {{ isset($item->penagihan->petugas[1]) && $item->penagihan->petugas[1]->id == $petugasItem->id ? 'selected' : '' }}>
                                                            {{ $petugasItem->nama_petugas }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Petugas 3 -->
                                            @if (isset($item->penagihan->petugas[2]))
                                                <div class="mb-3">
                                                    <label for="petugas3" class="form-label">Petugas 3</label>
                                                    <select class="form-control" id="petugas3" name="petugas3">
                                                        <option value="">Pilih Petugas</option>
                                                        @foreach ($petugas as $petugasItem)
                                                            <option value="{{ $petugasItem->id }}"
                                                                {{ isset($item->penagihan->petugas[2]) && $item->penagihan->petugas[2]->id == $petugasItem->id ? 'selected' : '' }}>
                                                                {{ $petugasItem->nama_petugas }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update Pengajuan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="emptyPengajuan" class="empty-state" style="display:none;">
            <p>✅ Semua pengajuan sudah diagendakan!</p>
        </div>
    </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <script>
        $('#startDate, #endDate, #filterTarif, #filterDokumen').on('change', function() {
            $('#pengajuanTable').DataTable().draw();
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {

            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            const tarif = $('#filterTarif').val();
            const dokumen = $('#filterDokumen').val();

            const tanggal = data[1]; // kolom tanggal
            const tarifText = data[12]; // kolom jenis tarif
            const dokumenText = data[8]; // 🔥 kolom jenis dokumen

            let valid = true;

            // =====================
            // FILTER TANGGAL
            // =====================
            if (tanggal) {
                const parts = tanggal.split('-');
                const rowDate = new Date(parts[2], parts[1] - 1, parts[0]);

                if (startDate && rowDate < new Date(startDate)) valid = false;
                if (endDate && rowDate > new Date(endDate)) valid = false;
            }

            // =====================
            // FILTER TARIF
            // =====================
            if (tarif) {
                if (tarif == "170000" && tarifText !== "Dalam Kota < 8 Jam") valid = false;
                if (tarif == "320000" && tarifText !== "Dalam Kota > 8 Jam") valid = false;
                if (tarif == "380000" && tarifText !== "Luar Kota") valid = false;
            }

            // =====================
            // FILTER JENIS DOKUMEN 🔥
            // =====================
            if (dokumen) {
                if (dokumenText !== dokumen) valid = false;
            }

            return valid;
        });

        $(document).ready(function() {
            const table = $('#pengajuanTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                columnDefs: [{

                        searchable: false
                    },
                    {
                        orderable: false,
                        targets: 0
                    }
                ],
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
                    // Ensure the row numbers are updated after each table draw
                    resetRowNumbers
                        (); // This ensures that row numbers are recalculated every time the table is drawn
                }
            });

            // Redraw table and reset row numbers when applying filters
            $('#startDate, #endDate').on('change', function() {

                table.draw(); // Trigger the row number recalculation after filtering
            });
            // 🔥 AUTO NUMBERING (INI KUNCINYA)
            table.on('order.dt search.dt draw.dt', function() {
                let i = 1;

                table.rows({
                    search: 'applied'
                }).every(function(rowIdx) {
                    let node = this.node();
                    $('td:eq(0)', node).html(i++);
                });
            }).draw();
        });
        $('#filterTarif').on('change', function() {

        });
    </script>
    <script>
        $('#pengajuanTable thead th').each(function(index) {

            if (index !== 15) { // skip kolom aksi saja
                headers.push($(this).text().trim());
            }
        });



        // Function to manually reset row numbers
        function resetRowNumbers() {
            const rows = document.querySelectorAll("#pengajuanTable tbody tr");
            let rowNumber = 1;

            rows.forEach(row => {

            });
        }

        function resetFilter() {

    const table = $('#pengajuanTable').DataTable();

    // reset semua input
    $('#startDate').val('');
    $('#endDate').val('');
    $('#filterTarif').val('');
    $('#filterDokumen').val('');

    // redraw ulang (filter otomatis kosong)
    table.draw();

    // kembali ke halaman pertama
    table.page('first').draw('page');
}

        function downloadTableAsExcel() {

            const table = $('#pengajuanTable').DataTable();

            // ambil hanya data yang sudah kena filter
            const allRows = table.rows({
                search: 'applied'
            }).nodes();

            let data = [];

            // HEADER
            let headers = [
                "No",
                "Tanggal Pemeriksaan",
                "Surat Tugas",
                "Nama Kapal",
                "Nama Perusahaan",
                "Petugas 1",
                "Petugas 2",
                "Petugas 3",
                "Jenis Dokumen",
                "Lokasi Pemeriksaan",
                "Waktu Mulai",
                "Waktu Selesai",
                "Jenis Tarif",
                "Biaya",
                "Kode Bayar",
                "Tanggal Bayar"
            ];

            data.push(headers);

            let tempData = [];

            // =========================
            // AMBIL DATA DARI TABLE
            // =========================
            $(allRows).each(function(index, row) {

                let rowData = [];

                $(row).find('td').each(function(i) {
                    if (i !== 14) { // skip kolom aksi
                        rowData.push($(this).text().trim());
                    }
                });

                // ambil attribute tambahan
                let kodeBayar = $(row).data('kode-bayar') || '-';
                let tanggalBayar = $(row).data('tanggal-bayar');

                if (tanggalBayar) {
                    let d = new Date(tanggalBayar);

                    let tanggal = d.toLocaleDateString('id-ID'); // tanggal
                    let jam = d.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    }); // jam tanpa detik

                    tanggalBayar = tanggal + ' ' + jam;
                } else {
                    tanggalBayar = '-';
                }

                rowData.push(kodeBayar);
                rowData.push(tanggalBayar);

                tempData.push(rowData);
            });

            // =========================
            // SORT DESC (TERBARU DI ATAS)
            // =========================
            tempData.sort(function(a, b) {

                // format dd-mm-yyyy
                let dateA = a[1].split('-');
                let dateB = b[1].split('-');

                let newA = new Date(dateA[2], dateA[1] - 1, dateA[0]);
                let newB = new Date(dateB[2], dateB[1] - 1, dateB[0]);

                return newA - newB; // 🔥 DESC (terbaru dulu)
            });

            // =========================
            // RESET NOMOR
            // =========================
            tempData.forEach(function(row, index) {
                row[0] = index + 1;
                data.push(row);
            });

            // =========================
            // EXPORT KE EXCEL
            // =========================
            const ws = XLSX.utils.aoa_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Pengajuan');

            XLSX.writeFile(wb, 'Pengajuan_Pemeriksaan.xlsx');
        }
    </script>
@endsection
