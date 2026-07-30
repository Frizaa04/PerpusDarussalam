<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Anggota Perpustakaan</title>
    <style>
        /* Pengaturan Kertas A4 & Reset */
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .print-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); 
            gap: 15px;
            justify-content: center;
        }

        /* Desain Ukuran */
        .card {
            width: 85.6mm;
            height: 54mm;
            border: 1px dashed #999; 
            box-sizing: border-box;
            padding: 8px;
            background: #ffffff;
            position: relative;
            page-break-inside: avoid;
            overflow: hidden;
            border-radius: 4px;
        }

        /* Header Kartu */
        .card-header {
            background-color: #004d40;
            color: white;
            text-align: center;
            padding: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 2px;
        }
        .card-sub-header {
            font-size: 8px;
            text-align: center;
            margin-bottom: 6px;
            color: #333;
        }

        /* Konten Kartu */
        .card-body {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }
        .photo-box {
            width: 22mm;
            height: 28mm;
            border: 1px solid #004d40;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: #555;
            flex-shrink: 0;
        }
        .info-table {
            font-size: 8.5px;
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }
        .info-table td.label {
            width: 32%;
            font-weight: bold;
        }
        .barcode-section {
            text-align: center;
            margin-top: 5px;
            width: 100%;
        }
        .barcode-section img {
            width: 92%; 
            height: 10mm; 
            object-fit: contain; 
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .barcode-text {
            font-size: 8px;
            letter-spacing: 1px;
            margin-top: 2px;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="print-container">
        @foreach($users as $user)
            @php
                $noInduk = $user->nis ?? ($user->nip ?? ($user->nik ?? '000000'));
            @endphp
            <div class="card">
                <div class="card-header">
                    Kartu Anggota Perpustakaan
                </div>
                <div class="card-sub-header">
                    Sistem Layanan Mandiri Madrasah
                </div>
                
                <div class="card-body">
                    <!-- Foto Anggota -->
                    <div class="photo-box">
                        @if(!empty($user->foto))
                            <img src="{{ asset('storage/' . $user->foto) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            FOTO
                        @endif
                    </div>

                    <!-- Informasi Identitas -->
                    <table class="info-table">
                        <tr>
                            <td class="label">Nama</td>
                            <td>: {{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td class="label">No. Induk</td>
                            <td>: {{ $noInduk }}</td>
                        </tr>
                        <tr>
                            <td class="label">Peran</td>
                            <td>: {{ ucfirst($user->role) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kelamin</td>
                            <td>: {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : ($user->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Alamat</td>
                            <td>: {{ $user->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Bagian Barcode Berisi No Identitas -->
                <div class="barcode-section">
                    <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $noInduk }}&code=Code128&dpi=150" alt="Barcode">
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>