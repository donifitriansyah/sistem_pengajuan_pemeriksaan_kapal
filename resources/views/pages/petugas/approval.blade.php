@extends('layouts.app')
@section('header-title')
    Approval
@endsection
@section('title')
    Approval User
@endsection
@section('content')


    <div class="content-card">
        <div class="content-header">
            <h2>Approval User</h2>
        </div>
        <table id="tableApproval">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Email</th>
                    <th>Nama Perusahaan</th>
                    <th>Nama Petugas</th>
                    <th>No HP</th>
                    <th>Wilayah Kerja</th>
                    <th>Tanggal Daftar</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Persetujuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>

                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->nama_perusahaan }}</td>
                        <td>{{ $user->nama_petugas }}</td>
                        <td>{{ $user->no_hp }}</td>
                        <td>{{ $user->wilayah_kerja }}</td>
                        <td>{{ $user->created_at->format('d-m-Y') }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            @if ($user->status == 'aktif')
                                <span class="badge bg-success">
                                    Aktif
                                </span>
                            @elseif($user->status == 'nonaktif')
                                <span class="badge bg-warning text-dark">
                                    Nonaktif
                                </span>
                            @elseif($user->status == 'ditolak')
                                <span class="badge bg-danger">
                                    Ditolak
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    {{ ucfirst($user->status) }}
                                </span>
                            @endif
                        </td>

                        <td>
{{-- FORM APPROVE --}}
                            <form id="approve-form-{{ $user->id }}" action="{{ route('petugas.user.approve', $user->id) }}"
                                method="POST" style="display:none;">
                                @csrf
                                @method('PUT')
                            </form>



                            {{-- JIKA STATUS NONAKTIF → TAMPIL SETUJUI --}}
                            @if ($user->status == 'nonaktif')
                                <button onclick="confirmApprove({{ $user->id }})" class="btn btn-success btn-sm">
                                    Setujui
                                </button>

                                {{-- JIKA STATUS AKTIF → TAMPIL TOLAK --}}
                            @elseif($user->status == 'aktif')
                                <button onclick="confirmReject({{ $user->id }})" class="btn btn-danger btn-sm">
                                    Tolak
                                </button>
                            @endif
                        </td>



                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada user menunggu persetujuan
                        </td>
                    </tr>
                @endforelse
            </tbody>


        </table>
        <div id="emptyApproval" class="empty-state" style="display:none;">
            <p>Tidak ada user yang menunggu persetujuan</p>
        </div>
    </div>
    <script>
        function confirmApprove(id) {
            Swal.fire({
                title: 'Setujui User?',
                text: 'User akan diaktifkan',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
            }).then((result) => {

                if (result.isConfirmed) {
                    document.getElementById('approve-form-' + id).submit();
                }

            });
        }


        function confirmReject(id) {
            Swal.fire({
                title: 'Tolak User?',
                text: 'User akan dinonaktifkan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
            }).then((result) => {

                if (result.isConfirmed) {
                    document.getElementById('reject-form-' + id).submit();
                }

            });
        }


        function confirmReset(id) {
            Swal.fire({
                title: 'Reset Password?',
                text: 'Password akan menjadi: 12345678',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
            }).then((result) => {

                if (result.isConfirmed) {
                    document.getElementById('reset-form-' + id).submit();
                }

            });
        }
        $(document).ready(function() {
            $('#tableApproval').DataTable({
                // You can customize this to suit your needs
                "paging": true, // Enable pagination
                "searching": true, // Enable search functionality
                "ordering": true, // Enable column sorting
                "info": true, // Display information about the table
                "language": {
                    "search": "Cari:", // Custom search box text
                    "lengthMenu": "Tampilkan _MENU_ baris", // Customize length menu
                    "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri", // Customize info text
                    "infoEmpty": "Tidak ada entri yang tersedia", // Customize empty info text
                    "zeroRecords": "Tidak ada data yang cocok", // Customize zero records text
                    "paginate": {
                        "previous": "Sebelumnya", // Customize previous button
                        "next": "Selanjutnya" // Customize next button
                    }
                }
            });
        });
    </script>
@endsection
