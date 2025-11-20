<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; // class laravel excel
use App\Exports\MovieExport;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use Yajra\DataTables\Facades\DataTables; // class laravel yajra : datatables

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movie.index', compact('movies'));
    }

    public function chartData()
    {
        $movieActive = Movie::where('activated', 1)->count();
        $movieNonActive = Movie::where('activated', 0)->count();
        // karena chart hanya perlu jumlah, jd hitung dgn count()
        $data = [$movieActive, $movieNonActive];
        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required|numeric',
            // mimes -> bentuk file yang di izinkan untuk upload
            'poster' => 'required|mimes:jpg,jpeg,png,webp,svg',
            'description' => 'required|min:10'
        ], [
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'Genre film harus diisi',
            'director.required' => 'Sutradara wajib diisi',
            'age_rating.required' => 'Usia Minimal penonton harus diisi',
            'age_rating.numeric' => 'Usia Minimal penonton harus diisi dengan angka',
            'poster.required' => 'Poster harus diisi',
            'poster.mimes' => 'Poster harus diisi dengan JPG/JPEG/PNG/WEBP/SVG',
            'description.required' => 'Sinopsis film harus diisi',
            'description.min' => 'Sinopsis film harus diisi minimal 10 karakter',
        ]);
        // ambil file yang di upload = $request->file('nama_input')
        $gambar = $request->file('poster');
        // buat nama baru di filenya, agar menghindari nama file yang sama
        // nama file yg diinginkan = <random>-poster.png
        // getClientOriginalExtension() = mengambil ekstensi file (png/jpg/dll)
        $namaGambar = Str::random(5) . "-poster." . $gambar->getClientOriginalExtension();
        // simpan file ke storage, nama file gunakan nama file baru
        // storeAs('namaFolder', #namafile, 'public')
        $path = $gambar->storeAs('poster', $namaGambar, 'public');

        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $path, // $path beriis lokasi file yg disimpan dr storeAs()
            'description' => $request->description,
            'activated' => 1
        ]);

        if ($createData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil membuat data film');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $movie = Movie::find($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required|numeric',
            // mimes -> bentuk file yang di izinkan untuk upload
            'poster' => 'mimes:jpg,jpeg,png,webp,svg',
            'description' => 'required|min:10'
        ], [
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'Genre film harus diisi',
            'director.required' => 'Sutradara wajib diisi',
            'age_rating.required' => 'Usia Minimal penonton harus diisi',
            'age_rating.numeric' => 'Usia Minimal penonton harus diisi dengan angka',
            'poster.mimes' => 'Poster harus diisi dengan JPG/JPEG/PNG/WEBP/SVG',
            'description.required' => 'Sinopsis film harus diisi',
            'description.min' => 'Sinopsis film harus diisi minimal 10 karakter',
        ]);
        // data sebelumnya
        $movie = Movie::find($id);
        if ($request->file('poster')) {
            // storage_path() : cek apakah file sblmnya ada di folder storage/app/public
            $fileSebelumnya = storage_path('app/public/' . $movie['poster']);
            if ($fileSebelumnya) {
                // hapus file sebelumnya
                unlink($fileSebelumnya);
            }

            // ambil file yang di upload = $request->file('nama_input')
            $gambar = $request->file('poster');
            // buat nama baru di filenya, agar menghindari nama file yang sama
            // nama file yg diinginkan = <random>-poster.png
            // getClientOriginalExtension() = mengambil ekstensi file (png/jpg/dll)
            $namaGambar = Str::random(5) . "-poster." . $gambar->getClientOriginalExtension();
            // simpan file ke storage, nama file gunakan nama file baru
            // storeAs('namaFolder', #namafile, 'public')
            $path = $gambar->storeAs('poster', $namaGambar, 'public');
        }

        $updateData = Movie::where('id', $id)->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            // ?? : sebelum ?? itu (if) ?? sesudah itu (else)
            // kalau ada $path (poster baru), ambil data baru, kalau
            'poster' => $path ?? $movie['poster'], // $path berisi lokasi file yg disimpan dr storeAs()
            'description' => $request->description,
            'activated' => 1
        ]);

        if ($updateData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengganti data film');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $schedules = Schedule::where('movie_id', $id)->count();
        if ($schedules) {
            return redirect()->route('admin.movies.index')->with('failed', 'Tidak dapat menghapus data bioskop! Data tertaut dengan jadwal tayang');
        }

        $movie = Movie::find($id);
        if ($movie && $movie->poster) {
            $filePath = storage_path('/app/public/' . $movie['poster']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $deleteDataFilm = Movie::where('id', $id)->delete();
        if ($deleteDataFilm) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil menghapus data film!');
        } else {
            return redirect()->back()->with('failed', 'Gagal menghapus data film!');
        }
    }

    public function home()
    {
        // where ('field', 'value') -> mencari data
        // get() -> mengambil semua data dari hasil filter
        // mengurutkan -> orderBy('field', 'ASC/DESC') : ASC (a-z, 0-9, terlama-terbaru), DESC (z-a, 9-0, terbaru-terlama)
        // limit(angka) -> mengambil sejumlah yg ditentukan
        $movies = Movie::where('activated', 1)->orderBy('created_at', 'DESC')->limit(4)->get();
        return view('home', compact('movies'));
    }

    public function homeAllMovies(Request $request)
    {
        // ambil value input search name="search_movie"
        $title = $request->search_movie;
        // cek jika input search ada isinya, maka cari data
        if ($title != "") {
            // LIKE : mencari data yang mengandung kata tertentu
            // % depan : mencari kata belakang, % belakang : mencari kata depan, % depan belakang : mencari kata di depan dan belakang
            $movies = Movie::where('title', 'LIKE', '%' . $title . '%')->where('activated', 1)->orderBy('created_at', 'DESC')->get();
        } else {
            $movies = Movie::where('activated', 1)->orderBy('created_at', 'DESC')->get();
        }

        return view('home_movies', compact('movies'));
    }

    public function movieSchedules($movie_id, Request $request)
    {
        // ambil data dari href="?price=ASC" tanda tanya
        $priceSort = $request->price;
        if ($priceSort) {
            // karna price adanya di schedules bukan movie, jadi urukan datanya dari schedules (relasi)
            $movie = Movie::where('id', $movie_id)->with(['schedules' => function ($q) use ($priceSort) {
                // 'schedules' => function ($q) {...} : melakukan filter pada relasi
                // $q yang mewakillkan model Schedule
                $q->orderBy('price', $priceSort);
            }, 'schedules.cinema'])->first();
        } else {
            $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
        }
        // ambil data film beserta schedule dan bioskop pada schedule
        // 'schedules.cinema' -> karna relasi cinea adnya di schedules bukan movie
        // first() :
        return view('schedule.detail-film', compact('movie'));
    }

    public function nonactivated($id)
    {

        // Non-aktifkan film
        $updateDataFilm = Movie::where('id', $id)->update([
            'activated' => 0
        ]);

        if ($updateDataFilm) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil menon-aktifkan film');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    // eksport to excel
    public function export()
    {
        // nama file yg akan diunduh
        // xlsx / csv
        $fileName = 'data-film.xlsx';
        // proses unduh
        return Excel::download(new MovieExport, $fileName);
    }

    // data sampah
    public function trash()
    {
        $movieTrash = Movie::onlyTrashed()->get();
        return view('admin.movie.trash', compact('movieTrash'));
    }

    // restore
    public function restore($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->restore();
        return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengembalikan data!');
    }

    // delete permanen
    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data secara permanen!');
    }

    // datatables
    public function datatables()
    {
        $movies = Movie::query();
        return DataTables::of($movies)
            ->addIndexColumn()
            ->addColumn('poster_img', function ($item) {
                $url = asset('storage/' . $item->poster);
                return '<img src="' . $url . '" width="70">';
            })
            ->addColumn('activated_badge', function ($item) {
                if ($item->activated) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-danger">Non-Aktif</span>';
                }
            })
            ->addColumn('action', function ($item) {
                $btnDetail = '<button type="button" class="btn btn-secondary" onclick=\'showModal(' . json_encode($item) . ')\'>Detail</button>';
                $btnEdit = '<a href="' . route('admin.movies.edit', $item->id) . '" class="btn btn-primary">Edit</a>';
                $btnDelete = '<form action="' . route('admin.movies.delete', $item->id) . '" method="POST" style="display:inline-block">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger">Hapus</button>
                          </form>';
                $btnNonAktif = '';
                if ($item->activated) {
                    $btnNonAktif = '<form action="' . route('admin.movies.non-activated', $item->id) . '" method="POST" style="display:inline-block">
                            ' . csrf_field() . method_field('PATCH') . '
                            <button type="submit" class="btn btn-warning">Non-Aktif</button>
                          </form>';
                }

                return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnDetail . $btnEdit . $btnDelete . $btnNonAktif . '</div>';
            })
            ->rawColumns(['poster_img', 'activated_badge', 'action'])
            ->make(true);
    }
}
