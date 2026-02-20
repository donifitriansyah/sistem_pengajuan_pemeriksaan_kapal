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
            <h2>Daftar Surat Masuk</h2>
        </div>

        <div class="filter-container">
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate">

            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate">

            <button onclick="filterTable()">Filter</button>
            <button onclick="resetFilter()">Reset Filter</button>
            <button onclick="downloadTableAsExcel()">Download Excel</button>
        </div>

        <table id="tablePengajuan">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Tanggal Surat</th>
                    <th>Nomor Surat Pengajuan</th>
                    <th>Nomor Surat Masuk</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Jenis Dokumen</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($suratKeluar as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- <td>
                        <strong>{{ $item->nomor_surat_keluar }}</strong>
                    </td> --}}

                        <td>
                            {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d-m-Y') }}
                        </td>
                        <td>{{ $item->nomor_surat_pengajuan }}</td>

                        <td><strong>{{ $item->nomor_surat_masuk }}</strong></td>

                        <td>{{ $item->pengajuan->nama_kapal ?? '-' }}</td>

                        <td>{{ $item->pengajuan->user->nama_perusahaan ?? '-' }}</td>

                        <td>
                            <span class="badge bg-primary">
                                {{ $item->pengajuan->jenis_dokumen ?? '-' }}
                            </span>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            Belum ada surat keluar
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
    <!-- Add SheetJS library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <script>
        // Function to filter the table based on date range
        // Function to filter the table based on date range
        function filterTable() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const rows = document.querySelectorAll("#tablePengajuan tbody tr");

            rows.forEach(row => {
                const dateCell = row.cells[1].innerText.trim(); // Get the 'Tanggal Surat' cell content
                const rowDate = new Date(dateCell.split('-').reverse().join(
                '-')); // Convert to Date object (dd-mm-yyyy to yyyy-mm-dd)

                const filterStartDate = startDate ? new Date(startDate) : null;
                const filterEndDate = endDate ? new Date(endDate) : null;

                let showRow = true;

                // Show or hide the row based on date range
                if (filterStartDate && rowDate < filterStartDate) {
                    showRow = false;
                }

                if (filterEndDate && rowDate > filterEndDate) {
                    showRow = false;
                }

                row.style.display = showRow ? '' : 'none';
            });

            // Manually reset row numbers after the filter is applied
            resetRowNumbers();
        }

        // Function to manually reset row numbers
        function resetRowNumbers() {
            const rows = document.querySelectorAll("#tablePengajuan tbody tr");
            let rowNumber = 1;

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    row.cells[0].innerText = rowNumber; // Update the row number in the first column
                    rowNumber++;
                }
            });
        }

        // Function to download the table as an Excel file using SheetJS
        function downloadTableAsExcel() {
            const wb = XLSX.utils.table_to_book(document.getElementById('tablePengajuan'), {
                sheet: "Sheet1"
            });
            XLSX.writeFile(wb, "surat_masuk.xlsx");
        }

        $(document).ready(function() {
            const table = $('#tablePengajuan').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                pageLength: 10,
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
                    // Ensure the row numbers are updated after each table draw
                    resetRowNumbers
                (); // This ensures that row numbers are recalculated every time the table is drawn
                }
            });

            // Redraw table and reset row numbers when applying filters
            $('#startDate, #endDate').on('change', function() {
                filterTable();
                table.draw(); // Trigger the row number recalculation after filtering
            });
        });
    </script>
@endsection
