# Aplikasi Absensi Wajah (Laravel + Face API)

Aplikasi absensi wajah berbasis web modern menggunakan Laravel 13, Breeze (Tailwind CSS), Vite, dan `@vladmandic/face-api`. Deteksi dan pengenalan wajah dilakukan sepenuhnya di sisi klien (browser) tanpa menggunakan Python, dlib, atau Visual C++.

## Prasyarat
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL

## Instalasi

1. **Install dependensi PHP**
   ```bash
   composer install
   ```

2. **Install dependensi NPM & Face API**
   ```bash
   npm install
   npm install @vladmandic/face-api
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Buka file `.env` dan atur koneksi database Anda (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).*

4. **Migrasi Database**
   ```bash
   php artisan migrate
   ```

5. **Storage Link (Untuk foto karyawan)**
   ```bash
   php artisan storage:link
   ```

6. **Unduh Model Face API**
   Aplikasi membutuhkan file model AI untuk berjalan. Buat folder `public/models` dan unduh file-file berikut dari repositori [justadudewhohacks/face-api.js/weights](https://github.com/justadudewhohacks/face-api.js/tree/master/weights):
   
   - `tiny_face_detector_model-weights_manifest.json`
   - `tiny_face_detector_model-shard1`
   - `face_landmark_68_model-weights_manifest.json`
   - `face_landmark_68_model-shard1`
   - `face_recognition_model-weights_manifest.json`
   - `face_recognition_model-shard1`
   - `face_recognition_model-shard2`

   *Pastikan semua file tersebut berada di dalam direktori `public/models/`.*

7. **Jalankan Aplikasi**
   Buka 2 terminal terpisah:
   ```bash
   # Terminal 1 (Vite/Frontend dev server)
   npm run dev

   # Terminal 2 (Laravel backend server)
   php artisan serve
   ```

## Cara Penggunaan
1. Register akun baru atau login menggunakan akun Breeze Anda.
2. Masuk ke menu **Karyawan** dan klik "Tambah Karyawan". Isi data karyawan.
3. Masuk ke menu **Daftar Wajah**, izinkan akses kamera, pilih karyawan yang baru dibuat, dan klik "Ambil Foto & Daftarkan Wajah".
4. Masuk ke menu **Absensi Wajah** untuk melakukan absensi (Scan Wajah atau Auto Scan).
5. Lihat hasil absensi di menu **Riwayat**.
