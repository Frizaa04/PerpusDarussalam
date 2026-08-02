<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Semua Barcode - {{ $book->title }}</title>
    <!-- Panggil library JsBarcode via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
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

        /* Styling untuk elemen SVG barcode */
        .barcode-container svg {
            width: 100%;
            height: 14mm;
            display: block;
            margin: 0 auto;
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
            z-index: 999;
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
        @foreach ($book->bookItems ?? [] as $index => $item)
            <div class="barcode-card">
                <div class="library-name">Perpustakaan Madrasah</div>
                <div class="book-title" title="{{ $book->judul }}">{{ $book->judul }}</div>

                <div class="barcode-container">
                    <!-- Menggunakan tag SVG dengan ID unik agar digambar otomatis oleh JsBarcode -->
                    <svg id="barcode-{{ $index }}"></svg>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Script untuk men-generate barcode secara otomatis ke semua elemen SVG -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach ($book->bookItems ?? [] as $index => $item)
                try {
                    JsBarcode("#barcode-{{ $index }}", "{{ $item->nomor_inventaris }}", {
                        format: "CODE128",
                        lineColor: "#000",
                        width: 1.5,
                        height: 40,
                        displayValue: true, // Menampilkan teks nomor inventaris di bawah barcode
                        fontSize: 12,
                        margin: 0
                    });
                } catch (e) {
                    console.error("Gagal generate barcode untuk: {{ $item->nomor_inventaris }}");
                }
            @endforeach
        });
    </script>

</body>

</html>