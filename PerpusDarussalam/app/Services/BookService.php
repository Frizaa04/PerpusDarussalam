<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BookService
{
    public function createBook(array $data)
    {
        return DB::transaction(function () use ($data) {

            $category = Category::where('nama', $data['kategori'] ?? null)->first();

            $categoryId = $category
                ? $category->id
                : (Category::first()->id ?? 1);

            $cover = $this->uploadCover($data['cover'] ?? null);

            $kodeBuku = $this->generateBookCode(
                $category,
                $data['tahun_terbit'] ?? date('Y'),
                $categoryId
            );

            $book = Book::create([

                'categories_id' => $categoryId,
                'kode_buku' => $kodeBuku,
                'judul' => $data['judul'],
                'penulis' => $data['penulis'] ?? 'Anonim',
                'penerbit' => $data['penerbit'] ?? 'Umum',
                'isbn' => $data['isbn'] ?? '000-0-00-000000-0',
                'tanggal_pembelian' =>
                    $data['tanggal_pembelian']
                    ?? now()->toDateString(),
                'deskripsi' => $data['deskripsi'] ?? null,
                'rak' => $data['rak'] ?? null,
                'tahun_terbit' =>
                    $data['tahun_terbit']
                    ?? date('Y'),
                'stok' => $data['stok'],
                'cover' => $cover
            ]);

            $this->createBookItems(
                $book,
                $data['stok']
            );
            return $book;

        });
    }

    public function updateBook(Book $book, Request $request)
{
    return DB::transaction(function () use ($book, $request) {

        $categoryId = $request->categories_id;

        if (!is_numeric($categoryId)) {
            $category = Category::firstOrCreate([
                'nama' => $categoryId ?? 'Umum'
            ]);

            $categoryId = $category->id;
        }

        $oldStok = $book->stok;
        $newStok = (int) $request->stok;

        $dataToUpdate = [
            'kode_buku'         => $request->kode_buku,
            'categories_id'     => $categoryId,
            'judul'             => $request->judul,
            'penulis'           => $request->penulis,
            'penerbit'          => $request->penerbit,
            'isbn'              => $request->isbn,
            'tanggal_pembelian' => date('Y-m-d', strtotime($request->tanggal_pembelian)),
            'tahun_terbit'      => $request->tahun_terbit,
            'stok'              => $newStok,
            'deskripsi'         => $request->deskripsi ?? null,
            'rak'               => $request->rak ?? null,
        ];

        if ($request->hasFile('cover')) {

            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }

            $dataToUpdate['cover'] = $request
                ->file('cover')
                ->store('covers', 'public');
        }

        $book->update($dataToUpdate);

        if ($newStok > $oldStok) {

            for ($i = $oldStok + 1; $i <= $newStok; $i++) {

                BookItem::create([
                    'book_id'          => $book->id,
                    'nomor_inventaris' => $book->kode_buku . '-INV-' . sprintf('%03d', $i),
                    'kondisi'          => 'baik',
                    'status_pinjam'    => 'tersedia',
                ]);

            }

        } elseif ($newStok < $oldStok) {

            $book->bookItems()
                ->where('status_pinjam', 'tersedia')
                ->orderBy('id', 'desc')
                ->take($oldStok - $newStok)
                ->delete();

        }

        return $book;
    });
}

    public function deleteBook(Book $book)
    {
        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();
    }

    private function uploadCover($file)
    {
        if (!$file) {
            return null;
        }

        return $file->store(
            'covers',
            'public'
        );
    }

    private function generateBookCode($category, $tahun, $categoryId)
    {
        $kategoriSingkatan = $category
            ? strtoupper(substr($category->nama, 0, 3))
            : 'UMM';

        $urutan = Book::where(
            'categories_id',
            $categoryId
        )->count() + 1;

        return $kategoriSingkatan
            . '-'
            . $tahun
            . '-'
            . str_pad($urutan,3,'0',STR_PAD_LEFT);
    }

    private function createBookItems(Book $book, int $stok)
    {
        for($i=1;$i<=$stok;$i++){

            BookItem::create([

                'book_id'=>$book->id,

                'nomor_inventaris'=>
                    $book->kode_buku
                    .'-INV-'
                    .sprintf('%03d',$i),

                'kondisi'=>'baik',

                'status_pinjam'=>'tersedia'

            ]);

        }
    }
}