<div class="tabs">

    {{-- ================= ADMIN ================= --}}
    @if (auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="tab {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            Data Pengajuan
        </a>

        <a href="{{ route('admin.approval.index') }}"
            class="tab {{ request()->routeIs('admin.approval.index') ? 'active' : '' }}">
            Approval User
        </a>

        <a href="{{ route('admin.users') }}" class="tab {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            Management User
        </a>

        <a href="{{ route('profile.edit') }}" class="tab {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            Ganti Password
        </a>
    @elseif(auth()->check() && auth()->user()->role === 'arsiparis_wilker')
        {{-- <a href="{{ route('arsiparis.dashboard') }}"
            class="tab {{ request()->routeIs('arsiparis.dashboard') ? 'active' : '' }}">
            Data Pengajuan Belum Diagendakan
        </a> --}}
        <a href="{{ route('arsiparis.verifikasi') }}"
            class="tab {{ request()->routeIs('arsiparis.verifikasi') ? 'active' : '' }}">
            Data Pengajuan Verifikasi
        </a>
        <a href="{{ route('arsiparis.sudah-diagendakan') }}"
            class="tab {{ request()->routeIs('arsiparis.sudah-diagendakan') ? 'active' : '' }}">
            Data Pengajuan Sudah Diagendakan
        </a>
        <a href="{{ route('arsiparis.surat-masuk') }}"
            class="tab {{ request()->routeIs('arsiparis.surat-masuk') ? 'active' : '' }}">
            Data Surat Masuk
        </a>
        <a href="{{ route('arsiparis.surat-keluar') }}"
            class="tab {{ request()->routeIs('arsiparis.surat-keluar') ? 'active' : '' }}">
            Data Surat Keluar
        </a>
        <a href="{{ route('profile.edit') }}" class="tab {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            Ganti Password
        </a>
    @elseif(auth()->check() && auth()->user()->role === 'kawilker')
        <a href="{{ route('petugas.dashboard') }}"
            class="tab {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
            Data Penagihan
        </a>
        <a href="{{ route('petugas.dashboard.petugas') }}"
            class="tab {{ request()->routeIs('petugas.dashboard.petugas') ? 'active' : '' }}">
            Data Pembayaran
        </a>
        <a href="{{ route('petugas.pemeriksa') }}"
            class="tab {{ request()->routeIs('petugas.pemeriksa') ? 'active' : '' }}">
            Data Petugas Pemeriksa
        </a>
        <a href="{{ route('petugas.approval.index') }}"
            class="tab {{ request()->routeIs('petugas.approval.index') ? 'active' : '' }}">
            Approval User
        </a>

        <a href="{{ route('pengajuan.petugas') }}"
            class="tab {{ request()->routeIs('pengajuan.petugas') ? 'active' : '' }}">
            Semua Data Pengajuan
        </a>
        <a href="{{ route('profile.edit') }}" class="tab {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            Ganti Password
        </a>
    @elseif(auth()->check() && auth()->user()->role === 'bendahara_wilker')
        <a href="{{ route('petugas.dashboard') }}"
            class="tab {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
            Data Penagihan
        </a>
        <a href="{{ route('petugas.pembayaran') }}"
            class="tab {{ request()->routeIs('petugas.pembayaran') ? 'active' : '' }}">
            Data Pembayaran
        </a>
        <a href="{{ route('profile.edit') }}" class="tab {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            Ganti Password
        </a>
        {{-- ================= USER ================= --}}
    @else
        <a href="{{ route('user.dashboard') }}"
            class="tab {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            Pengajuan
        </a>
        <a href="{{ route('profile.edit') }}" class="tab {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            Ganti Password
        </a>

        {{-- <a href="{{ route('user.pengajuan') }}"
            class="tab {{ request()->routeIs('user.pengajuan') ? 'active' : '' }}">
            Pengajuan
        </a>

        <a href="{{ route('user.status') }}"
            class="tab {{ request()->routeIs('user.status') ? 'active' : '' }}">
            Status Pengajuan
        </a> --}}
    @endif

</div>
