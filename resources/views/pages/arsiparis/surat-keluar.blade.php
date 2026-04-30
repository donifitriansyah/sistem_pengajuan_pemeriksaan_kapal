@extends('layouts.app')

@section('content')
    <style>
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
    <div class="scorecard-container">
        <div class="scorecard total">
            <div class="scorecard-label">Total Pengajuan</div>
            <div class="scorecard-value" id="totalPengajuan">{{ $totalPengajuan }}</div>
        </div>
        <div class="scorecard belum-tagihan">
            <div class="scorecard-label">Belum Diagendakan</div>
            <div class="scorecard-value" id="totalBelumTagihan">{{ $totalBelumTagihan }}</div>
        </div>
        <div class="scorecard belum-bayar">
            <div class="scorecard-label">Butuh Verifikasi</div>
            <div class="scorecard-value" id="totalBelumBayar">{{ $totalBelumBayar }}</div>
        </div>
        <div class="scorecard menunggu">
            <div class="scorecard-label">Surat Masuk</div>
            <div class="scorecard-value" id="totalMenunggu">{{ $totalMenunggu }}</div>
        </div>
        <div class="scorecard lunas">
            <div class="scorecard-label">Surat Keluar</div>
            <div class="scorecard-value" id="totalLunas">{{ $totalLunas }}</div>
        </div>
    </div>
    <div class="content-card">

        <div class="content-header">
            <h2>Daftar Surat Keluar</h2>
        </div>

        <div class="filter-container">
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate">

            <button onclick="resetFilter()">Reset Filter</button>
            <button onclick="downloadTableAsExcel()">Download Excel</button>
        </div>

        <script>
            function resetFilter() {
                window.location.reload();
            }
        </script>

        <table id="tablePengajuan">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Tanggal Surat</th>
                    <th>Nomor Surat Keluar</th>
                    <th>Nomor Surat Pengajuan</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Jenis Dokumen</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($suratKeluar as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                        </td>

                        <td class="bold">
                            <strong>{{ $item->nomor_surat_keluar }}</strong>
                        </td>

                        <td>{{ $item->nomor_surat_pengajuan }}</td>

                        <td>{{ $item->pengajuan->nama_kapal ?? '-' }}</td>

                        <td>{{ $item->pengajuan->user->nama_perusahaan ?? '-' }}</td>

                        <td>
                            <span class="badge bg-primary">
                                {{ $item->pengajuan->jenis_dokumen ?? '-' }}
                            </span>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    <!-- Add SheetJS library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <script>
    $(document).ready(function() {

        // =========================
        // DATATABLE
        // =========================
        const table = $('#tablePengajuan').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,

            columnDefs: [{
                targets: 0,
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
                emptyTable: "Tidak ada data surat keluar"
            },

            // Auto numbering
            drawCallback: function(settings) {

                let api = this.api();

                api.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {

                    cell.innerHTML = i + 1 + settings._iDisplayStart;

                });
            }
        });

        // =========================
        // FILTER RANGE TANGGAL
        // =========================
        $.fn.dataTable.ext.search.push(function(settings, data) {

            // Pastikan hanya untuk tabel ini
            if (settings.nTable.id !== 'tablePengajuan') {
                return true;
            }

            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();

            // Kolom tanggal
            let tanggal = data[1] || '';

            if (!tanggal) {
                return true;
            }

            // Format dd-mm-yyyy
            let parts = tanggal.split('-');

            if (parts.length !== 3) {
                return true;
            }

            let day = parts[0];
            let month = parts[1];
            let year = parts[2];

            // Ubah jadi YYYYMMDD
            let rowDate = parseInt(year + month + day);

            // FILTER START DATE
            if (startDate) {

                let s = startDate.split('-');

                let start = parseInt(s[0] + s[1] + s[2]);

                if (rowDate < start) {
                    return false;
                }
            }

            // FILTER END DATE
            if (endDate) {

                let e = endDate.split('-');

                let end = parseInt(e[0] + e[1] + e[2]);

                if (rowDate > end) {
                    return false;
                }
            }

            return true;
        });

        // =========================
        // TRIGGER FILTER
        // =========================
        $('#startDate, #endDate').on('change', function() {
            table.draw();
        });

    });

    // =========================
    // RESET FILTER
    // =========================
    function resetFilter() {

        $('#startDate').val('');
        $('#endDate').val('');

        let table = $('#tablePengajuan').DataTable();

        table.search('');
        table.draw();
    }

    // =========================
    // EXPORT EXCEL
    // =========================
    function downloadTableAsExcel() {

        let table = $('#tablePengajuan').DataTable();

        // Simpan jumlah row awal
        let oldLength = table.page.len();

        // Tampilkan semua row hasil filter
        table.page.len(-1).draw();

        setTimeout(() => {

            // Export hasil filter
            const wb = XLSX.utils.table_to_book(
                document.getElementById('tablePengajuan'), {
                    sheet: "Surat Keluar"
                }
            );

            XLSX.writeFile(wb, "surat_keluar.xlsx");

            // Kembalikan pagination awal
            table.page.len(oldLength).draw();

        }, 300);
    }
</script>
@endsection
