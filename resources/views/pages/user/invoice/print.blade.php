<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 14px;
            background: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 720px;
            margin: auto;
            background: #fff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border: 1px solid #ddd;
        }

        .kop {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .kop img {
            width: 300px;
            height: auto;
        }

        .kop-text {
            margin-left: 20px;
        }

        .kop-text div {
            font-weight: bold;
            line-height: 1.5;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        td,
        th {
            padding: 4px;
            vertical-align: top;
        }

        .rincian th,
        .rincian td {
            border: 1px solid #000;
            padding: 6px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
        }

        .qr img {
            width: 120px;
            margin-top: 10px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .container {
                border: none;
                border-radius: 0;
                box-shadow: none;
                padding: 0;
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="container">

        <!-- KOP SURAT -->
        <div class="kop">
            <img src="{{ asset('logokemenkes.png') }}" alt="Logo Kemenkes">
            <div class="kop-text">
                <div>KEMENTERIAN KESEHATAN REPUBLIK INDONESIA</div>
                <div>KANTOR KESEHATAN PELABUHAN</div>
                <div>Wilayah Kerja {{ $penagihan->pengajuan->wilayah_kerja }}</div>
            </div>
        </div>

        <div class="judul" style="margin-top: -30px">KWITANSI PEMBAYARAN</div>
        <p><strong>{{ $penagihan->nomor_surat_keluar }}</strong></p>



        <!-- INFO UTAMA -->
        <table>
            <tr>
                <td width="30%">Sudah Terima Dari</td>
                <td width="2%">:</td>
                <td><strong>{{ $penagihan->pengajuan->user->nama_perusahaan ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Uang Sejumlah</td>
                <td>:</td>
                <td><strong>Rp {{ number_format($penagihan->total_tarif, 0, ',', '.') }}</strong></td>
            </tr>

            <tr>
                <td>Untuk Pembayaran</td>
                <td>:</td>
                <td>
                    ({{ $penagihan->pengajuan->jenis_dokumen }})
                </td>
            </tr>
        </table>

        <!-- DATA KAPAL -->
        <table>
            <tr>
                <td width="30%">Nama Kapal</td>
                <td width="2%">:</td>
                <td>{{ $penagihan->pengajuan->nama_kapal }}</td>
            </tr>
            <tr>
                <td>Lokasi Kapal</td>
                <td>:</td>
                <td>{{ $penagihan->pengajuan->lokasi_kapal }}</td>
            </tr>
            <tr>
                <td>Wilayah Kerja</td>
                <td>:</td>
                <td>{{ $penagihan->pengajuan->wilayah_kerja }}</td>
            </tr>
            <tr>
                <td>Tanggal Pemeriksaan</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($penagihan->pengajuan->tgl_estimasi_pemeriksaan)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Jenis Pembiayaan</td>
                <td>:</td>
                <td>{{ $jenis_tarif_name }}</td>
            </tr>
            <tr>
                <td>Waktu Mulai</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($penagihan->waktu_mulai)->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Waktu Selesai</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($penagihan->waktu_selesai)->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Durasi (Hari)</td>
                <td>:</td>
                <td>{{ $days_difference }} Hari</td> <!-- Display rounded number of days -->
            </tr>



        </table>
        <p>
        <p>
            Sesuai dengan : <br>
            1. Peraturan Pemerintah RI Nomor 64 Tahun 2019 <br>
            2. Peraturan Menteri Keuangan RI Nomor 113 Tahun 2012 <br>
            3. Peraturan Menteri Keuangan RI Nomor 119 Tahun 2023 <br>
            4. Peraturan Menteri Keuangan RI Nomor 45 Tahun 2024 <br>
            5. Peraturan Menteri Keuangan RI Nomor 32 Tahun 2025 <br>
            6. Keputusan Menteri Kesehatan RI Nomor HK.01.07/MENKES/898/2025 <br>
            7. Peraturan Direktur Jenderal Perbendaharaan Nomor PER-22/PB/2013 <br>
            Dengan rincian sebagai berikut : <br>
        </p>
        </p>

        <!-- RINCIAN -->
        <table class="rincian">
            <tr>
                <th width="10%">Uraian</th>
                <th width="30%">Jumlah per Petugas</th>
                <th width="30%">Jumlah Total</th>
            </tr>
            <tr>
                <td>Transportasi</td>
                <td>Rp {{ number_format($total_transportasi_per_petugas, 0, ',', '.') }} x {{ $jumlah_petugas }}
                    Petugas x {{ $days_difference }} Hari</td>
                <td>Rp
                    {{ number_format($total_transportasi_per_petugas * $jumlah_petugas * $days_difference, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Uang Harian</td>
                <td>Rp {{ number_format($total_uang_harian_per_petugas, 0, ',', '.') }} x {{ $jumlah_petugas }}
                    Petugas x {{ $days_difference }} Hari</td>
                <td>Rp
                    {{ number_format($total_uang_harian_per_petugas * $jumlah_petugas * $days_difference, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Penginapan</td>
                <td>Rp {{ number_format($total_penginapan_per_petugas, 0, ',', '.') }} x {{ $jumlah_petugas }} Petugas
                    x {{ $days_difference }} Hari</td>
                <td>Rp
                    {{ number_format($total_penginapan_per_petugas * $jumlah_petugas * $days_difference, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <th>Total</th>
                <th></th>
                <th>Rp {{ number_format($total, 0, ',', '.') }}</th>
            </tr>
        </table>

        <!-- FOOTER QR -->
        <div class="footer">
            <div class="qr">
                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ route('invoice.verify', $penagihan->id) }}">
                <div style="font-size:12px; text-align:center;">Scan untuk verifikasi invoice</div>
            </div>
        </div>

    </div>

</body>

</html>
