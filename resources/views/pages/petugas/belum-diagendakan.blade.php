@extends('layouts.app')
@section('header-title')
    Dashboard Petugas
@endsection
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
    </div>


    <div class="content-card">
        <div class="content-header">
            <h2>Daftar Pengajuan Verifikasi</h2>
            <div class="header-actions">
            </div>
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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <table id="pengajuanTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Diajukan</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Lokasi</th>
                    <th>Jenis Dokumen</th>
                    <th>Kode Bayar</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($pengajuan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>

                        <td>{{ $item->nama_kapal }}</td>
                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $item->lokasi_kapal }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $item->jenis_dokumen }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $item->kode_bayar }}</span>
                        </td>
                        <td>
                            <a href="{{ asset('storage/' . $item->surat_permohonan_dan_dokumen) }}" target="_blank"
                                class="btn btn-sm btn-info">
                                Lihat File
                            </a>
                        </td>
                        <td>{{ $item->status }}</td>

                        <td>
                            <!-- Check if agenda_surat_pengajuan_id is null, show "Belum Diarsipkan" -->
                            @if (is_null($item->agenda_surat_pengajuan_id))
                                <span class="badge bg-danger">Belum Diarsipkan</span>
                            @else
                                <!-- Check if penagihan exists -->
                                @php
                                    $penagihan = $item->penagihan;
                                @endphp

                                @if ($penagihan)
                                    <!-- If penagihan exists, check the payment status -->
                                    @if ($penagihan->isLunas())
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-warning">Belum Bayar</span>
                                    @endif
                                @else

                                @endif
                            @endif


                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        <div id="emptyPengajuan" class="empty-state" style="display:none;">
            <p>✅ Semua pengajuan sudah diagendakan!</p>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            const table = $('#pengajuanTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                pageLength: 10,

                columnDefs: [{
                    targets: 0, // kolom nomor
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
                    var api = this.api();
                    api.column(0, {
                        page: 'current'
                    }).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1 + settings._iDisplayStart;
                    });
                }
            });

            /* =====================================
                ISI FILTER DINAMIS (TAHUN, BULAN, PERUSAHAAN)
            ===================================== */

            const tahunSet = new Set();
            const bulanSet = new Set();
            const perusahaanSet = new Set();

            table.rows().every(function() {
                const data = this.data();

                // Kolom tanggal (format d-m-Y)
                const dateParts = data[1].split('-');
                tahunSet.add(dateParts[2]);
                bulanSet.add(dateParts[1]);

                // Kolom perusahaan (index 3)
                const perusahaan = data[3];
                if (perusahaan) {
                    perusahaanSet.add(perusahaan.trim());
                }
            });

            // Isi dropdown Tahun
            [...tahunSet].sort().forEach(t =>
                $('#filterTahun').append(`<option value="${t}">${t}</option>`)
            );

            // Isi dropdown Bulan
            [...bulanSet].sort().forEach(b =>
                $('#filterBulan').append(`<option value="${b}">${b}</option>`)
            );

            // Isi dropdown Perusahaan
            [...perusahaanSet].sort().forEach(p =>
                $('#filterPerusahaan').append(`<option value="${p}">${p}</option>`)
            );

            /* =====================================
                FILTER CUSTOM
            ===================================== */

            $.fn.dataTable.ext.search.push(function(settings, data) {

                const filterTahun = $('#filterTahun').val();
                const filterBulan = $('#filterBulan').val();
                const filterPerusahaan = $('#filterPerusahaan').val();
                const filterDokumen = $('#filterJenisDokumen').val();
                const filterStatus = $('#filterStatus').val();

                const date = data[1].split('-');
                const bulan = date[1];
                const tahun = date[2];

                const perusahaan = data[4];
                const dokumen = data[5];
                const status = data[8]; // sesuaikan dengan kolom status kamu

                if (filterTahun && tahun !== filterTahun) return false;
                if (filterBulan && bulan !== filterBulan) return false;
                if (filterPerusahaan && perusahaan !== filterPerusahaan) return false;
                if (filterDokumen && dokumen !== filterDokumen) return false;

                if (filterStatus) {
                    if (!status.includes(filterStatus)) return false;
                }

                return true;
            });

            // Trigger filter saat dropdown berubah
            $('#filterTahun, #filterBulan, #filterPerusahaan, #filterJenisDokumen, #filterStatus')
                .on('change', function() {
                    table.draw();
                });

        });


        /* =====================================
            RESET FILTER
        ===================================== */

        function resetFilter() {

            $('#filterTahun').val('');
            $('#filterBulan').val('');
            $('#filterPerusahaan').val('');
            $('#filterJenisDokumen').val('');
            $('#filterStatus').val('');

            const table = $('#pengajuanTable').DataTable();
            table.search('').columns().search('').draw();
        }
    </script>
@endsection

