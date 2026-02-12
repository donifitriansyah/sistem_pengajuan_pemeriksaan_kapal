@extends('layouts.app')
@section('header-title')
    Management
@endsection
@section('title')
    Management User
@endsection
@section('content')
    <style>
        /* Set the width of the 'Persetujuan' and 'Aksi' columns */
        #tableApproval th:nth-child(10){
            /* Aksi column */
            width: 300px;
            /* Adjust this width as needed */
        }

        /* Adjust the width of the corresponding table data cells */
        #tableApproval td:nth-child(10) {
            width: 300px;
            /* Match the width of the header */
            text-align: center;
            /* Center the buttons */
        }
    </style>

    <div class="content-card">
        <div class="content-header">
            <h2>Management User</h2>
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
                    <th>Aksi</th>
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
                            {{-- BUTTON DELETE --}}
                            @if (auth()->id() !== $user->id)
                                <button onclick="confirmDelete({{ $user->id }})" class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            @endif

                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editUserModal{{ $user->id }}">
                                Edit
                            </button>

                            {{-- FORM DELETE --}}
                            <form id="delete-form-{{ $user->id }}"
                                action="{{ route('admin.user.destroy', $user->id) }}" method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>


                            {{-- RESET PASSWORD (SELALU ADA) --}}
                            <button onclick="confirmReset({{ $user->id }})" class="btn btn-warning btn-sm">
                                Reset Password
                            </button>
                        </td>
                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label>Email</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ $user->email }}" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Role</label>
                                                    <select name="role" class="form-select">
                                                        <option value="admin"
                                                            {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                        <option value="arsiparis"
                                                            {{ $user->role == 'arsiparis' ? 'selected' : '' }}>Arsiparis
                                                        </option>
                                                        <option value="user"
                                                            {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                                        <option value="petugas"
                                                            {{ $user->role == 'petugas' ? 'selected' : '' }}>Petugas
                                                        </option>
                                                        <option value="petugas-kapal"
                                                            {{ $user->role == 'petugas-kapal' ? 'selected' : '' }}>Petugas Karantina
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="aktif"
                                                            {{ $user->status == 'aktif' ? 'selected' : '' }}>
                                                            Aktif
                                                        </option>
                                                        <option value="nonaktif"
                                                            {{ $user->status == 'nonaktif' ? 'selected' : '' }}>
                                                            Nonaktif
                                                        </option>
                                                        <option value="ditolak"
                                                            {{ $user->status == 'ditolak' ? 'selected' : '' }}>
                                                            Ditolak
                                                        </option>
                                                    </select>
                                                </div>


                                                <div class="col-md-6 mb-3">
                                                    <label>Nama Perusahaan</label>
                                                    <input type="text" name="nama_perusahaan" class="form-control"
                                                        value="{{ $user->nama_perusahaan }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Nama Petugas</label>
                                                    <input type="text" name="nama_petugas" class="form-control"
                                                        value="{{ $user->nama_petugas }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>No HP</label>
                                                    <input type="text" name="no_hp" class="form-control"
                                                        value="{{ $user->no_hp }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label>Wilayah Kerja</label>
                                                    <input type="text" name="wilayah_kerja" class="form-control"
                                                        value="{{ $user->wilayah_kerja }}">
                                                </div>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">
                                                Simpan Perubahan
                                            </button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>


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
