<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base target="_top">
    <title>@yield('title')</title>
    @include('includes.backend.style')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>

    <div class="container">

        @include('includes.backend.header')

        @include('includes.backend.tab')



        @yield('content')
        @stack('script')

        {{-- <div id="tab-petugas-pemeriksa" class="content-section">
            <div class="content-card">
                <div class="content-header">
                    <h2>Data Petugas Pemeriksa</h2>
                    <div class="header-actions">
                        <div class="search-box">
                            <input type="text" id="searchPetugas" placeholder="Cari kapal / nama petugas...">
                        </div>
                    </div>
                </div>

                <div class="filter-container-petugas">
                    <div class="filter-group">
                        <label>Tanggal Awal</label>
                        <input type="date" id="filterTanggalAwal">
                    </div>
                    <div class="filter-group">
                        <label>Tanggal Akhir</label>
                        <input type="date" id="filterTanggalAkhir">
                    </div>
                    <div class="filter-group">
                        <label>Jenis Dokumen</label>
                        <select id="filterJenisDokumenPetugas">
                            <option value="">Semua Dokumen</option>
                            <option value="PHQC">PHQC</option>
                            <option value="SSCEC">SSCEC</option>
                            <option value="COP">COP</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Jenis Tarif</label>
                        <select id="filterJenisTarif">
                            <option value="">Semua Tarif</option>
                            <option value="Dalam Kota Kurang 8 Jam">Dalam Kota Kurang 8 Jam</option>
                            <option value="Dalam Kota Lebih 8 Jam">Dalam Kota Lebih 8 Jam</option>
                            <option value="Luar Kota">Luar Kota</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Nama Petugas</label>
                        <select id="filterNamaPetugas">
                            <option value="">Semua Petugas</option>
                        </select>
                    </div>
                    <button class="btn btn-outline" onclick="resetFilterPetugas()">Reset</button>
                    <button class="btn btn-success" onclick="downloadExcelPetugas()">Excel</button>
                </div>

                <table id="tablePetugas">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pemeriksaan</th>
                            <th>Nama Kapal</th>
                            <th>Jenis Dokumen</th>
                            <th>Lokasi Pemeriksaan</th>
                            <th>Kategori Tarif</th>
                            <th>Petugas 1</th>
                            <th>Petugas 2</th>
                            <th>Petugas 3</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div id="emptyPetugas" class="empty-state" style="display:none;">
                    <p>Belum ada data petugas pemeriksa</p>
                </div>
            </div>
        </div> --}}


    </div>

</body>

</html>
