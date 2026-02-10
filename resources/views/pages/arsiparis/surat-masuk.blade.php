@extends('layouts.app')

@section('content')
    <div class="content-card">

        <div class="content-header">
            <h2>Daftar Surat Masuk</h2>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nomor Surat Pengajuan</th>
                    <th>Nomor Surat Masuk</th>
                    <th>Nomor Surat Keluar</th>
                    <th>Nama Kapal</th>
                    <th>Perusahaan</th>
                    <th>Jenis Dokumen</th>
                    <th>Tanggal Surat</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($suratKeluar as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- <td>
                        <strong>{{ $item->nomor_surat_keluar }}</strong>
                    </td> --}}

                        <td>{{ $item->nomor_surat_pengajuan }}</td>

                        <td ><strong>{{ $item->nomor_surat_masuk }}</strong></td>
                        <td>{{ $item->nomor_surat_keluar }}</td>

                        <td>{{ $item->pengajuan->nama_kapal ?? '-' }}</td>

                        <td>{{ $item->pengajuan->user->nama_perusahaan ?? '-' }}</td>

                        <td>
                            <span class="badge bg-primary">
                                {{ $item->pengajuan->jenis_dokumen ?? '-' }}
                            </span>
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d-m-Y') }}
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
@endsection
