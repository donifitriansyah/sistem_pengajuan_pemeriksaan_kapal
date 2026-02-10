<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Terverifikasi</title>
    <style>
        @page { size: A4; margin: 0; }

        html, body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 50%, #80cbc4 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 720px;
            padding: 20px;
        }

        .card {
            width: 100%;
            background: #fff;
            border-radius: 12px;
            padding: 30px 30px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            border: 1px solid #ddd;
        }

        .kop {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .kop img {
            width: 200px;
            margin-right: 20px;
        }

        .kop-text div {
            font-size: 12px;
            font-weight: bold;
            line-height: 1.2;
        }

        h3 {
            text-align: center;
            color: green;
            margin: 15px 0;
            font-size: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .status {
            font-weight: bold;
            color: green;
        }

        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            color: #555;
        }

        /* QR Code Section */
        .qr-container {
            margin-top: 20px;
            text-align: center;
        }

        .qr-container img {
            width: 100px;
            height: 100px;
            border: 5px solid white;
            border-radius: 12px;
        }

        .qr-container span {
            display: block;
            margin-top: 10px;
            font-size: 12px;
        }

        /* Responsive */
        @media(max-width: 480px) {
            .card {
                padding: 20px;
            }

            .kop img {
                width: 80px;
                margin-right: 10px;
            }

            h3 {
                font-size: 18px;
            }

            .qr-container img {
                width: 140px;
                height: 140px;
            }
        }

    </style>
</head>
<body>

<div class="container">

    <!-- INVOICE CARD -->
    <div class="card">

        <!-- KOP SURAT -->
        <div class="kop">
            <img src="{{ asset('logokemenkes.png') }}" alt="Logo Kemenkes">
            <div class="kop-text">
                <div>KEMENTERIAN KESEHATAN REPUBLIK INDONESIA</div>
                <div>KANTOR KESEHATAN PELABUHAN</div>
                <div>Wilayah Kerja {{ $penagihan->pengajuan->wilayah_kerja }}</div>
            </div>
        </div>

        <h3>✅ INVOICE TERVERIFIKASI</h3>
        <hr>

        <!-- INFO UTAMA -->
        <table>
            <tr>
                <td width="30%">Nama Kapal</td>
                <td>: {{ $penagihan->pengajuan->nama_kapal }}</td>
            </tr>
            <tr>
                <td>Perusahaan</td>
                <td>: {{ $penagihan->pengajuan->user->nama_perusahaan }}</td>
            </tr>
            <tr>
                <td>Wilayah Kerja</td>
                <td>: {{ $penagihan->pengajuan->wilayah_kerja }}</td>
            </tr>
            <tr>
                <td>Tanggal Pemeriksaan</td>
                <td>: {{ \Carbon\Carbon::parse($penagihan->pengajuan->tgl_estimasi_pemeriksaan)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Total Pembayaran</td>
                <td>: <strong>Rp {{ number_format($penagihan->total_tarif,0,',','.') }}</strong></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: <span class="status">LUNAS</span></td>
            </tr>
        </table>

        <hr>

        <!-- FOOTER -->
        <div class="footer">
            Invoice ini sah dan terdaftar di sistem<br>
            Kantor Kesehatan Pelabuhan
        </div>

    </div>

    <!-- QR CODE -->
    <div class="qr-container">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ route('invoice.show', $penagihan->id) }}" alt="QR Code">
        <span>Scan untuk melihat invoice</span>
    </div>

</div>

</body>
</html>
