# SAPA (Sistem Aspirasi dan Kepuasan Pasien)
SAPA adalah aplikasi berbasis web yang dirancang khusus untuk mengukur, mengelola, dan menganalisis tingkat kepuasan pasien terhadap layanan kesehatan di RSU El-Syifa. Melalui pendekatan yang interaktif dan ramah pengguna, aplikasi ini berfungsi sebagai jembatan komunikasi antara pasien dan manajemen rumah sakit guna meningkatkan mutu pelayanan secara berkelanjutan.

## Fitur Utama
* **Dashboard:** Visualisasi data hasil survei dalam bentuk grafik dan persentase untuk mempermudah manajemen memantau indeks kepuasan.
* **Manajemen User:** Kelola daftar pengguna aplikasi yang terdiri dari Admin, Manajer Mutu, dan Direktur Rumah Sakit.
* **Pengaturan Umum Aplikasi:** Atur nama rumah sakit, logo, favicon, dan metatag aplikasi.
* **Pengaturan Koneksi SIMRS:** Hubungkan dengan data kunjungan pasien pada aplikasi SIMRS dengan API.
* **Email Gateway:** Hubungkan dengan modul aplikasi email gateway untuk mempermudah mengirimkan tautan reset password, tautan form responden, dan pemberitahuan.
* **Whatsapp Gateway:** Hubungkan dengan modul aplikasi WhatsApp gateway, membuat pesan template, dan kirim tautan ke kontak pasien secara langsung.
* **Sesi Survey:** Buat sesi survey untuk membedakan kelompok instrumen, responden, dan jawaban berdasarkan kelompok tertentu dan periode tertentu.
* **Daftar Pertanyaan:** Kelola daftar pertanyaan, tipe pertanyaan, alternatif jawaban, label, dan nilai bobot secara ordinal berdasarkan sesi survey.
* **Responden:** Kelola daftar target responden, ambil dari data kunjungan pasien pada SIMRS, dan kirimkan tautan form melalui email atau WhatsApp pada kontak yang dimiliki.
* **Jawaban Responden:** Tampilkan data jawaban responden secara keseluruhan.
* **Laporan:** Rekap data jawaban responden, sajikan dalam bentuk laporan berdasarkan sesi atau periode tertentu.

---

## Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan *tech stack* yang berfokus pada kecepatan akses, keamanan data, dan kemudahan kustomisasi UI/UX:

- **Front-End:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Back-End:** PHP 8.1.31
- **Database:** MySQL 9.1.0
- **Interaktivitas:** AJAX untuk proses pengisian kuesioner yang *seamless* tanpa *reload* halaman

---

## Dependensi

Aplikasi ini menggunakan beberapa dependensi pihak ketiga untuk menunjang performa dan visualisasi antarmuka:

* **Bootstrap v5.3.8** - Framework CSS untuk tata letak dan komponen UI yang responsif.
* **Bootstrap Icons v1.13.1** - Library ikon untuk navigasi dan tombol.
* **jQuery v4.0.0** - Digunakan untuk menangani fungsionalitas asinkronus (AJAX) pada form kuesioner tanpa reload.
* **ApexCharts v5.15.1** - Digunakan pada halaman admin untuk menampilkan grafik statistik tingkat kepuasan secara *real-time*.

---

## Cara Instalasi

Ikuti langkah berikut agar aplikasi dapat dijalankan dengan benar di lokal server.

### 1. Siapkan Lingkungan

Pastikan perangkat sudah memiliki:

* Web server seperti WAMP, XAMPP, atau Laragon
* PHP 8.1
* MySQL atau MariaDB
* phpMyAdmin untuk impor database

### 2. Letakkan Proyek di Folder Web Server

Salin folder proyek `SAPA` ke direktori web server Anda.

Contoh untuk WAMP:

`C:\wamp64\www\SAPA`

### 3. Buat Database

Buka `phpMyAdmin`, lalu buat database baru dengan nama:

`SAPA`

Nama database harus sama dengan konfigurasi pada file `_Config/Connection.php`.

### 4. Impor Database

Setelah database dibuat, impor file SQL berikut:

`DB/sapa.sql`

Langkahnya:

1. Pilih database `SAPA`
2. Klik menu `Import`
3. Pilih file `DB/sapa.sql`
4. Jalankan proses impor

### 5. Atur Koneksi Database

Jika diperlukan, sesuaikan file berikut:

`_Config/Connection.php`

Pastikan parameter berikut sesuai dengan server database Anda:

* `servername`
* `username`
* `password`
* `db`

Contoh konfigurasi yang digunakan pada proyek ini:

```php
$servername = "localhost";
$username   = "root";
$password   = "arunaparasilvanursari";
$db         = "SAPA";
```

### 6. Jalankan Aplikasi

Aktifkan web server dan MySQL, lalu buka aplikasi melalui browser:

`http://localhost/SAPA`

### 7. Uji Aplikasi

Setelah aplikasi terbuka:

* Pastikan halaman utama tampil dengan normal
* Coba akses fitur login jika data akun sudah tersedia di database
* Cek menu dashboard, pengaturan, dan laporan untuk memastikan koneksi database berjalan

---

## Catatan Penting

* Jika aplikasi tidak bisa terhubung ke database, periksa kembali nama database, username, dan password pada `_Config/Connection.php`.
* Jika folder proyek Anda berbeda, sesuaikan alamat URL saat membuka aplikasi.
* Pastikan ekstensi `mysqli` aktif pada PHP.
