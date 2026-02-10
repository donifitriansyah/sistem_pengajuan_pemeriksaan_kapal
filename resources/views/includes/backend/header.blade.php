<div class="header">
    <div class="header-left">
        <img src="https://lh3.googleusercontent.com/d/1GypYsK22OTiKiLfW0WWnJ779Wx209LDe" alt="Logo" class="logo-small"
            onerror="this.style.display='none'">
        <div class="header-title">
            <h1>@yield('header-title')</h1>
            <p class="subtitle">Sistem Pengajuan Pemeriksaan Kapal</p>
        </div>
    </div>
    <div class="header-right">
        <span class="user-info">
            <span class="user-icon">&#128100;</span>
            <span id="namaUser">
                {{ Auth::user()->nama_petugas ?? '-' }}
            </span>

        </span>
        <form id="logoutForm" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="button" onclick="confirmLogout()" class="btn btn-danger btn-sm">
                Logout
            </button>
        </form>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            function confirmLogout() {
                Swal.fire({
                    title: 'Yakin logout?',
                    text: 'Anda akan keluar dari sistem',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logoutForm').submit();
                    }
                });
            }
        </script>

    </div>
</div>
