<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// memberi nama untuk route agar bisa dipanggiil
// path : kebab, name : snake
// route - controller - model - view : memerlukan data
// route - view : tanpa data

// http method Route::
// 1. get -> menampilkan halmaman
// 2. post -> mengambil data/menambahkan data
// 3. patch/put -> mengubah data
// 4. delete -> menghapus data

// prefix() : awalan, menulis /admin satu kali untuk 16 route CRUD (beberapa route)
// name('admin.') : biar diawali dengan admin. untuk name nya. pake titik karna nanti akan digabungkan (admin.dashboard / admin.cinemas)
// middleware('isAdmin') : memanggil middleware yg akan digunakan
// middleware : Authorization, pengaturan hak akses pengguna

//logout
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

// Beranda
Route::get('/', [MovieController::class, 'home'])->name('home');

// Semua data film home
Route::get('/home/movies', [MovieController::class, 'homeAllMovies'])->name('home.movies');

// detail - schedule
Route::get('/schedules/{movie_id}', [MovieController::class, 'movieSchedules'])->name('schedule.detail');

// menu "bioskop" pada navbar user (pengguna umum)
Route::get('/cinemas/list', [CinemaController::class, 'cinemaList'])->name('cinemas.list');
Route::get('/cinemas/{ciname_id}/schedules', [CinemaController::class, 'cinemaSchedules'])->name('cinemas.schedules');

// middleware isUser
Route::middleware('isUser')->group(function () {
    // halaman pilihan kursi
    Route::get('/schedules/{scheduleId}/hours/{hourId}/show-seats', [TicketController::class, 'showSeats'])->name('schedules.seats');

    Route::prefix('/tickets')->name('tickets.')->group(function () {
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticketId}/order', [TicketController::class, 'ticketOrder'])->name('order');
        // pembuatan barcode pembayaran
        Route::post('/payment', [TicketController::class, 'ticketPayment'])->name('payment');
        // halaman yang menampilkan qrcode
        Route::get('/{ticketId}/payment', [TicketController::class, 'ticketPaymentPage'])->name('payment.page');
    });
});

Route::middleware('isGuest')->group(function () {
    // Authentication
    Route::get('/login', function () { // login
        return view('login');
    })->name('login');

    Route::post('/login', [UserController::class, 'loginAuth'])->name('login.auth');

    Route::get('/sign-up', function () { // signup
        return view('signup');
    })->name('sign_up');

    Route::post('/sign-up', [UserController::class, 'signUp'])->name('sign_up.add');
});

// middleware isAdmin - Datamaster
Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard'); // admin dashboard

    // data bioskop
    Route::prefix('/cinemas')->name('cinemas.')->group(function () {
        // index (/)
        Route::get('/', [CinemaController::class, 'index'])->name('index');
        // create.blade.php
        Route::get('/create', function () {
            return view('admin.cinema.create');
        })->name('create');

        // create
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        // route edit
        Route::get('/edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        // route update
        Route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        // route delete
        Route::delete('/delete/{id}', [CinemaController::class, 'destroy'])->name('delete');
        // route export
        Route::get('/export', [CinemaController::class, 'export'])->name('export');
        // trash
        Route::get('/trash', [CinemaController::class, 'trash'])->name('trash');
        // restore
        Route::patch('/restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        // delete permanen
        Route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
        // datatables
        Route::get('datatables', [CinemaController::class, 'datatables'])->name('datatables');
    });

    // data pengguna
    Route::prefix('/staffs')->name('staffs.')->group(function () {
        // index (/)
        Route::get('/', [UserController::class, 'index'])->name('index');
        // create.blade.php
        Route::get('/create', function () {
            return view('admin.staff.create');
        })->name('create');

        // store
        Route::post('/store', [UserController::class, 'store'])->name('store');
        // route edit
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        // route update
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        // route delete
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        // route export
        Route::get('/export', [UserController::class, 'export'])->name('export');
        // trash
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        // restore
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        // delete permanen
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
        // datatables
        Route::get('datatables', [UserController::class, 'datatables'])->name('datatables');
    });

    // data movie
    Route::prefix('/movies')->name('movies.')->group(function () {
        // index
        Route::get('/', [MovieController::class, 'index'])->name('index');
        // create
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        // store
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        // edit
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        // update
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        // non-aktif button
        Route::get('/non-activated/{id}', [MovieController::class, 'nonActivated'])->name('non-activated');
        // delete
        Route::delete('/delete/{id}', [MovieController::class, 'destroy'])->name('delete');
        // export
        Route::get('/export', [MovieController::class, 'export'])->name('export');
        // trash
        Route::get('/trash', [MovieController::class, 'trash'])->name('trash');
        // restore
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        // delete permanen
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
        // datatables
        Route::get('datatables', [MovieController::class, 'datatables'])->name('datatables');
    });
});

// middleware isStaff
Route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function () {
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    // data promo
    Route::prefix('/promo')->name('promos.')->group(function () {
        // index
        Route::get('/', [PromoController::class, 'index'])->name('index');
        // create
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        // store
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        // route edit
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        // route update
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        // route delete
        Route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
        // non-activated
        Route::get('/non-activated/{id}', [PromoController::class, 'nonActivated'])->name('non-activated');
        // route export
        Route::get('/export', [PromoController::class, 'export'])->name('export');
        // trash
        Route::get('/trash', [PromoController::class, 'trash'])->name('trash');
        // restore
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        // delete permanen
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
        // datatables
        Route::get('datatables', [PromoController::class, 'datatables'])->name('datatables');
    });

    // data schedule
    Route::prefix('/schedules')->name('schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        // store\
        Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        // edit
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        // update
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        // delete
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');
        // trash
        Route::get('/trash', [ScheduleController::class, 'trash'])->name('trash');
        // restore
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        // delete permanen
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
        // datatables
        Route::get('datatables', [ScheduleController::class, 'datatables'])->name('datatables');
    });
});
