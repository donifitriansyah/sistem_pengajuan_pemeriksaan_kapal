<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body { font-family: Arial; font-size: 12px; }
        table { width: 100%; }
    </style>
</head>
<body onload="window.print()">

<h2>INVOICE PEMBAYARAN</h2>
<hr>

<table>
    <tr>
        <td>No Invoice</td>
        <td>: INV-{{ $penagihan->kode_bayar }}</td>
    </tr>
    <tr>
        <td>Nama Kapal</td>
        <td>: {{ $penagihan->pengajuan->nama_kapal }}</td>
    </tr>
    <tr>
        <td>Wilker</td>
        <td>: {{ $penagihan->pengajuan->wilayah_kerja }}</td>
    </tr>
    <tr>
        <td>Total</td>
        <td>: Rp {{ number_format($penagihan->total_tarif,0,',','.') }}</td>
    </tr>
</table>

<br>

<table>
    <tr>
        <td>
            <strong>Status:</strong><br>
            LUNAS
        </td>
        <td align="right">
            {!! QrCode::size(120)->generate(json_encode([
                'invoice' => 'INV-'.$penagihan->kode_bayar,
                'kapal' => $penagihan->pengajuan->nama_kapal,
                'total' => $penagihan->total_tarif,
                'status' => 'LUNAS'
            ])) !!}
            <br>
            <small>Scan untuk verifikasi</small>
        </td>
    </tr>
</table>

</body>
</html>
