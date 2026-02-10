@extends('layouts.back')
@section('content')
    <style>
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 40px;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
            font-size: 18px;
        }
    </style>
    <div class="card">

        <div class="logo-container">
            <img src="https://lh3.googleusercontent.com/d/1GypYsK22OTiKiLfW0WWnJ779Wx209LDe" alt="Logo Kemenkes"
                onerror="this.style.display='none'">
        </div>

        <h1>Sistem Pengajuan<br>Pemeriksaan Kapal</h1>

        <div class="tabs">
            <button class="tab active" onclick="switchTab('login')">Login</button>
            <button class="tab" onclick="switchTab('register')">Registrasi</button>
            {{-- <button class="tab" onclick="switchTab('invoice')">Cek Invoice</button> --}}
        </div>

        <!-- LOGIN -->
        <div id="loginTab" class="tab-content active">

            {{-- ERROR MESSAGE --}}
            @if (session('error'))
                <div class="error-box" style="display:block;">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="error-box" style="display:block;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" id="loginBtn">
                    Login
                </button>

            </form>
        </div>

        <!-- REGISTER -->
        <div id="registerTab" class="tab-content">

            @if ($errors->any())
                <div class="error-box" style="display:block;">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="success-box">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>

                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" required>

                        <span class="toggle-password" onclick="togglePassword('password', this)">
                            👁️
                        </span>
                    </div>
                </div>


                <div class="form-group">
                    <label>Konfirmasi Password</label>

                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation" required>

                        <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                            👁️
                        </span>
                    </div>
                </div>


                <div class="form-group">
                    <label>Nama Perusahaan</label>
                    <input type="text" name="nama_perusahaan" required>
                </div>

                <div class="form-group">
                    <label>Nama Petugas</label>
                    <input type="text" name="nama_petugas" required>
                </div>

                <div class="form-group">
                    <label>No. HP</label>
                    <div class="phone-input-wrapper">
                        <span class="phone-prefix">+62</span>

                        <input id="regNoHP" name="no_hp" type="tel" placeholder="812345678" pattern="[0-9]{9,12}"
                            maxlength="12" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Wilker</label>
                    <select name="wilayah_kerja" required>
                        <option value="">-- Pilih Wilker --</option>
                        <option value="Dwikora">Dwikora</option>
                        <option value="Kijing">Kijing</option>
                        <option value="Padang Tikar">Padang Tikar</option>
                        <option value="Teluk Batang">Teluk Batang</option>
                        <option value="Ketapang">Ketapang</option>
                        <option value="Kendawangan">Kendawangan</option>
                    </select>
                </div>

                <button type="submit">
                    Daftar
                </button>

            </form>
        </div>



        {{-- <!-- CEK INVOICE -->
        <div id="invoice" class="tab-content">
            <div class="form-group">
                <label>Kode Bayar</label>
                <div class="invoice-input-row">
                    <input id="kodeBayar" placeholder="Masukkan Kode Bayar">
                    <button type="button" class="submit-btn" id="cekBtn" onclick="cekInvoice()">
                        <span class="btn-text">Cek</span>
                        <div class="spinner"></div>
                    </button>
                </div>
            </div>

            <div id="errorMsg" class="invoice-error"></div>

            <div id="preview" class="invoice-box">
                <div class="invoice-row"><span class="invoice-label">Nama Kapal</span><span id="namaKapal">-</span>
                </div>
                <div class="invoice-row"><span class="invoice-label">Jenis Tarif</span><span id="jenisTarif">-</span>
                </div>
                <div class="invoice-row"><span class="invoice-label">Lokasi</span><span id="lokasi">-</span></div>
                <div class="invoice-row"><span class="invoice-label">Agent</span><span id="agent">-</span></div>
                <div class="invoice-row"><span class="invoice-label">Tarif</span><span id="tarif">-</span></div>
                <div class="invoice-row"><span class="invoice-label">Status</span><span id="status">-</span></div>
                <button onclick="downloadInvoice()">📄 Download PDF</button>
            </div>
        </div> --}}

    </div>

    <!-- CONFIRM POPUP (BEFORE SUBMIT) -->
    <div id="confirmPopup" class="popup-overlay">
        <div class="popup-content">
            <div class="popup-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2>Konfirmasi Pendaftaran</h2>
            <p>Pastikan semua data yang Anda masukkan sudah benar. Lanjutkan proses pendaftaran?</p>
            <div class="popup-buttons">
                <button class="popup-btn-cancel" onclick="closeConfirmPopup()">Batal</button>
                <button class="popup-btn-confirm" onclick="doRegister()">Ya, Daftar</button>
            </div>
        </div>
    </div>

    <!-- SUCCESS POPUP (AFTER SUBMIT) -->
    <div id="successPopup" class="popup-overlay">
        <div class="popup-content">
            <div class="popup-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2>Registrasi Berhasil!</h2>
            <p>Akun Anda sedang dalam proses verifikasi oleh petugas kami.</p>
            <button class="popup-btn" onclick="closeSuccessPopup()">Mengerti</button>
        </div>
    </div>
    <script>
        function togglePassword(id, el) {

            const input = document.getElementById(id);

            if (input.type === "password") {
                input.type = "text";
                el.textContent = "🙈";
            } else {
                input.type = "password";
                el.textContent = "👁️";
            }

        }
    </script>
@endsection
