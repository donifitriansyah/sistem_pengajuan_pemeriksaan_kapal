@extends('layouts.app')

@section('title')
    Dashboard Admin
@endsection

@section('header-title')
    Dashboard Admin
@endsection

@section('content')
    <div id="tab-pengajuan" class="content-section active">
        <!-- Scorecard Container -->
        <div class="scorecard-container">
            <div class="scorecard total">
                <div class="scorecard-label">Total Pengajuan</div>
                <div class="scorecard-value" id="totalPengajuan">{{ $pengajuanPemeriksaans->count() }}</div>
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
                <h2>Daftar Pengajuan Pemeriksaan Kapal</h2>
                <div class="header-actions">
                </div>
            </div>

            <!-- Filter Container -->
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
                <button class="btn btn-outline" onclick="resetFilter()">Reset</button>



            </div>

            <!-- Data Table for Pengajuan -->
            <table id="tablePengajuan" class="display">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pemeriksaan</th>
                        <th>Nama Kapal</th>
                        <th>Nama Perusahaan</th>
                        <th>Lokasi Pemeriksaan</th>
                        <th>Wilayah Kerja</th>
                        <th>Jenis Dokumen</th>
                        <th>Kode Bayar</th>
                        <th>Status Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengajuanPemeriksaans as $index => $pengajuan)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($pengajuan->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}
                            </td>
                            <td>{{ $pengajuan->nama_kapal }}</td>
                            <td>{{ $pengajuan->user->nama_perusahaan ?? '-' }}</td>

                            <td>{{ $pengajuan->lokasi_kapal }}</td>
                            <td>{{ $pengajuan->wilayah_kerja }}</td>
                            <td>{{ $pengajuan->jenis_dokumen }}</td>
                            <td>{{ $pengajuan->kode_bayar }}</td>
                            <td>
                                @if (!$pengajuan->penagihan)
                                    Belum Ada Tagihan
                                @else
                                    @if ($pengajuan->penagihan->status_bayar === 'belum_bayar')
                                        Belum Bayar
                                    @elseif ($pengajuan->penagihan->status_bayar === 'menunggu')
                                        Menunggu Verifikasi
                                    @elseif ($pengajuan->penagihan->status_bayar === 'ditolak')
                                        Ditolak
                                    @elseif ($pengajuan->penagihan->status_bayar === 'diterima')
                                        Lunas
                                    @endif
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm">View</button>
                                <button class="btn btn-warning btn-sm">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Empty State -->
            <div id="emptyPengajuan" class="empty-state"
                style="display: {{ $pengajuanPemeriksaans->isEmpty() ? 'block' : 'none' }};">
                <p>Belum ada pengajuan untuk wilker Anda</p>
            </div>
        </div>
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

                // Kolom Aksi & No tidak bisa di-sort
                columnDefs: [{
                    targets: [0, 8],
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
                }
            });

        });
    </script>
    <script>
        function applyFilter() {
            const tahun = $('#filterTahun').val();
            const bulan = $('#filterBulan').val();
            const perusahaan = $('#filterPerusahaan').val();
            const jenis = $('#filterJenisDokumen').val();

            $('#tablePengajuan tbody tr').each(function() {
                const tanggal = $(this).find('td:eq(1)').text(); // dd-mm-yyyy
                const perusahaanText = $(this).find('td:eq(3)').text();
                const jenisText = $(this).find('td:eq(5)').text();

                let show = true;

                if (tanggal) {
                    const parts = tanggal.split('-');
                    const rowBulan = parts[1];
                    const rowTahun = parts[2];

                    if (tahun && rowTahun !== tahun) show = false;
                    if (bulan && rowBulan !== bulan) show = false;
                }

                if (perusahaan && !perusahaanText.includes(perusahaan)) show = false;
                if (jenis && !jenisText.includes(jenis)) show = false;

                $(this).toggle(show);
            });
        }

        $('#filterTahun, #filterBulan, #filterPerusahaan, #filterJenisDokumen').on('change', applyFilter);

        function resetFilter() {
            $('#filterTahun, #filterBulan, #filterPerusahaan, #filterJenisDokumen').val('');
            $('#tablePengajuan tbody tr').show();
        }
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the filter elements and table rows
            const filterTahun = document.getElementById('filterTahun');
            const filterBulan = document.getElementById('filterBulan');
            const filterPerusahaan = document.getElementById('filterPerusahaan');
            const filterJenisDokumen = document.getElementById('filterJenisDokumen');

            const rows = document.querySelectorAll('table tbody tr');

            /* =========================
               ISI OPTION TAHUN & BULAN
            ========================== */
            const tahunSet = new Set();
            const bulanSet = new Set();
            const perusahaanSet = new Set();

            // Extract years, months, and companies from table rows
            rows.forEach(row => {
                const tanggal = row.cells[1]?.innerText.trim(); // dd-mm-yyyy
                const perusahaan = row.cells[3]?.innerText.trim();

                if (tanggal) {
                    const [dd, mm, yyyy] = tanggal.split('-');
                    tahunSet.add(yyyy);
                    bulanSet.add(mm);
                }

                if (perusahaan && perusahaan !== '-') {
                    perusahaanSet.add(perusahaan);
                }
            });

            // Add options for Tahun, Bulan, and Perusahaan filter
            [...tahunSet].sort().forEach(tahun => {
                filterTahun.innerHTML += `<option value="${tahun}">${tahun}</option>`;
            });

            [...bulanSet].sort().forEach(bulan => {
                filterBulan.innerHTML += `<option value="${bulan}">${bulan}</option>`;
            });

            [...perusahaanSet].sort().forEach(p => {
                filterPerusahaan.innerHTML += `<option value="${p}">${p}</option>`;
            });

            /* =========================
               APPLY FILTER
            ========================== */
            function applyFilter() {
                const tahun = filterTahun.value;
                const bulan = filterBulan.value;
                const perusahaan = filterPerusahaan.value.toLowerCase();
                const jenis = filterJenisDokumen.value.toLowerCase();

                // Loop through each row and check if it matches the selected filters
                rows.forEach(row => {
                    const tanggalText = row.cells[1].innerText; // dd-mm-yyyy
                    const perusahaanText = row.cells[3].innerText.toLowerCase();
                    const jenisText = row.cells[5].innerText.toLowerCase();

                    const [dd, mm, yyyy] = tanggalText.split('-');

                    let show = true;

                    if (tahun && yyyy !== tahun) show = false;
                    if (bulan && mm !== bulan) show = false;
                    if (perusahaan && !perusahaanText.includes(perusahaan)) show = false;
                    if (jenis && !jenisText.includes(jenis)) show = false;

                    // Show or hide the row based on the filtering conditions
                    row.style.display = show ? '' : 'none';
                });
            }

            // Add event listeners to filters
            filterTahun.addEventListener('change', applyFilter);
            filterBulan.addEventListener('change', applyFilter);
            filterPerusahaan.addEventListener('change', applyFilter);
            filterJenisDokumen.addEventListener('change', applyFilter);
        });

        /* =========================
           RESET FILTER
        ========================= */
        function resetFilter() {
            // Reset all filter values
            document.getElementById('filterTahun').value = '';
            document.getElementById('filterBulan').value = '';
            document.getElementById('filterPerusahaan').value = '';
            document.getElementById('filterJenisDokumen').value = '';

            // Reset the display of all rows
            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = '';
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('table tbody tr');

            // Initialize counters for each category
            let totalPengajuan = 0;
            let totalBelumTagihan = 0;
            let totalBelumBayar = 0;
            let totalMenunggu = 0;
            let totalLunas = 0;

            // Loop through each row to count the statuses
            rows.forEach(row => {
                totalPengajuan++;

                // Get the status value from the row (assuming status is in the 9th column)
                const status = row.cells[8]?.innerText.trim().toLowerCase();

                // Increment the appropriate counter based on status
                if (status === 'belum ada tagihan') {
                    totalBelumTagihan++;
                } else if (status === 'belum bayar') {
                    totalBelumBayar++;
                } else if (status === 'menunggu verifikasi') {
                    totalMenunggu++;
                } else if (status === 'lunas') {
                    totalLunas++;
                }
            });

            // Update the scorecard values in the DOM
            document.getElementById('totalPengajuan').innerText = totalPengajuan;
            document.getElementById('totalBelumTagihan').innerText = totalBelumTagihan;
            document.getElementById('totalBelumBayar').innerText = totalBelumBayar;
            document.getElementById('totalMenunggu').innerText = totalMenunggu;
            document.getElementById('totalLunas').innerText = totalLunas;
        });
    </script>
@endsection
