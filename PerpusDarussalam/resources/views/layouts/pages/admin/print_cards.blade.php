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
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: #fff;
            color: #111;
            margin: 0;
            padding: 0;
        }
        .print-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); 
            gap: 15px;
            justify-content: center;
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            border: 1px solid #d1d5db; 
            box-sizing: border-box;
            padding: 6px 5mm 0 5mm; 
            background: #ffffff;
            position: relative;
            page-break-inside: avoid;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .card::before {
            content: "MDIBS";
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 55px;
            font-weight: 900;
            color: rgba(0, 77, 64, 0.03);
            z-index: 0;
            pointer-events: none;
            letter-spacing: 5px;
        }

        .card-header {
            background: linear-gradient(135deg, #004d40, #00695c);
            color: white;
            text-align: center;
            padding: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 4px;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }
        .card-sub-header {
            font-size: 7.5px;
            text-align: center;
            margin-top: 2px;
            margin-bottom: 4px;
            color: #004d40;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            position: relative;
            z-index: 1;
            border-bottom: 1.5px solid #004d40;
            padding-bottom: 3px;
        }

        .card-body {
            display: flex;
            gap: 8px; 
            align-items: center; 
            position: relative;
            z-index: 1;
        }
        
        .logo-box {
            width: 27mm;
            height: 27mm;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            font-weight: bold;
            color: #004d40;
            flex-shrink: 0;
            border-radius: 3px;
            overflow: hidden;
            text-align: center;
            margin-top: 12px; 
            margin-left: 20px;
        }
        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain; 
        }
        
        .info-table {
            font-size: 7.8px; 
            width: 100%;
            border-collapse: collapse;
            margin-left: 20px;
        }
        .info-table td {
            padding: 0.8px 0; 
            vertical-align: top;
            color: #1f2937;
        }
        .info-table td.label {
            width: 32%;
            font-weight: bold;
            color: #374151;
        }
        
        .barcode-section {
            background-color: #ffffff;
            text-align: center;
            position: absolute;
            bottom: 2px;
            left: 0;
            right: 0;
            padding: 0 10mm;
            z-index: 1;
        }
        .barcode-section img {
            width: 85%; 
            height: 9.5mm; 
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        @media print {
            body {
                background: transparent;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10mm;
            }
            .card {
                border: 1px solid #9ca3af !important; 
                page-break-inside: avoid;
                break-inside: avoid;
            }
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
                $noInduk = $user->nisn ?? ($user->nik ?? '000000');
                $masaBerlaku = \Carbon\Carbon::now()->addYear()->translatedFormat('d F Y');
            @endphp
            <div class="card">
                <div class="card-header">
                    Kartu Anggota Perpustakaan
                </div>
                <div class="card-sub-header">
                    Madrasah Darussalam Internasional Boarding School
                </div>
                
                <div class="card-body">
                    <!-- Logo Sekolah -->
                    <div class="logo-box">
                        <img src="{{ asset('image/123.png') }}" alt="Logo Sekolah" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span style="display:none;">LOGO MDIBS</span>
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
                            <td class="label">Status</td>
                            <td>: {{ ucfirst($user->status) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jenjang</td>
                            <td>: {{ $user->jenjang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kelamin</td>
                            <td>: {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : ($user->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Masa Berlaku</td>
                            <td>: {{ $user->masaBerlakuFormatted ?? $masaBerlaku }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Bagian Barcode -->
                <div class="barcode-section">
                    <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $noInduk }}&code=Code128&dpi=300" alt="Barcode">
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>