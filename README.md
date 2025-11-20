# Tixid App

Tixid App adalah sebuah aplikasi berbasis **Laravel** yang dikembangkan untuk kebutuhan sekolah SMK Wikrama Bogor, khususnya dalam kelas **RPL**. Proyek ini dikerjakan selama **4 bulan** mengikuti tutorial yang diberikan oleh sekolah.

Aplikasi ini memiliki beberapa fitur utama yang memudahkan pengolahan data dan visualisasi, antara lain:

- **Maatwebsite Excel**: Untuk import dan export data dalam format Excel.
- **PDF**: Untuk generate sejarah tiket dalam format PDF.
- **ChartJS**: Untuk visualisasi data dalam bentuk grafik.
- **jQuery & AJAX**: Untuk interaksi dinamis pada halaman web tanpa reload.

## Fitur

1. **Import & Export Excel**  
   Memudahkan pengelolaan data melalui file Excel, termasuk menyimpan data ke database dan mengekspor data kembali ke Excel.

2. **Generate PDF**  
   Membuat sejarah tiket dalam format PDF yang rapi dan mudah dibagikan.

3. **Visualisasi Data dengan ChartJS**  
   Menampilkan data dalam bentuk grafik sehingga lebih mudah dianalisis.

4. **Interaksi Dinamis dengan jQuery & AJAX**  
   Mempercepat proses input data dan menampilkan informasi tanpa harus memuat ulang halaman.

## Instalasi

1. Pastikan **PHP**, **Composer**, dan **MySQL** sudah terinstall di komputer.
2. Clone repository ini:
   ```php
   git clone https://github.com/fu-fufajer/tixid-app.git
   ```
3. Masuk ke folder proyek:
    ```php
    cd tixid-app
    ```

4. Install dependensi Laravel:
    ```php
    composer install
    ```

5. Copy file .env.example menjadi .env dan atur konfigurasi database.
    ```php
    cp .env.example .env
    ```

6. Generate key Laravel:
    ```php
    php artisan key:generate
    ```

7. Jalankan migrasi dan seed database:
    ```php
    php artisan migrate
    php artisan db:seed --class=UserSeeder
    ```
> Untuk akun admin, emailnya admin@gmail.com, passwordnya adminID

8. Buat Symlink Storage
    ```php
    php artisan storage:link
    ```

9. Jalankan server:
    ```php
    php artisan serve
    ```

## Kontribusi
Proyek ini dikembangkan untuk pembelajaran di SMK Wikrama Bogor, jadi kontribusi dari pihak luar tidak diterima saat ini. Namun, kamu dapat belajar dari kode yang ada untuk meningkatkan kemampuan Laravel, Excel, PDF, ChartJS, jQuery, dan AJAX.

## Lisensi
Proyek ini dibuat untuk keperluan edukasi dan tidak memiliki lisensi khusus. Dapat digunakan sebagai referensi belajar pribadi.

Tixid App – Proyek RPL SMK Wikrama Bogor

