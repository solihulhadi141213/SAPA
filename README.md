# SAPA (Sistem Aspirasi dan Kepuasan Pasien)
SAPA adalah aplikasi berbasis web yang dirancang khusus untuk mengukur, mengelola, dan menganalisis tingkat kepuasan pasien terhadap layanan kesehatan di RSU El-Syifa. Melalui pendekatan yang interaktif dan ramah pengguna, aplikasi ini berfungsi sebagai jembatan komunikasi antara pasien dan manajemen rumah sakit guna meningkatkan mutu pelayanan secara berkelanjutan.

## Fitur Utama
* **Dashboard :** Visualisasi data hasil survei dalam bentuk grafik dan persentase untuk mempermudah manajemen memantau indeks kepuasan.
* **Manajemen User :** Kelola daftar pengguna aplikasi yang terdiri dari Admin, Manajer Mutu, dan Direktur Rumah Sakit. 
* **Pengaturan Umum Aplikasi :** Atur nama Rumah Sakit, Logo, Pavicon dan metatag aplikasi.
* **Pengaturan Koneksi SIMRS:** Hubungkan dengan data kunjungan pasien pada aplikasi SIMRS dengan API.
* **Email Gateway :** Hubungkan dengan modul aplikasi email gateway untuk mempermudah mengirimkan tautan reset password, tautan form responden dan pemberitahuan.
* **Whatsapp Gateway :** Hubungkan dengan modul aplikasi whatsapp gateway, membuat pesan template dan kirim tautan ke kontak pasien secara langsung.
* **Sesi Survey :** Buat sesi survey untuk membedakan kelompok instrumen, responden dan jawaban berdasarkan kelompok tertentu dan periode tertentu.
* **Daftar Pertanyaan :** Kelola daftar pertanyaan, tipe pertanyaan, alternatif jawaban, label dan nilai bobot secara ordinal berdasarkan sesi survey.
* **Responden :** Kelola daftar target responden, ambil dari data kunjungan pasien pada SIMRS dan kirimkan tautan form melalui email atau whatsapp pada kontak yang dimiliki.
* **Jawaban Responden :** Tampilkan data jawaban responden secara keseluruhan.
* **Laporan :** Rekap data jawaban responden, sajikan dalam bentuk laporan berdasarkan sesi atau periode tertentu.

---

## Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan *tech stack* yang berfokus pada kecepatan akses, keamanan data, dan kemudahan kustomisasi UI/UX:

- **Front-End:** HTML5, CSS3, JavaScript, Bootstrap 5 (Responsive UI)
- **Back-End:** PHP 8.1.31
- **Database:** MySQL 9.1.0
- **Interaktivitas:** AJAX (Untuk proses pengisian kuesioner yang *seamless* tanpa *reload* halaman)

---

## Dependensi

Aplikasi ini menggunakan beberapa dependensi pihak ketiga (*third-party dependencies*) untuk menunjang performa dan visualisasi antarmuka:

* **[Bootstrap v5.3.8](https://getbootstrap.com/)** – Framework CSS untuk tata letak (*layouting*) dan komponen UI yang responsif.
* **[Bootstrap Icons v1.13.1](https://icons.getbootstrap.com/)** – Library ikon untuk navigasi dan tombol.
* **[jQuery v4.0.0](https://jquery.com/)** – Digunakan untuk menangani fungsionalitas asinkronus (AJAX) pada form kuesioner tanpa *reload*.
* **[ApexCharts v5.15.1](https://apexcharts.com/)** – Digunakan pada halaman admin untuk menampilkan grafik statistik tingkat kepuasan secara *real-time*.