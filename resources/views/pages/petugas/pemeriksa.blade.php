@extends('layouts.app')

@section('content')
    <div class="content-card">
        <div class="content-header">
            <h2>Daftar Pengajuan Belum Diagendakan</h2>
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

        <table id="tablePengajuan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Pemeriksaan</th>
                    <th>Wilker</th>
                    <th>Nama Kapal</th>
                    <th>Nama Perusahaan</th>
                    <th>Lokasi Pemeriksaan</th>
                    <th>Jenis Dokumen</th>
                    <th>Kode Bayar</th>
                    <th>Status Pembayaran</th>
                    <th>Surat Tugas</th>
                    <th>Petugas 1</th> <!-- Petugas 1 column -->
                    <th>Petugas 2</th> <!-- Petugas 2 column -->
                    <th>Petugas 3</th> <!-- Petugas 3 column -->
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengajuan as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}</td>
                        <td>{{ $item->wilayah_kerja }}</td>
                        <td>{{ $item->nama_kapal }}</td>
                        <td>{{ $item->user->nama_perusahaan ?? '-' }}</td>
                        <td>{{ $item->lokasi_kapal }}</td>
                        <td>{{ $item->jenis_dokumen }}</td>
                        <td>{{ $item->kode_bayar }}</td>
                        <td>{{ $item->penagihan->pembayaran->status ?? 'Belum Dibayar' }}</td>
                        <td>
                            {{ $item->agendaSuratPengajuan->nomor_surat_keluar ?? '-' }}
                            <!-- Replace with actual attribute -->
                        </td>
                        <!-- Display Petugas -->
                        <!-- Display Petugas -->
                        <td>
                            {{ isset($item->penagihan->petugas[0]) ? $item->penagihan->petugas[0]->nama_petugas : '-' }}
                        </td>
                        <td>
                            {{ isset($item->penagihan->petugas[1]) ? $item->penagihan->petugas[1]->nama_petugas : '-' }}
                        </td>
                        <td>
                            {{ isset($item->penagihan->petugas[2]) ? $item->penagihan->petugas[2]->nama_petugas : '-' }}
                        </td>

                        <td>
                            <button class="btn btn-info" data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $item->id }}" data-id="{{ $item->id }}">Edit</button>
                        </td>


                    </tr>
                @endforeach
                @foreach ($pengajuan as $item)
                    <!-- Modal for Editing -->
                    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                        aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('pengajuan.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel{{ $item->id }}">Edit Pengajuan
                                            Pemeriksaan Kapal</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <!-- Nama Kapal -->
                                        <div class="mb-3">
                                            <label for="nama_kapal" class="form-label">Nama Kapal</label>
                                            <input type="text" class="form-control" id="nama_kapal" name="nama_kapal"
                                                value="{{ $item->nama_kapal }}" required>
                                        </div>

                                        <!-- Lokasi Kapal -->
                                        <div class="mb-3">
                                            <label for="lokasi_kapal" class="form-label">Lokasi Kapal</label>
                                            <input type="text" class="form-control" id="lokasi_kapal" name="lokasi_kapal"
                                                value="{{ $item->lokasi_kapal }}" required>
                                        </div>

                                        <!-- Jenis Dokumen -->
                                        <div class="mb-3">
                                            <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                                            <select class="form-control" id="jenis_dokumen" name="jenis_dokumen" required>
                                                <option value="PHQC"
                                                    {{ $item->jenis_dokumen == 'PHQC' ? 'selected' : '' }}>PHQC</option>
                                                <option value="SSCEC"
                                                    {{ $item->jenis_dokumen == 'SSCEC' ? 'selected' : '' }}>SSCEC</option>
                                                <option value="COP"
                                                    {{ $item->jenis_dokumen == 'COP' ? 'selected' : '' }}>COP</option>
                                            </select>
                                        </div>

                                        <!-- Add other fields if needed -->
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update Pengajuan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

            </tbody>
        </table>
        <div id="emptyPengajuan" class="empty-state" style="display:none;">
            <p>✅ Semua pengajuan sudah diagendakan!</p>
        </div>
    </div>
@endsection
