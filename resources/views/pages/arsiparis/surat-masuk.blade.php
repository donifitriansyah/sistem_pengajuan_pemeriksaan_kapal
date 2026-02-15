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
            <h2>Daftar Surat Masuk</h2>
        </div>

        <table id="tablePengajuan" >
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

                        <td ><strong>{{ $item->nomor_surat_masuk }}</strong></td>

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



@endsection
