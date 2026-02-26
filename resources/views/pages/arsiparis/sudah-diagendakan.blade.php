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
                    <option value="P3K">P3K</option>
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
                    <th>Kode Bayar</th>
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
                            <span class="badge bg-secondary">{{ $item->kode_bayar }}</span>
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
        columnDefs: [{
            targets: [0, 10], // No & Aksi tidak bisa di sort
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
            api.column(0, { page: 'current' }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1 + settings._iDisplayStart;
            });
        }
    });

    /* ===============================
       ISI DROPDOWN DINAMIS
    =============================== */

    const tahunSet = new Set();
    const bulanSet = new Set();
    const perusahaanSet = new Set();

    table.rows().every(function() {
        const data = this.data();

        // Tanggal (kolom ke-1)
        const dateParts = data[1].split('-');
        tahunSet.add(dateParts[2]);
        bulanSet.add(dateParts[1]);

        // Perusahaan (kolom ke-4)
        const perusahaan = data[4];
        if (perusahaan) {
            perusahaanSet.add(perusahaan.trim());
        }
    });

    [...tahunSet].sort().forEach(t =>
        $('#filterTahun').append(`<option value="${t}">${t}</option>`)
    );

    [...bulanSet].sort().forEach(b =>
        $('#filterBulan').append(`<option value="${b}">${b}</option>`)
    );

    [...perusahaanSet].sort().forEach(p =>
        $('#filterPerusahaan').append(`<option value="${p}">${p}</option>`)
    );

    /* ===============================
       FILTER CUSTOM
    =============================== */

    $.fn.dataTable.ext.search.push(function(settings, data) {

        const filterTahun = $('#filterTahun').val();
        const filterBulan = $('#filterBulan').val();
        const filterPerusahaan = $('#filterPerusahaan').val();
        const filterDokumen = $('#filterJenisDokumen').val();

        const date = data[1].split('-');
        const bulan = date[1];
        const tahun = date[2];

        const perusahaan = data[4]; // kolom Nama Perusahaan
        const dokumen = $('<div>').html(data[6]).text().trim();

        if (filterTahun && tahun !== filterTahun) return false;
        if (filterBulan && bulan !== filterBulan) return false;
        if (filterPerusahaan && perusahaan !== filterPerusahaan) return false;
        if (filterDokumen && dokumen !== filterDokumen) return false;

        return true;
    });

    $('#filterTahun, #filterBulan, #filterPerusahaan, #filterJenisDokumen')
        .on('change', function() {
            table.draw();
        });

    /* ===============================
       RESET FILTER
    =============================== */

    window.resetFilter = function() {
        $('#filterTahun').val('');
        $('#filterBulan').val('');
        $('#filterPerusahaan').val('');
        $('#filterJenisDokumen').val('');
        table.draw();
    };

});
</script>
@endsection
