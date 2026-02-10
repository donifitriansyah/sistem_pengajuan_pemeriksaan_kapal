<!DOCTYPE html>
<html>
<head>
    <title>Invoice Pemeriksaan Kapal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body text-center">

            <h4 class="mb-3">Invoice Pemeriksaan Kapal</h4>

            <table class="table table-bordered text-start">
                <tr>
                    <th>Nama Kapal</th>
                    <td>{{ $penagihan->pengajuan->nama_kapal }}</td>
                </tr>
                <tr>
                    <th>Jenis Dokumen</th>
                    <td>{{ $penagihan->pengajuan->jenis_dokumen }}</td>
                </tr>
                <tr>
                    <th>Wilayah Kerja</th>
                    <td>{{ $penagihan->pengajuan->wilayah_kerja }}</td>
                </tr>
                <tr>
                    <th>Total Tagihan</th>
                    <td>
                        Rp {{ number_format($penagihan->total_tarif, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-success">Lunas</span>
                    </td>
                </tr>
            </table>

            <a href="{{ route('invoice.download', $penagihan->id) }}"
               class="btn btn-success mt-3">
                ⬇️ Download PDF Invoice
            </a>

        </div>
    </div>
</div>

</body>
</html>
