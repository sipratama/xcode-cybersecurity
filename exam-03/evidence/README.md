# Exam 03 — Register Evidence

[Overview Exam 3](../README.md) · [HTTP Header Manipulation](../lab-01-http-header-manipulation/README.md)

**Pembaruan dokumentasi:** 17 November 2026 (WIB). Hasil historis hingga 17 November 2026 dipertahankan; tanggal eksekusi HTTP-17 belum diberikan.

## 1. Register sumber

| ID | Sumber | Tanggal hasil | Cakupan | Ketersediaan dan batasan |
|---|---|---|---|---|
| SRC-01 | Instruksi dan ringkasan pengujian pengguna, lampiran `Pasted text.txt` | 17 November 2026 | HTTP-01–HTTP-08, observasi browser, karakteristik respons | Ringkasan ditranskripsikan ke dokumen HTTP; raw request/response dan screenshot tidak dilampirkan |
| SRC-02 | Informasi penyelenggara yang diteruskan pengguna dalam lampiran yang sama | Tidak dinyatakan | Inventaris target dan kebijakan Allow/Deny | **Not Verified:** belum ada bukti konektivitas atau eksploitasi |
| SRC-03 | Lampiran pengguna berjudul `Task: Document XCODE Cybersecurity Exam 3 — HTTP Header Manipulation`, berkas `Pasted text.txt` | Hingga 17 November 2026 | Command dan hasil HTTP-01–HTTP-15 serta ENV-01 | Memperinci SRC-01 dengan cuplikan header/body, hasil ukuran body, dan konteks shell; raw transcript lengkap serta screenshot tidak disertakan |
| SRC-05 | Pesan pengguna HTTP-18 ? Browser-like Request Headers | Tanggal eksekusi tidak diberikan | Command aktual dan cuplikan respons HTTP-18 | Tidak diuji ulang; metadata lingkungan dan timestamp belum tersedia |
| SRC-04 | Pesan lanjutan pengguna tentang Complete Redirect Chain Analysis / HTTP-17 | Tanggal eksekusi tidak diberikan; didokumentasikan 17 November 2026 | Dua redirect HTTPS → HTTP → HTTPS, versi protokol per hop, hasil akhir 403 | Command asli, header lengkap per hop, lingkungan, dan timestamp belum diberikan; command jurnal ditandai rekonstruksi reproduksi |

Lampiran dan pesan percakapan berada di luar repository. Hasil ditranskripsikan pada [Results HTTP](../lab-01-http-header-manipulation/README.md#7-results) dan [learning journal](../lab-01-http-header-manipulation/TEST-LOG.md) agar pembaca dapat menelusuri dasar klaim tanpa path lokal pengguna. Sumber merupakan laporan pengguna dengan cuplikan output, bukan verifikasi ulang maintainer. Raw transcript lengkap dan hash artefak bukti belum tersedia. SRC-03 melengkapi batas pengetahuan SRC-01: command aktual HTTP-01–HTTP-15 kini diberikan, tanpa mengubah hasil historis delapan test awal.

## 2. Pemetaan observasi

| Evidence ID | Observasi yang dilaporkan | Test ID | Sumber |
|---|---|---|---|
| E-HDR-01 | Browser menampilkan 403 Forbidden | Observasi awal, di luar delapan test case | SRC-01 |
| E-HDR-02 | Seluruh delapan test case menghasilkan 403 Forbidden | HTTP-01–HTTP-08 | SRC-01 |
| E-HDR-03 | HTTP/2; `server: cloudflare`; `content-type: text/html; charset=UTF-8`; `cf-cache-status: DYNAMIC`; body `403 Forbidden`; `content-length: 13` pada GET yang diamati | Ringkasan respons; tidak dipetakan pengguna ke setiap test ID | SRC-01 |
| E-HDR-04 | Header `Allow` tidak ditemukan pada respons OPTIONS | HTTP-07 | SRC-01 |
| E-HDR-05 | Command HTTP-01–HTTP-08, HTTP/2 403 pada masing-masing test; HTTP-08 tetap bertipe HTML UTF-8 | HTTP-01–HTTP-08 | SRC-03 |
| E-HDR-06 | GET root dan `/target/` menampilkan HTTP/2 200; body dibuang lokal | HTTP-09, HTTP-10 | SRC-03 |
| E-HDR-07 | Listing `Index of /target/`, `secret-admin/`, `ujian.php`; footer `Apache/2.4.58 (Ubuntu)` | HTTP-11 | SRC-03 |
| E-HDR-08 | HTTPS direktori admin dengan slash: HTTP/2 403, body `403 Forbidden` | HTTP-12 | SRC-03 |
| E-HDR-09 | Loop berhasil pada root shell container: root 200 / 2766 bytes; parent 200 / 1150 bytes; admin dengan slash 403 / 13 bytes; redirect URL kosong pada ketiganya | HTTP-13 | SRC-03 |
| E-HDR-10 | HTTPS tanpa slash: HTTP/2 301, Content-Type HTML ISO-8859-1, Location ke HTTP dengan slash; link HTML ke HTTPS dan footer Apache | HTTP-14 | SRC-03 |
| E-HDR-11 | HTTPS dengan slash: HTTP/2 403, HTML UTF-8, Content-Length 13, Server cloudflare; body `403 Forbidden` | HTTP-15 | SRC-03 |
| E-HDR-12 | Kali gagal menemukan curl karena PATH tanpa `/usr/bin:/bin`; setelah koreksi sementara, lokasi `/usr/bin/curl` dan versi `8.20.0` dilaporkan | ENV-01, percobaan awal HTTP-13 | SRC-03 |
| E-HDR-14 | Parent PHP dengan User-Agent Mozilla/5.0 dan Accept HTML: HTTP/2 403, HTML UTF-8, Content-Length 13, cloudflare, DYNAMIC; cfEdge dur=24 dan cfOrigin dur=43; body 403 Forbidden | HTTP-18 | SRC-05 |
| E-HDR-13 | Dua redirect: HTTPS HTTP/2 301 → HTTP HTTP/1.1 301 → HTTPS HTTP/2 403; akses tetap ditolak | HTTP-17 | SRC-04 |

ID tersebut mengidentifikasi catatan dalam dokumen ini, bukan file bukti yang sudah ada. Jangan menyatakan karakteristik E-HDR-03 identik pada semua request karena raw respons per test belum diberikan.

HTTP-13 memakai sintaks newline yang dikoreksi untuk reproduksi; hasil sukses berasal dari root shell container, bukan percobaan Kali yang gagal. Contoh export pada ENV-01 bukan command asli yang diberikan. Command HTTP-17 juga merupakan rekonstruksi, bukan bukti command history. Tidak ada HTTP-16 yang diberikan; celah penomoran tidak diisi dengan hasil rekaan.

## 3. Pemetaan findings ke evidence

| Finding | Evidence | Kesimpulan yang didukung |
|---|---|---|
| F-01 | E-HDR-06, E-HDR-07, E-HDR-09 | Root dan parent dapat diakses pada konteks yang dilaporkan |
| F-02 | E-HDR-07 | Struktur direktori terlihat; konten resource belum terbukti dapat dibaca |
| F-03 | E-HDR-02, E-HDR-05, E-HDR-08, E-HDR-11, E-HDR-13 | Resource yang diuji sebagai terlindungi tetap menolak akses |
| F-04 | E-HDR-05 | Nilai header client yang diuji tidak memberi authorization bypass |
| F-05 | E-HDR-10, E-HDR-11, E-HDR-13 | Tanpa slash mengembalikan 301; dengan slash HTTPS berakhir 403 |
| F-06 | E-HDR-10, E-HDR-13 | Inkonsistensi scheme teramati; penyebab dan dampak aktual belum terbukti |
| F-07 | E-HDR-09, E-HDR-12 | Hasil loop sukses dan error PATH berasal dari dua lingkungan terpisah |

## 4. Bukti yang masih diperlukan

- [ ] Raw request/response tersensor untuk HTTP-01–HTTP-15 dan HTTP-17, termasuk seluruh hop redirect.
- [ ] Tanggal/jam dan zona waktu, versi curl, serta lingkungan asal setiap test; khususnya command asli dan tanggal eksekusi HTTP-17.
- [ ] Konteks jaringan sumber dan state autentikasi tanpa membocorkan secret.
- [ ] Screenshot browser untuk observasi awal jika tersedia.
- [ ] Log edge/origin tersensor dan ID korelasi jika penyelenggara menyediakannya.
- [ ] Konfigurasi canonical URL, forwarded scheme, TLS termination, dan kebijakan client/cookie/HSTS untuk analisis dampak hop HTTP.

Tidak ada screenshot Exam 3 yang tersedia saat pembaruan. Tidak dibuat file gambar atau tautan placeholder yang menyerupai bukti aktual.

## 5. Tata kelola bukti baru

Simpan bukti mentah secara privat di luar repository. Sebelum menambahkan salinan publik, sensor Authorization, Cookie, Set-Cookie, JWT, kredensial, flag, identitas, dan alamat akses di luar scope publik yang diminta. Periksa metadata gambar dan teks terminal juga.

Gunakan nama berkas berbasis test ID dan tanggal yang diketahui, lalu tambahkan tautan hanya setelah berkas benar-benar tersedia. Untuk setiap bukti, catat asal, waktu/zona waktu yang diketahui, test ID, tindakan, hasil, redaksi, dan SHA-256 file publik. Jangan mengarang metadata yang belum dicatat. Penambahan hasil baru tidak boleh menimpa hasil historis 17 November 2026. Status challenge tetap **IN PROGRESS — NOT SOLVED**; redirect bukan bukti bypass atau eksploitasi.
