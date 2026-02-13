@extends('layouts.app')
@section('content')
    <div class="content-card">
        <div class="content-header">
            <h2>Daftar Pengajuan Sudah Diagendakan</h2>
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
                </select>
            </div>
            <button class="btn btn-outline" onclick="resetFilter()">Reset</button>



        </div>

        <table id="tablePengajuan">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Tanggal Pemeriksaan</th>
                    <th>Nomor Surat Masuk</th>
                    <th>Nama Kapal</th>
                    <th>Nama Perusahaan</th>
                    <th>Lokasi Pemeriksaan</th>
                    <th>Jenis Dokumen</th>
                    <th>Surat Pengajuan</th>
                    <th>Status Arsip</th>
                    <th style="width:120px">Aksi</th>
                </tr>
            </thead>
            <tbody>

                @forelse($pengajuan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}
                        </td>

                        <td>{{ $item->agendaSuratPengajuan->nomor_surat_masuk }}</td>
                        <td>{{ $item->nama_kapal }}</td>

                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>

                        <td>{{ $item->lokasi_kapal }}</td>

                        <td>
                            <span class="badge bg-primary">
                                {{ $item->jenis_dokumen }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ asset('storage/' . $item->surat_permohonan_dan_dokumen) }}" target="_blank"
                                class="btn btn-sm btn-info">
                                Lihat File
                            </a>
                        </td>

                        <td>
                            @if ($item->agenda_surat_pengajuan_id)
                                <span class="badge bg-success">Sudah Arsip</span>
                            @else
                                <span class="badge bg-warning text-dark">Belum Arsip</span>
                            @endif
                        </td>



                        <td>
                            @if ($item->agenda_surat_pengajuan_id)
                                {{-- SUDAH ARSIP --}}
                                <button class="btn btn-sm btn-secondary" disabled>
                                    Sudah Diarsipkan
                                </button>
                            @else
                                {{-- BELUM ARSIP --}}
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                    data-bs-target="#arsipModal{{ $item->id }}">
                                    Arsipkan
                                </button>
                            @endif
                        </td>




                        <div class="modal fade" id="arsipModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">

                                <div class="modal-content">

                                    <form action="{{ route('arsiparis.arsipkan', $item->id) }}" method="POST">

                                        @csrf

                                        <div class="modal-header">
                                            <h5 class="modal-title">Arsipkan Pengajuan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            {{-- Nomor Surat --}}
                                            <div class="mb-3">
                                                <label class="form-label">Nomor Surat Pengajuan</label>

                                                <input type="text" name="nomor_surat_pengajuan" class="form-control"
                                                    required>
                                            </div>

                                            {{-- Tanggal Surat --}}
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal Surat</label>

                                                <input type="date" name="tanggal_surat" class="form-control" required>
                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Batal
                                            </button>

                                            <button type="submit" class="btn btn-primary">
                                                Simpan Arsip
                                            </button>

                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center">
                            Tidak ada data pengajuan
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
                api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + settings._iDisplayStart; // Menampilkan nomor urut sesuai halaman dan filter
                });
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
    </script>

@endsection
