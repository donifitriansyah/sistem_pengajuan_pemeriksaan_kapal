@extends('layouts.app')
@section('header-title')
    Dashboard Petugas
@endsection
@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <div class="content-card">
        <div class="content-header">
            <h2>Daftar Penagihan</h2>
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


        <table id="pengajuanTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Estimasi</th>
                    <th>Waktu Kedatangan Kapal</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Lokasi</th>
                    <th>Jenis Dokumen</th>
                    <th>Nomor Surat</th>
                    <th>File</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($pengajuan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->waktu_kedatangan_kapal)->format('H:i') }}</td>

                        <td>{{ $item->nama_kapal }}</td>
                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $item->lokasi_kapal }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $item->jenis_dokumen }}</span>
                        </td>
                        <td>{{ $item->agendaSuratPengajuan->nomor_surat_masuk ?? '-' }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $item->surat_permohonan_dan_dokumen) }}" target="_blank"
                                class="btn btn-sm btn-info">
                                Lihat File
                            </a>
                        </td>
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
                                    <!-- If no penagihan exists, show the button to create penagihan -->
                                    <button class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#modalPenagihan{{ $item->id }}">
                                        Buat Penagihan
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @foreach ($pengajuan as $item)
            {{-- ================= MODAL ================= --}}
            <div class="modal fade" id="modalPenagihan{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('petugas.penagihan.store', $item->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Penagihan Pemeriksaan Kapal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                {{-- INFO --}}
                                <div class="alert alert-light border">
                                    <strong>Kapal :</strong> {{ $item->nama_kapal }} <br>
                                    <strong>Lokasi :</strong> {{ $item->lokasi_kapal }} <br>
                                    <strong>Tanggal :</strong>
                                    {{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}
                                </div>

                                {{-- JUMLAH PETUGAS --}}
                                <div class="mb-3">
                                    <label class="form-label">Jumlah Petugas</label>
                                    <select name="jumlah_petugas" class="form-select jumlah-petugas"
                                        data-id="{{ $item->id }}" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="2">2 Petugas</option>
                                        <option value="3">3 Petugas</option>
                                    </select>
                                </div>

                                {{-- PETUGAS (DINAMIS - WAJIB DI DALAM FORM) --}}
                                <div id="petugas-container-{{ $item->id }}"></div>


                                {{-- WAKTU MULAI DAN SELESAI --}}
                                <div class="mb-3">
                                    <label class="form-label">Waktu Mulai</label>
                                    <input type="datetime-local" name="waktu_mulai" class="form-control" required
                                        data-id="{{ $item->id }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Waktu Selesai</label>
                                    <input type="datetime-local" name="waktu_selesai" class="form-control" required
                                        data-id="{{ $item->id }}">
                                </div>



                                {{-- JENIS TARIF --}}
                                <div class="mb-3">
                                    <label class="form-label">Jenis Tarif</label>
                                    <select name="jenis_tarif" class="form-select tarif" data-id="{{ $item->id }}"
                                        required>
                                        <option value="">-- Pilih Tarif --</option>
                                        <option value="170000">Dalam Kota (&lt; 8 Jam)</option>
                                        <option value="320000">Dalam Kota (&gt; 8 Jam)</option>
                                        <option value="380000">Luar Kota</option>
                                    </select>
                                </div>

                                {{-- TOTAL TARIF --}}
                                <div class="mb-3">
                                    <label class="form-label">Total Tarif</label>
                                    <input type="text" id="totalDisplay{{ $item->id }}" class="form-control"
                                        readonly>
                                    <input type="hidden" name="total_tarif" id="totalValue{{ $item->id }}">
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Penagihan</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            {{-- =============== END MODAL =============== --}}
        @endforeach


        <script>
            const petugasData = @json($petugas);
        </script>
        <div id="emptyPengajuan" class="empty-state" style="display:none;">
            <p>✅ Semua pengajuan sudah diagendakan!</p>
        </div>
    </div>







@endsection
@push('script')
    <script>
        document.querySelectorAll('.jumlah-petugas').forEach(select => {
            select.addEventListener('change', function() {

                const id = this.dataset.id;
                const jumlah = parseInt(this.value);
                const container = document.getElementById(`petugas-container-${id}`);
                container.innerHTML = '';

                if (!jumlah) return;

                for (let i = 0; i < jumlah; i++) {
                    let options = '<option value="">-- Pilih Petugas --</option>';

                    petugasData.forEach(p => {
                        options +=
                            `<option value="${p.id}" data-nama="${p.nama_petugas ?? p.nama ?? p.name}">${p.nama_petugas ?? p.nama ?? p.name}</option>`;
                    });

                    container.innerHTML += `
                <div class="mb-3">
                    <label class="form-label">Petugas ${i + 1}</label>
                    <select name="petugas[]" class="form-select petugas-select" data-index="${i}" data-id="${id}" required>
                        ${options}
                    </select>
                </div>
            `;
                }

                // Tambahkan event listener untuk setiap dropdown baru
                updatePetugasOptions(id);
            });
        });

        function updatePetugasOptions(pengajuanId) {
            const selects = document.querySelectorAll(`#petugas-container-${pengajuanId} .petugas-select`);

            selects.forEach(select => {
                select.addEventListener('change', () => {
                    const selectedValues = Array.from(selects)
                        .map(s => s.value)
                        .filter(v => v); // ambil yang ada isinya

                    // update opsi di semua select
                    selects.forEach(s => {
                        const currentValue = s.value;

                        Array.from(s.options).forEach(opt => {
                            if (opt.value === "") return; // biarkan option kosong
                            if (selectedValues.includes(opt.value) && opt.value !==
                                currentValue) {
                                opt.disabled =
                                    true; // disable yang sudah dipilih di dropdown lain
                            } else {
                                opt.disabled = false;
                            }
                        });
                    });
                });
            });
        }

        // // Update tarif total tetap sama
        // document.querySelectorAll('.tarif').forEach(select => {
        //     select.addEventListener('change', function() {
        //         const id = this.dataset.id;

        //         // Get the selected tariff (parse as integer to avoid floating-point issues)
        //         const tarif = parseInt(this.value) || 0; // Ensure it's an integer
        //         const jumlah = parseInt(document.querySelector(`.jumlah-petugas[data-id="${id}"]`).value) ||
        //             0; // Ensure it's an integer

        //         // Get the start and end time
        //         const waktuMulai = new Date(document.querySelector(`[name="waktu_mulai"]`).value);
        //         const waktuSelesai = new Date(document.querySelector(`[name="waktu_selesai"]`).value);

        //         // Calculate the difference in milliseconds
        //         const waktuDifferenceInMs = waktuSelesai - waktuMulai;

        //         // Convert milliseconds to days (rounding the difference correctly)
        //         const daysDifference = Math.ceil(waktuDifferenceInMs / (1000 * 3600 *
        //         24)); // Convert ms to days

        //         // Calculate the total tarif (ensure integer)
        //         let total = tarif * jumlah * daysDifference;

        //         // Round the total value to the nearest whole number (to avoid decimals)
        //         total = Math.round(total); // Round to nearest integer

        //         // Update the display for total tarif (formatted as currency)
        //         document.getElementById(`totalDisplay${id}`).value = new Intl.NumberFormat('id-ID', {
        //             style: 'currency',
        //             currency: 'IDR'
        //         }).format(total); // Format the total as currency

        //         // Set the hidden input value (which will be sent to the server and saved to the database)
        //         document.getElementById(`totalValue${id}`).value =
        //         total; // Set the rounded value (no decimals)
        //     });
        // });
        // Update tarif total tetap sama
        document.querySelectorAll('.tarif').forEach(function(select) {
            select.addEventListener('change', function() {
                const id = this.dataset.id;

                // Get the selected tariff (parse as integer to avoid floating-point issues)
                const tarif = parseInt(this.value) || 0; // Ensure it's an integer
                const jumlah = parseInt(document.querySelector(`.jumlah-petugas[data-id="${id}"]`).value) ||
                    0; // Ensure it's an integer

                // Get the start and end time
                const waktuMulai = document.querySelector(`[name="waktu_mulai"][data-id="${id}"]`).value;
                const waktuSelesai = document.querySelector(`[name="waktu_selesai"][data-id="${id}"]`)
                    .value;

                // Check if both times are selected
                if (!waktuMulai || !waktuSelesai) {
                    console.log('Waktu Mulai atau Waktu Selesai belum dipilih');
                    return; // Skip calculation if either time is missing
                }

                // Calculate the difference in milliseconds
                const start = new Date(waktuMulai);
                const end = new Date(waktuSelesai);
                const waktuDifferenceInMs = end - start;

                // Validate if the time difference is valid (should not be negative or NaN)
                if (isNaN(waktuDifferenceInMs) || waktuDifferenceInMs <= 0) {
                    console.log('Perhitungan waktu tidak valid');
                    return; // Skip calculation if the time difference is invalid
                }

                // Convert milliseconds to days (rounding the difference correctly)
                const daysDifference = Math.ceil(waktuDifferenceInMs / (1000 * 3600 *
                    24)); // Convert ms to days

                // Calculate the total tarif (ensure integer)
                let total = tarif * jumlah * daysDifference;

                // Log the total value before formatting (for debugging)
                console.log('Total Tarif (no rounding):', total);

                // Update the display for total tarif (formatted as currency)
                document.getElementById(`totalDisplay${id}`).value = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(total); // Format the total as currency (e.g., Rp 1.520.000)

                // Set the hidden input value (which will be sent to the server and saved to the database)
                document.getElementById(`totalValue${id}`).value = total; // Set the raw value (no rounding)
            });
        });













        // Prevent petugas duplication
        document.querySelectorAll('.petugas-select').forEach(select => {
            select.addEventListener('change', function() {
                const id = this.dataset.id;
                const selectedPetugas = this.value;

                // Logic to prevent the same petugas from being selected in other dates (e.g., "out of town" days)
                const allSelectedValues = [...document.querySelectorAll(
                        `#petugas-container-${id} .petugas-select`)]
                    .map(s => s.value);

                allSelectedValues.forEach(value => {
                    // Disable selected petugas if already chosen elsewhere (out of town)
                    document.querySelectorAll('.petugas-select').forEach(otherSelect => {
                        if (otherSelect.value === value) {
                            otherSelect.disabled = true;
                        } else {
                            otherSelect.disabled = false;
                        }
                    });
                });
            });
        });
    </script>
    {{-- <script>
        document.querySelectorAll('.jumlah-petugas').forEach(select => {
            select.addEventListener('change', function() {

                const id = this.dataset.id;
                const jumlah = parseInt(this.value);
                const container = document.getElementById(`petugas-container-${id}`);
                container.innerHTML = '';

                if (!jumlah) return;

                for (let i = 0; i < jumlah; i++) {
                    let options = '<option value="">-- Pilih Petugas --</option>';

                    petugasData.forEach(p => {
                        options +=
                            `<option value="${p.id}" data-nama="${p.nama_petugas ?? p.nama ?? p.name}">${p.nama_petugas ?? p.nama ?? p.name}</option>`;
                    });

                    container.innerHTML += `
                <div class="mb-3">
                    <label class="form-label">Petugas ${i + 1}</label>
                    <select name="petugas[]" class="form-select petugas-select" data-index="${i}" data-id="${id}" required>
                        ${options}
                    </select>
                </div>
            `;
                }

                // Tambahkan event listener untuk setiap dropdown baru
                updatePetugasOptions(id);
            });
        });

        function updatePetugasOptions(pengajuanId) {
            const selects = document.querySelectorAll(`#petugas-container-${pengajuanId} .petugas-select`);

            selects.forEach(select => {
                select.addEventListener('change', () => {
                    const selectedValues = Array.from(selects)
                        .map(s => s.value)
                        .filter(v => v); // ambil yang ada isinya

                    // update opsi di semua select
                    selects.forEach(s => {
                        const currentValue = s.value;

                        Array.from(s.options).forEach(opt => {
                            if (opt.value === "") return; // biarkan option kosong
                            if (selectedValues.includes(opt.value) && opt.value !==
                                currentValue) {
                                opt.disabled =
                                    true; // disable yang sudah dipilih di dropdown lain
                            } else {
                                opt.disabled = false;
                            }
                        });
                    });
                });
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            const petugasData = @json($petugas);
            const penagihanData = @json($penagihanData); // Data of assigned petugas

            // Function to calculate tarif based on selected hours and type
            function calculateTarif(waktuMulai, waktuSelesai, tarifType, jumlahPetugas) {
                const start = new Date(waktuMulai);
                const end = new Date(waktuSelesai);
                const timeDiff = (end - start) / (1000 * 3600 * 24); // difference in days

                let totalTarif = 0;

                // Calculate tarif based on the selected tarif type
                if (tarifType === "320000") { // Dalam Kota > 8 Jam
                    totalTarif = 320000 * timeDiff * jumlahPetugas;
                } else if (tarifType === "170000") { // Dalam Kota < 8 Jam
                    totalTarif = 170000 * timeDiff * jumlahPetugas;
                } else if (tarifType === "380000") { // Luar Kota
                    totalTarif = 380000 * timeDiff * jumlahPetugas;
                }

                return totalTarif;
            }

            // Update total tarif when jenis tarif or jumlah petugas is selected
            document.querySelectorAll('.tarif').forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const tarif = this.value || 0;
                    const jumlah = document.querySelector(`.jumlah-petugas[data-id="${id}"]`)
                        .value || 0;
                    const waktuMulai = document.querySelector(
                        `input[name="waktu_mulai"][data-id="${id}"]`).value;
                    const waktuSelesai = document.querySelector(
                        `input[name="waktu_selesai"][data-id="${id}"]`).value;

                    const total = calculateTarif(waktuMulai, waktuSelesai, tarif, jumlah);

                    // Display total in rupiah format
                    document.getElementById(`totalDisplay${id}`).value = new Intl.NumberFormat(
                        'id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(total);
                    document.getElementById(`totalValue${id}`).value = total;
                });
            });

            // Disable selected petugas options that are already assigned
            function disableAssignedPetugas(pengajuanId) {
                const selectedPetugasIds = penagihanData
                    .filter(p => p.waktu_mulai && p.waktu_selesai) // Filter only assigned petugas
                    .map(p => p.petugas_id); // Get their assigned petugas id

                const selects = document.querySelectorAll(`#petugas-container-${pengajuanId} .petugas-select`);

                selects.forEach(select => {
                    select.addEventListener('change', () => {
                        const selectedPetugas = select.value;

                        // Disable selected petugas in all other selects
                        selects.forEach(s => {
                            const currentValue = s.value;

                            // Disable the selected petugas option
                            Array.from(s.options).forEach(opt => {
                                if (opt.value !== "" && selectedPetugasIds.includes(
                                        opt.value) && opt.value !== currentValue) {
                                    opt.disabled = true;
                                } else {
                                    opt.disabled = false;
                                }
                            });
                        });
                    });
                });
            }

            // Trigger disable petugas for each pengajuan
            document.querySelectorAll('.jumlah-petugas').forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.id;
                    disableAssignedPetugas(id);
                });
            });
        });
    </script> --}}

    <script>
        $(document).ready(function() {

            const table = $('#pengajuanTable').DataTable({
                paging: true,
                ordering: true,
                info: true,
                searching: true,
                columnDefs: [{
                        orderable: false,
                        targets: [7, 8]
                    } // File & Aksi tidak bisa sort
                ]
            });

            /* ==============================
                ISI FILTER DINAMIS
            ============================== */

            const tahunSet = new Set();
            const bulanSet = new Set();
            const perusahaanSet = new Set();

            table.rows().every(function() {
                const data = this.data();

                // Tanggal (kolom 1)
                const dateParts = data[1].split('-'); // d-m-Y
                tahunSet.add(dateParts[2]);
                bulanSet.add(dateParts[1]);

                // Perusahaan (kolom 3)
                if (data[3] && data[3] !== '-') {
                    perusahaanSet.add(data[3]);
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

            /* ==============================
                FILTER CUSTOM
            ============================== */

            $.fn.dataTable.ext.search.push(function(settings, data) {

                const filterTahun = $('#filterTahun').val();
                const filterBulan = $('#filterBulan').val();
                const filterPerusahaan = $('#filterPerusahaan').val();
                const filterDokumen = $('#filterJenisDokumen').val();

                const date = data[1].split('-'); // d-m-Y
                const bulan = date[1];
                const tahun = date[2];

                const perusahaan = data[3];
                const dokumen = data[5];

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

        });

        /* ==============================
            RESET FILTER
        ============================== */
        function resetFilter() {
            $('#filterTahun').val('');
            $('#filterBulan').val('');
            $('#filterPerusahaan').val('');
            $('#filterJenisDokumen').val('');
            $('#pengajuanTable').DataTable().draw();
        }
    </script>
@endpush
