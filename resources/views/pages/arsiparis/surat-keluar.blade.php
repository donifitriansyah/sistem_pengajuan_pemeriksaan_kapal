@extends('layouts.app')

@section('content')
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
                    <option value="P3K">P3K</option>
                </select>
            </div>
            <button class="btn btn-outline" onclick="resetFilter()">Reset</button>



        </div>

        <table id="tablePengajuan" >
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
                @forelse ($suratKeluar as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d-m-Y') }}
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
        document.addEventListener('DOMContentLoaded', function() {

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

                rows.forEach(row => {
                    const tanggalText = row.cells[1].innerText; // dd-mm-yyyy
                    const perusahaanText = row.cells[3].innerText.toLowerCase();
                    const jenisText = row.cells[6].innerText.toLowerCase();

                    const [dd, mm, yyyy] = tanggalText.split('-');

                    let show = true;

                    if (tahun && yyyy !== tahun) show = false;
                    if (bulan && mm !== bulan) show = false;
                    if (perusahaan && !perusahaanText.includes(perusahaan)) show = false;
                    if (jenis && !jenisText.includes(jenis)) show = false;

                    row.style.display = show ? '' : 'none';
                });
            }

            filterTahun.addEventListener('change', applyFilter);
            filterBulan.addEventListener('change', applyFilter);
            filterPerusahaan.addEventListener('change', applyFilter);
            filterJenisDokumen.addEventListener('change', applyFilter);
        });

        /* =========================
           RESET FILTER
        ========================= */
        function resetFilter() {
            document.getElementById('filterTahun').value = '';
            document.getElementById('filterBulan').value = '';
            document.getElementById('filterPerusahaan').value = '';
            document.getElementById('filterJenisDokumen').value = '';

            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = '';
            });
        }
    </script>
@endsection
