@extends('layouts.app')
@section('content')
    <div class="content-card">
        <div class="content-header">
            <h2>Verifikasi Daftar Pengajuan</h2>
            <div class="header-actions">
                <div class="search-box">
                    <input type="text" id="searchPengajuan" placeholder="Cari kapal / perusahaan...">
                </div>
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


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Wilker</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Lokasi</th>
                    <th>Jenis Dokumen</th>
                    <th>Nomor Surat</th>
                    <th>Nomor Surat Tugas</th>
                    <th>Bukti Bayar</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($pengajuan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}</td>
                        <td>{{ $item->wilayah_kerja }}</td>
                        <td>{{ $item->nama_kapal }}</td>
                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $item->lokasi_kapal }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $item->jenis_dokumen }}</span>
                        </td>
                        <td>{{ $item->agendaSuratPengajuan->nomor_surat_pengajuan ?? '-' }}</td>
                        <td>{{ $item->agendaSuratPengajuan->nomor_surat_keluar ?? '-' }}</td>
                        <td>
                            @if ($item->penagihan && $item->penagihan->pembayaran)
                                <a href="{{ asset('storage/' . $item->penagihan->pembayaran->file) }}" target="_blank"
                                    class="btn btn-sm btn-primary">
                                    Lihat Bukti
                                </a>
                            @else
                                <span class="badge bg-secondary">Belum Upload</span>
                            @endif
                        </td>

                        <td>
                            @if ($item->penagihan && $item->penagihan->pembayaran)
                                @if ($item->penagihan->pembayaran->status === 'menunggu')
                                    <span class="badge bg-warning text-dark mb-1 d-block">
                                        Menunggu Verifikasi
                                    </span>

                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                        data-bs-target="#verifikasiModal{{ $item->id }}">
                                        Verifikasi
                                    </button>
                                @elseif ($item->penagihan->pembayaran->status === 'diterima')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif ($item->penagihan->pembayaran->status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Belum Bayar</span>
                            @endif
                        </td>

                    </tr>
                    @if ($item->penagihan && $item->penagihan->pembayaran)
                        <div class="modal fade" id="verifikasiModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <form
                                        action="{{ route('admin.pembayaran.verifikasi', $item->penagihan->pembayaran->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Verifikasi Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p>
                                                <strong>Kapal:</strong> {{ $item->nama_kapal }} <br>
                                                <strong>Perusahaan:</strong> {{ $item->user->nama_perusahaan ?? '-' }}
                                                <strong>Wilker:</strong> {{ $item->wilker }} <br>
                                                <strong>Lokasi:</strong> {{ $item->jenis_dokumen }} <br>
                                                <strong>Bukti Bayar:</strong> <a
                                                    href="{{ asset('storage/' . $item->penagihan->pembayaran->file) }}"
                                                    target="_blank" class="btn btn-sm btn-primary">
                                                    Lihat Bukti
                                                </a> <br>
                                            </p>

                                            <div class="mb-3">
                                                <label>Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="diterima">Terima (Lunas)</option>
                                                    <option value="ditolak">Tolak</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label>Keterangan (opsional)</label>
                                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </tbody>
        </table>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const filterTahun = document.getElementById('filterTahun');
            const filterBulan = document.getElementById('filterBulan');
            const filterPerusahaan = document.getElementById('filterPerusahaan');
            const filterJenisDokumen = document.getElementById('filterJenisDokumen');
            const searchInput = document.getElementById('searchPengajuan');

            const rows = document.querySelectorAll('table tbody tr');

            /* =====================
               ISI DROPDOWN OTOMATIS
            ===================== */
            const tahunSet = new Set();
            const bulanSet = new Set();
            const perusahaanSet = new Set();

            rows.forEach(row => {
                const tanggal = row.cells[1].innerText; // dd-mm-yyyy
                const perusahaan = row.cells[3].innerText.trim();

                const [day, month, year] = tanggal.split('-');

                tahunSet.add(year);
                bulanSet.add(month);
                if (perusahaan !== '-') perusahaanSet.add(perusahaan);
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

            /* =====================
               FILTER + SEARCH
            ===================== */
            function applyFilter() {
                const tahun = filterTahun.value;
                const bulan = filterBulan.value;
                const perusahaan = filterPerusahaan.value.toLowerCase();
                const jenis = filterJenisDokumen.value.toLowerCase();
                const search = searchInput.value.toLowerCase();

                let visibleCount = 0;

                rows.forEach(row => {
                    const tanggalText = row.cells[1].innerText;
                    const kapalText = row.cells[2].innerText.toLowerCase();
                    const perusahaanText = row.cells[3].innerText.toLowerCase();
                    const jenisText = row.cells[5].innerText.toLowerCase();

                    const [day, month, year] = tanggalText.split('-');

                    let show = true;

                    if (tahun && year !== tahun) show = false;
                    if (bulan && month !== bulan) show = false;
                    if (perusahaan && !perusahaanText.includes(perusahaan)) show = false;
                    if (jenis && !jenisText.includes(jenis)) show = false;

                    if (search &&
                        !kapalText.includes(search) &&
                        !perusahaanText.includes(search)
                    ) show = false;

                    row.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                document.getElementById('emptyPengajuan').style.display =
                    visibleCount === 0 ? 'block' : 'none';
            }

            filterTahun.addEventListener('change', applyFilter);
            filterBulan.addEventListener('change', applyFilter);
            filterPerusahaan.addEventListener('change', applyFilter);
            filterJenisDokumen.addEventListener('change', applyFilter);
            searchInput.addEventListener('keyup', applyFilter);

        });

        /* =====================
           RESET FILTER
        ===================== */
        function resetFilter() {
            document.getElementById('filterTahun').value = '';
            document.getElementById('filterBulan').value = '';
            document.getElementById('filterPerusahaan').value = '';
            document.getElementById('filterJenisDokumen').value = '';
            document.getElementById('searchPengajuan').value = '';

            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = '';
            });

            document.getElementById('emptyPengajuan').style.display = 'none';
        }
    </script>







@endsection
