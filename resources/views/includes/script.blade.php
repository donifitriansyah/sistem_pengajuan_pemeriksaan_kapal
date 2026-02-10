<script>
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const invoiceTab = document.getElementById('invoiceTab');

    function switchTab(tab) {

        // Hapus semua active
        document.querySelectorAll('.tab-content').forEach(e => {
            e.classList.remove('active');
        });

        document.querySelectorAll('.tab').forEach(e => {
            e.classList.remove('active');
        });

        // Clear error messages (jika ada)
        const errorBox = document.getElementById('errorBox');
        const regErrorBox = document.getElementById('regErrorBox');

        if (errorBox) errorBox.classList.remove('show');
        if (regErrorBox) regErrorBox.classList.remove('show');

        // Aktifkan tab sesuai klik
        if (tab === 'login') {
            loginTab.classList.add('active');
            document.querySelectorAll('.tab')[0].classList.add('active');
        }

        if (tab === 'register') {
            registerTab.classList.add('active');
            document.querySelectorAll('.tab')[1].classList.add('active');
        }

        if (tab === 'invoice') {
            invoiceTab.classList.add('active');
            document.querySelectorAll('.tab')[2].classList.add('active');
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif


@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        text: '{{ $errors->first() }}',
        confirmButtonText: 'OK'
    });
</script>
@endif


@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK'
    });
</script>
@endif

