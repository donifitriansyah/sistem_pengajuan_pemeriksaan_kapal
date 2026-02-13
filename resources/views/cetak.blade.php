<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 4px 0;
            font-size: 13px;
        }

        .divider {
            border-top: 2px solid #000;
            margin: 20px 0;
        }

        .info-table,
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px 0;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .detail-table th {
            background: #f0f0f0;
            text-align: left;
        }

        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            background: #28a745;
            color: #fff;
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .kop {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="invoice-container">

        <!-- HEADER -->
        <div class="kop">
            <table width="100%">
                <tr>
                    <td width="15%" align="left">
                        <img src="{{ public_path('images/logo-kemenkes.png') }}" style="width:90px">
                    </td>
                    <td width="85%" align="center">
                        <div style="font-size:16px; font-weight:bold;">
                            KEMENTERIAN KESEHATAN REPUBLIK INDONESIA
                        </div>
                        <div style="font-size:15px; font-weight:bold;">
                            BALAI KEKARANTINAAN KESEHATAN KELAS I PONTIANAK
                        </div>
                        <div style="font-size:13px;">
                            Wilayah Kerja {{ $penagihan->pengajuan->wilayah_kerja }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>


        <div class="divider"></div>

        <!-- INFO -->
        <table class="info-table">
            <tr>
                <td><strong>No Invoice</strong></td>
                <td>: INV-{{ $pengajuan->kode_bayar }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>:
                    {{ optional($pengajuan->penagihan->pembayaran)->created_at
                        ? $pengajuan->penagihan->pembayaran->created_at->format('d-m-Y')
                        : '-' }}
                </td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>: <span class="badge">LUNAS</span></td>
            </tr>
        </table>

        <br>

        <!-- DETAIL KAPAL -->
        <table class="detail-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Uraian</th>
                    <th width="25%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Nama Kapal</td>
                    <td>{{ $pengajuan->nama_kapal }}</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Lokasi Pemeriksaan</td>
                    <td>{{ $pengajuan->lokasi_kapal }}</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Jenis Dokumen</td>
                    <td>{{ $pengajuan->jenis_dokumen }}</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Wilayah Kerja</td>
                    <td>{{ $pengajuan->wilayah_kerja }}</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Tanggal Pemeriksaan</td>
                    <td>{{ \Carbon\Carbon::parse($pengajuan->tgl_estimasi_pemeriksaan)->format('d-m-Y') }}</td>
                </tr>
            </tbody>
        </table>

        <br>

        <!-- TOTAL -->
        <p class="total">
            Total Pembayaran :
            Rp {{ number_format($pengajuan->penagihan->total_tarif, 0, ',', '.') }}
        </p>

        <!-- FOOTER -->
        <div class="footer">
            <p>Petugas Verifikasi</p>
            <br><br>
            <strong>{{ optional($pengajuan->penagihan->pembayaran->petugas)->name ?? 'Petugas' }}</strong>
        </div>

        <!-- BUTTON -->
        <div class="no-print" style="margin-top:20px; text-align:center;">
            <button onclick="window.print()" class="btn btn-primary">
                🖨 Cetak Invoice
            </button>
        </div>

    </div>

</body>

</html>
