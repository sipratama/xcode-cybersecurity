# Pentest Learning Notes

## Lab 01 — Path Traversal / Local File Inclusion (LFI)

> **Catatan:** Pengujian ini dilakukan pada environment lab/VPS yang memang disediakan untuk latihan penetration testing. Teknik yang dicatat di sini digunakan hanya pada sistem yang memiliki izin untuk diuji.

---

## 1. Tujuan Pembelajaran

Pada latihan ini, tujuan utama adalah memahami bagaimana sebuah parameter pada aplikasi web dapat dimanipulasi untuk mengakses file di luar direktori yang seharusnya.

Konsep utama yang dipelajari:

* Query parameter pada URL
* Struktur filesystem Linux
* Relative path `.` dan `..`
* Directory/Path Traversaversal
* Local File Inclusion (LFI)
* Cara aplikasi web dapat membaca file lokal secara tidak aman

---

## 2. Environment Lab

Soal pertama hanya dapat diakses melalui **VPS lab** dan tidak dapat diakses secara langsung dari jaringan luar.

Target aplikasi:

```text
http://192.168.39.87/ujianmonitor
```

Karena akses dilakukan dari terminal VPS, halaman dapat dibuka menggunakan text-based browser seperti `lynx` atau `w3m`.

Contoh menggunakan `lynx`:

```bash
lynx http://192.168.39.87/ujianmonitor
```

Alternatif menggunakan `w3m`:

```bash
w3m http://192.168.39.87/ujianmonitor
```

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 01]**

> Screenshot akses halaman `/ujianmonitor` melalui `lynx` atau `w3m` dari VPS.

---

## 3. Identifikasi Parameter

Dalam latihan ditemukan pola URL yang menggunakan parameter `page`.

Format sederhananya:

```text
http://<target-ip-atau-domain>/?page=<value>
```

Contoh penggunaan normal sebuah parameter `page` dapat berupa:

```text
?page=about.html
```

atau:

```text
?page=contact.html
```

Pada pola tersebut:

```text
?page=
```

merupakan **query parameter** bernama `page`.

Parameter seperti ini dapat digunakan aplikasi untuk menentukan halaman atau file yang perlu dibaca.

Secara konseptual, implementasi yang tidak aman dapat melakukan sesuatu seperti:

```text
base_directory + nilai_parameter_page
```

Misalnya:

```text
/var/www/html/pages/<nilai-page>
```

---

## 4. Pengujian Path Traversal

Payload yang dipelajari pada lab:

```text
http://<target-ip-atau-domain>/?page=../../../../home/data/linkwa.txt
```

Bagian yang penting adalah:

```text
?page=../../../../home/data/linkwa.txt
```

Payload tersebut dapat dibagi menjadi dua bagian:

```text
../../../../
```

sebagai **traversal path**, dan:

```text
home/data/linkwa.txt
```

sebagai **path file tujuan**.

Secara sederhana:

```text
?page=../../../../home/data/linkwa.txt
       └─ traversal ─┘ └── file tujuan ──┘
```

---

## 5. Memahami `../`

Pada filesystem Linux:

```text
.      = direktori saat ini
..     = direktori satu tingkat di atas
../..  = naik dua tingkat direktori
```

Dengan demikian:

```text
../../../../
```

berarti mencoba berpindah naik sebanyak **empat tingkat direktori**.

Misalnya aplikasi membaca file dari:

```text
/var/www/html/pages/
```

kemudian menerima nilai:

```text
../../../../home/data/linkwa.txt
```

maka secara konseptual perjalanan direktorinya dapat divisualisasikan sebagai berikut:

```text
/var/www/html/pages/
        │
        │ ..
        ▼
/var/www/html/
        │
        │ ..
        ▼
/var/www/
        │
        │ ..
        ▼
/var/
        │
        │ ..
        ▼
/
└── home/
    └── data/
        └── linkwa.txt
```

Setelah path dinormalisasi, tujuan akhirnya adalah:

```text
/home/data/linkwa.txt
```

Dengan kata lain, payload mencoba keluar dari direktori aplikasi dan kemudian mengakses file lain yang terdapat pada filesystem server.

---

## 6. Kenapa Ini Bisa Terjadi?

Masalah dapat terjadi apabila aplikasi menerima input pengguna melalui parameter seperti:

```text
?page=
```

kemudian menggunakan nilai tersebut sebagai lokasi file tanpa melakukan validasi atau pembatasan path yang memadai.

Sebagai ilustrasi, aplikasi mengharapkan:

```text
?page=about.html
```

sehingga file yang dibaca adalah:

```text
/var/www/html/pages/about.html
```

Namun pengguna memberikan:

```text
?page=../../../../home/data/linkwa.txt
```

Jika aplikasi tidak mencegah traversal, path tersebut dapat keluar dari:

```text
/var/www/html/pages/
```

dan mencoba menuju file:

```text
/home/data/linkwa.txt
```

Inilah alasan karakter:

```text
../
```

menjadi bagian penting dalam memahami serangan **Path Traversal**.

---

## 7. Path Traversal vs LFI

Dua istilah yang sering muncul pada kasus seperti ini adalah **Path Traversal** dan **Local File Inclusion (LFI)**.

### Path Traversal

Path Traversal adalah teknik memanipulasi path agar aplikasi mengakses file atau direktori di luar lokasi yang seharusnya.

Contoh pola:

```text
../../../../some/file.txt
```

Tujuannya adalah keluar dari direktori yang diperbolehkan.

### Local File Inclusion (LFI)

LFI biasanya merujuk pada kondisi ketika aplikasi memasukkan atau memuat file lokal berdasarkan input yang dapat dikendalikan pengguna.

Pada aplikasi PHP, misalnya, masalah dapat muncul ketika parameter pengguna diteruskan secara tidak aman ke mekanisme pemuatan file.

Karena itu, **Path Traversal dan LFI berhubungan erat tetapi tidak selalu berarti hal yang sama**.

Pada latihan ini, bagian terpenting yang perlu dipahami terlebih dahulu adalah mekanisme **Path Traversal menggunakan `../`**.

---

## 8. Hasil Pengujian

Payload:

```text
http://<target-ip-atau-domain>/?page=../../../../home/data/linkwa.txt
```

secara konseptual meminta aplikasi:

> Menggunakan parameter `page`, keluar dari direktori aplikasi melalui beberapa `../`, kemudian mencoba membaca file `/home/data/linkwa.txt`.

Apabila aplikasi menampilkan isi file tersebut, hal itu menunjukkan bahwa input `page` dapat memengaruhi lokasi file yang dibaca dan pembatasan path pada aplikasi tidak memadai.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 02]**

> Screenshot URL/payload yang digunakan pada environment lab.

**[PLACEHOLDER SCREENSHOT 03]**

> Screenshot response aplikasi ketika file berhasil dibaca.

**[PLACEHOLDER SCREENSHOT 04 — OPSIONAL]**

> Screenshot isi file atau informasi penting yang menjadi bukti keberhasilan pengujian. Sensor credential/token/data sensitif apabila catatan akan dipublikasikan.

---

## 9. Temuan

**Finding:** Path Traversal / Potential Local File Inclusion

**Parameter terdampak:**

```text
page
```

**Payload lab:**

```text
../../../../home/data/linkwa.txt
```

**File tujuan:**

```text
/home/data/linkwa.txt
```

**Indikator keberhasilan:**

Aplikasi dapat membaca atau menampilkan file yang berada di luar direktori halaman yang seharusnya dapat diakses melalui parameter `page`.

---

## 10. Root Cause

Akar masalah secara umum adalah aplikasi mempercayai input pengguna untuk menentukan path file tanpa melakukan pembatasan yang cukup.

Secara konseptual:

```text
User Input
    │
    ▼
?page=...
    │
    ▼
Digunakan sebagai path file
    │
    ▼
Tidak ada validasi/canonical path check
    │
    ▼
File di luar direktori yang diizinkan dapat diakses
```

---

## 11. Mitigasi

Beberapa pendekatan defensif yang dapat digunakan developer adalah:

1. Jangan menggunakan input pengguna secara langsung sebagai filesystem path.
2. Gunakan **allowlist** untuk halaman/file yang memang boleh diakses.
3. Lakukan canonicalization/normalization terhadap path sebelum file diakses.
4. Pastikan hasil path tetap berada di dalam direktori yang telah ditentukan.
5. Jalankan aplikasi menggunakan user dengan permission filesystem seminimal mungkin.

Contoh konsep allowlist:

```text
about   → about.html
contact → contact.html
help    → help.html
```

Dengan pendekatan tersebut, pengguna hanya memilih identifier yang telah ditentukan aplikasi dan tidak memberikan filesystem path secara langsung.

---

## 12. Kesimpulan Pembelajaran

Dari latihan ini saya memahami bahwa parameter URL tidak selalu hanya digunakan untuk data sederhana. Dalam beberapa aplikasi, parameter dapat digunakan untuk menentukan file yang akan dibaca oleh server.

Payload:

```text
?page=../../../../home/data/linkwa.txt
```

memanfaatkan:

```text
../
```

untuk berpindah ke parent directory secara berulang hingga keluar dari direktori aplikasi, kemudian mencoba menuju:

```text
/home/data/linkwa.txt
```

Konsep utama yang saya dapatkan dari latihan ini adalah:

```text
Input pengguna
      ↓
Parameter page
      ↓
Manipulasi path menggunakan ../
      ↓
Keluar dari direktori aplikasi
      ↓
Mengakses file lokal lainnya
```

Hal terpenting bukan sekadar menghafal payload `../../../../`, tetapi memahami **mengapa traversal tersebut bekerja**. Jumlah `../` bergantung pada posisi direktori awal aplikasi terhadap file tujuan.

---

## 13. Hal yang Perlu Dipelajari Selanjutnya

Setelah memahami latihan ini, materi berikutnya yang perlu saya pelajari adalah:

* Absolute path vs relative path
* Path normalization pada Linux
* Mengapa jumlah `../` dapat berbeda pada setiap aplikasi
* Perbedaan Path Traversal dan LFI secara lebih mendalam
* Validasi dan canonicalization path
* File permission pada Linux
* Cara developer mencegah Path Traversal
* Cara mendokumentasikan finding dalam penetration testing report

---

### Ringkasan

```text
Vulnerability : Path Traversal / Potential LFI
Parameter     : page
Technique     : Directory Traversal
Pattern       : ../
Target File   : /home/data/linkwa.txt
Environment   : Authorized Pentest Lab
Main Lesson   : User-controlled file paths harus divalidasi dan dibatasi.
```
