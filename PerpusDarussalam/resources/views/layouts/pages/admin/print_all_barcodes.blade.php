<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Semua Barcode - {{ $book->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .barcode-card {
            width: 7cm;
            padding: 8px;
            background: #fff;
            border: 1.5px dashed #004d40;
            border-radius: 6px;
            text-align: center;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .library-name {
            font-size: 9px;
            font-weight: bold;
            color: #004d40;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .book-title {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .barcode-container img {
            width: 100%;
            height: 14mm;
            object-fit: contain;
            display: block;
        }

        .barcode-code {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #000;
            margin-top: 3px;
        }

        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #004d40;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="no-print">🖨️ Cetak Semua Barcode</button>

    <div class="container">
        @foreach ($book->bookItems ?? [] as $item)
            <div class="barcode-card">
                <div class="library-name">Perpustakaan Madrasah</div>
                <div class="book-title" title="{{ $book->judul }}">{{ $book->judul }}</div>

                <div class="barcode-container">
                    <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $item->nomor_inventaris }}&code=Code128&dpi=300&imagetype=jpg"
                        alt="Barcode">
                </div>

            </div>
        @endforeach
    </div>

</body>

</html>
