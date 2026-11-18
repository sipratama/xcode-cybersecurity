# HTTP Header Manipulation — Learning Journal

[Laporan challenge](README.md) · [Glosarium dan command reference](GLOSSARY.md) · [Register evidence](../evidence/README.md)

**Hasil historis:** hingga 17 November 2026; tanggal eksekusi HTTP-17 belum diberikan  
**Pembaruan dokumentasi:** 17 November 2026 (WIB) · **Current Status:** IN PROGRESS — NOT SOLVED

## Cara membaca jurnal

Jurnal merekam HTTP-01–HTTP-15 dan ENV-01 dari [SRC-03](../evidence/README.md#1-register-sumber), melengkapi catatan awal SRC-01. HTTP-17 berasal dari SRC-04. Perintah dan cuplikan hasil berasal dari laporan pengguna; maintainer tidak menjalankannya. Sintaks HTTP-13 diperbaiki secara transparan. Command asli HTTP-17 tidak disertakan, sehingga command-nya ditandai sebagai rekonstruksi reproduksi. Rekaman lengkap per request, timestamp, dan screenshot belum tersedia; bagian yang tidak diberikan tidak direkonstruksi sebagai output aktual. HTTP-16 belum diberikan dan tidak dibuat-buat.

- **OBSERVED:** dinyatakan dalam output/hasil aktual yang diberikan pengguna; tidak berarti diverifikasi ulang secara independen.
- **IMPORTANT:** konsep yang perlu dipahami, bukan observasi tambahan pada target.
- **NOT VERIFIED:** hipotesis yang belum memiliki bukti cukup.
- **NEXT STEP:** rencana investigasi; belum dijalankan.

Command menggunakan Bash/Linux. `\` di akhir baris menyambung perintah ke baris berikutnya dan tidak boleh diikuti spasi. Path `/target/ujian.php/secret-admin` berbeda dari `/target/secret-admin/`; kesamaan nama tidak membuktikan keduanya menggunakan handler yang sama. Penjelasan opsi lengkap tersedia di [command reference](GLOSSARY.md#6-command-reference).

## HTTP-01 — Baseline GET

**Objective:** mengetahui respons endpoint utama sebelum mengubah header.

**Command:**

```bash
curl -i https://hackerlab.biz.id/target/ujian.php/secret-admin
```

**Command Breakdown:** `curl` mengirim request ke URL HTTPS tersebut. Tanpa pilihan method lain, request ini memakai GET. `-i` menampilkan response header bersama body; `/target/ujian.php/secret-admin` adalah path yang diuji.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden. Header/body lengkap spesifik HTTP-01 tidak disertakan; karakteristik GET umum dari laporan awal ada di [Results](README.md#7-results).

**Technical Explanation:** ini menjadi baseline penolakan untuk membandingkan test header selanjutnya. Status 403 tidak mengungkap komponen pembuat keputusan.

**Important Findings:** **IMPORTANT:** baseline perlu konteks sesi/jaringan agar perbandingan valid. **NOT VERIFIED:** penolakan berasal dari Cloudflare atau aplikasi. **NEXT STEP:** lengkapi raw respons dan konteks baseline.

**Conclusion:** endpoint utama menolak GET dalam kondisi yang dilaporkan; bypass belum terbukti.

## HTTP-02 — HEAD Request

**Objective:** membandingkan respons untuk method HEAD dengan GET baseline.

**Command:**

```bash
curl -I https://hackerlab.biz.id/target/ujian.php/secret-admin
```

**Command Breakdown:** `curl` mengakses URL endpoint utama. `-I` memilih HEAD dan meminta header tanpa response body; huruf `I` besar berbeda dari `-i` kecil.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden. Header lengkap tidak diberikan. Ketiadaan body pada HEAD adalah semantik method, bukan bukti akses atau error baru.

**Technical Explanation:** HEAD yang ditolak belum membuktikan GET dan HEAD diproses melalui jalur internal identik.

**Important Findings:** **IMPORTANT:** `curl -I` bukan GET dengan body yang hanya disembunyikan di terminal. **NEXT STEP:** bandingkan header lengkap kedua respons bila tersedia.

**Conclusion:** mengganti GET menjadi HEAD tidak memberikan akses pada pengujian ini.

## HTTP-03 — X-Forwarded-For

**Objective:** menguji apakah nilai alamat client yang dikirim dalam header mengubah hasil kontrol akses.

**Command:**

```bash
curl -i \
  -H "X-Forwarded-For: 127.0.0.1" \
  https://hackerlab.biz.id/target/ujian.php/secret-admin
```

**Command Breakdown:** `curl` memakai GET ke URL utama; `-i` menampilkan header dan body. `-H` menambahkan satu request header. Tanda kutip menjaga nama dan nilai header sebagai satu argumen; `127.0.0.1` adalah alamat loopback yang ditulis sebagai nilai header, bukan tujuan koneksi. `\` menyambung baris Bash.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden. Raw header/body per test belum diberikan.

**Technical Explanation:** menulis alamat loopback pada X-Forwarded-For tidak membuat koneksi jaringan berasal dari loopback. Origin mungkin tidak menerima atau tidak mempercayai nilai tersebut; keduanya **NOT VERIFIED**.

**Important Findings:** **OBSERVED:** nilai ini tidak mengubah hasil menjadi akses yang diizinkan. **IMPORTANT:** header client dapat dipalsukan. **NEXT STEP:** tinjau trust boundary melalui konfigurasi/log yang disediakan penyelenggara.

**Conclusion:** belum ada bypass yang terbukti melalui X-Forwarded-For yang diuji.

## HTTP-04 — X-Real-IP

**Objective:** membandingkan konvensi header alamat client lain terhadap baseline.

**Command:**

```bash
curl -i \
  -H "X-Real-IP: 127.0.0.1" \
  https://hackerlab.biz.id/target/ujian.php/secret-admin
```

**Command Breakdown:** `curl` mengirim GET ke endpoint utama; `-i` menampilkan header dan body. `-H` memasang X-Real-IP dengan nilai loopback. Kutip menjaga header sebagai satu argumen dan `\` melanjutkan baris.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden. Header/body lengkap tidak tersedia untuk test ini.

**Technical Explanation:** X-Real-IP memiliki makna sesuai konfigurasi proxy; client yang menambahkannya tidak otomatis memperoleh identitas internal.

**Important Findings:** **OBSERVED:** hasil tetap 403. **NOT VERIFIED:** header dibuang, ditimpa, atau diabaikan. **NEXT STEP:** korelasikan header masuk proxy dan nilai yang diterima origin bila log tersedia.

**Conclusion:** nilai X-Real-IP yang diuji belum mengubah authorization outcome.

## HTTP-05 — Referer

**Objective:** memeriksa apakah URI perujuk yang terlihat berasal dari domain yang sama memengaruhi akses.

**Command:**

```bash
curl -i \
  -H "Referer: https://hackerlab.biz.id/" \
  https://hackerlab.biz.id/target/ujian.php/secret-admin
```

**Command Breakdown:** `curl` mengirim GET ke URL utama. `-i` menyertakan response header; `-H` mengatur request header Referer ke URL root HTTPS. URL di dalam header adalah metadata, sedangkan argumen URL terakhir adalah tujuan request. `\` menyambung baris.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden. Header/body lengkap spesifik test tidak disediakan.

**Technical Explanation:** Referer yang ditentukan curl tidak membuktikan pengguna benar-benar bernavigasi dari halaman root atau telah login.

**Important Findings:** **IMPORTANT:** Referer bukan pengganti autentikasi/otorisasi. **OBSERVED:** nilai yang diuji tidak memberikan akses. **NEXT STEP:** konfirmasi mekanisme identitas yang dimaksud challenge.

**Conclusion:** penggantian Referer tidak membuktikan bypass.

## HTTP-06 — Parent PHP Endpoint

**Objective:** membandingkan endpoint PHP tanpa suffix `/secret-admin` dengan baseline.

**Command:**

```bash
curl -i https://hackerlab.biz.id/target/ujian.php
```

**Command Breakdown:** `curl` memakai GET; `-i` menampilkan header dan body. URL tetap pada host yang sama, tetapi path berhenti pada `/target/ujian.php`.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden. Tidak ada raw body/header khusus HTTP-06 yang diberikan.

**Technical Explanation:** endpoint PHP pembanding juga menolak request. Hasil ini tidak membuktikan seluruh direktori `/target/` ditolak; HTTP-10/11 kemudian menunjukkan 200 pada direktori tersebut.

**Important Findings:** **IMPORTANT:** path aplikasi dan direktori parent dapat memiliki kontrol berbeda. **NOT VERIFIED:** aturan penolakan pada keduanya sama. **NEXT STEP:** bandingkan routing melalui konfigurasi/log, bukan status saja.

**Conclusion:** menghilangkan suffix pada endpoint PHP tidak memberikan akses.

## HTTP-07 — OPTIONS Method

**Objective:** melihat respons terhadap permintaan informasi opsi komunikasi pada endpoint utama.

**Command:**

```bash
curl -i -X OPTIONS \
  https://hackerlab.biz.id/target/ujian.php/secret-admin
```

**Command Breakdown:** `curl` mengakses URL utama; `-i` menampilkan header dan body. `-X OPTIONS` memilih method OPTIONS secara eksplisit. `\` melanjutkan argumen URL pada baris berikutnya.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden; header `Allow` tidak ditemukan pada respons yang dilaporkan. Body lengkap per test tidak disertakan.

**Technical Explanation:** OPTIONS ditolak sehingga hasil ini belum mengungkap daftar method yang didukung. Jangan menerapkan persyaratan header `Allow` untuk status 405 pada respons 403 ini.

**Important Findings:** **IMPORTANT:** tidak adanya `Allow` di sini bukan bukti semua method tidak didukung. **NEXT STEP:** periksa kebijakan method melalui dokumentasi/configuration review yang sah.

**Conclusion:** OPTIONS tidak menyediakan informasi method atau akses tambahan dalam hasil yang tersedia.

## HTTP-08 — Accept Header

**Objective:** memeriksa apakah preferensi JSON mengubah representasi atau hasil akses.

**Command:**

```bash
curl -i \
  -H "Accept: application/json" \
  https://hackerlab.biz.id/target/ujian.php/secret-admin
```

**Command Breakdown:** `curl` memakai GET; `-i` menampilkan response header/body. `-H` menambahkan Accept dengan media type JSON. URL terakhir menentukan endpoint; `\` menyambung baris.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden; Content-Type tetap `text/html; charset=UTF-8`. Body lengkap spesifik test tidak disertakan.

**Technical Explanation:** Accept adalah preferensi representasi dari client; Content-Type menjelaskan representasi yang benar-benar dikirim. Preferensi JSON tidak memberi hak akses.

**Important Findings:** **OBSERVED:** penolakan masih berformat HTML menurut Content-Type. **IMPORTANT:** negosiasi konten berbeda dari authorization. **NEXT STEP:** simpan pasangan request/response untuk memastikan perbandingan.

**Conclusion:** Accept JSON tidak mengubah penolakan dan tidak menghasilkan response bertipe JSON.

## HTTP-09 — Root Endpoint

**Objective:** membandingkan keteraksesan root domain dengan endpoint yang ditolak.

**Command:**

```bash
curl -sS -D - -o /dev/null \
  https://hackerlab.biz.id/
```

**Command Breakdown:** `curl` melakukan GET ke `/`. `-sS` menggabungkan mode senyap (`-s`) dengan penampilan pesan error (`-S`). `-D -` menulis response header ke stdout, ditandai `-` setelah `-D`. `-o /dev/null` membuang body lokal; server tetap mengirim body GET. `\` menyambung baris.

**Actual Result:** **OBSERVED:** HTTP/2 200 OK. Isi body tidak ditampilkan oleh command dan tidak diberikan pada test ini.

**Technical Explanation:** root domain dapat diakses dalam konteks pengujian yang dilaporkan. Ini menolak generalisasi bahwa seluruh host selalu mengembalikan 403.

**Important Findings:** **IMPORTANT:** membuang body dengan `-o` tidak mengubah GET menjadi HEAD. **NEXT STEP:** gunakan path pembanding yang diketahui untuk menilai batas penolakan.

**Conclusion:** root mengembalikan 200; keteraksesan endpoint lain perlu dinilai per path.

## HTTP-10 — Target Directory

**Objective:** memeriksa direktori `/target/` tanpa menampilkan body.

**Command:**

```bash
curl -sS -D - -o /dev/null \
  https://hackerlab.biz.id/target/
```

**Command Breakdown:** `curl` melakukan GET ke `/target/`. `-sS` meniadakan progress namun tetap menampilkan error. `-D -` menampilkan header ke stdout; `-o /dev/null` membuang body yang diterima. `\` melanjutkan baris ke URL.

**Actual Result:** **OBSERVED:** HTTP/2 200 OK. Command ini tidak menampilkan konten direktori; konten diamati terpisah pada HTTP-11.

**Technical Explanation:** direktori parent dapat diakses walaupun endpoint PHP dan direktori admin menolak request.

**Important Findings:** **IMPORTANT:** status 200 saja belum menjelaskan isi halaman. **NEXT STEP:** lihat body seperti yang dicatat pada HTTP-11.

**Conclusion:** `/target/` memiliki hasil akses berbeda dari endpoint utama.

## HTTP-11 — Directory Listing

**Objective:** melihat isi respons `/target/` untuk memahami resource yang diekspos.

**Command:**

```bash
curl -i https://hackerlab.biz.id/target/
```

**Command Breakdown:** `curl` memakai GET ke `/target/`; `-i` menampilkan header sekaligus body HTML sehingga daftar direktori dapat diamati.

**Actual Result:** **OBSERVED:** HTTP/2 200 OK. HTML yang dilaporkan memuat `Index of /target/`, direktori `secret-admin/`, file `ujian.php`, dan footer `Apache/2.4.58 (Ubuntu)`. Ini adalah daftar elemen yang dilaporkan, bukan salinan HTML lengkap.

**Technical Explanation:** directory listing terlihat pada `/target/` dan mengungkap nama resource. Banner/footer adalah informasi yang ditampilkan, bukan verifikasi independen versi software atau paket yang benar-benar berjalan.

**Important Findings:** **OBSERVED:** listing mengekspos nama direktori/file tersebut. **IMPORTANT:** melihat nama resource tidak memberi izin membaca isinya; ini akses direktori biasa, bukan directory traversal. **NEXT STEP:** evaluasi kebutuhan listing bersama pengelola lab.

**Conclusion:** ada eksposur struktur direktori yang teramati, tanpa bukti akses admin atau pembacaan source PHP.

## HTTP-12 — Direct Secret Admin Directory Access

**Objective:** membandingkan akses langsung direktori yang terlihat pada listing dengan endpoint PHP challenge.

**Command:**

```bash
curl -i https://hackerlab.biz.id/target/secret-admin/
```

**Command Breakdown:** `curl` mengirim GET; `-i` menampilkan header/body. URL memakai direktori `/target/secret-admin/`, termasuk slash akhir, tanpa segmen `ujian.php`.

**Actual Result:** **OBSERVED:** HTTP/2 403 Forbidden; body `403 Forbidden`. Header lengkap khusus HTTP-12 tidak diberikan.

**Technical Explanation:** resource yang terlihat pada listing tetap menolak akses langsung. Ini membedakan pengungkapan nama dari kemampuan membaca konten.

**Important Findings:** **IMPORTANT:** path ini berbeda dari `/target/ujian.php/secret-admin`. **NOT VERIFIED:** kedua path menggunakan kontrol yang sama. **NEXT STEP:** tinjau pemetaan handler bila konfigurasi tersedia.

**Conclusion:** akses langsung ke direktori admin tidak berhasil.

## HTTP-13 — Status and Body Size Comparison

**Objective:** membandingkan status, ukuran body yang diunduh, dan target redirect pada tiga path secara konsisten dalam satu loop.

**Command:** sintaks reproduksi dikoreksi dari command logis yang dilaporkan. Paste awal menampilkan escape newline yang rusak secara visual. Format berikut memakai `\n` utuh di dalam string `-w`, bukan baris baru yang terpotong. Nama variabel loop dibuat deskriptif; tujuan URL tetap sama.

```bash
for request_path in \
  "/" \
  "/target/" \
  "/target/secret-admin/"; do
  echo "===== $request_path ====="
  curl -sS -o /dev/null \
    -w "HTTP: %{http_code} | Redirect: %{redirect_url} | Size: %{size_download}\n" \
    "https://hackerlab.biz.id$request_path"
done
```

**Command Breakdown:** `for ... in ...; do ... done` mengulang body loop untuk setiap path. `request_path` berisi satu path saat tiap iterasi; `$request_path` memasukkan nilainya ke label `echo` dan URL. Kutip ganda menjaga URL sebagai satu argumen sambil mengizinkan ekspansi variabel. `echo` memberi label lokal, bukan output server. `curl` mengirim GET; `-sS` menyembunyikan progress namun menampilkan error; `-o /dev/null` membuang body setelah diterima; `-w` mencetak metrik setelah transfer. `%{http_code}` adalah status, `%{redirect_url}` target redirect bila ada, dan `%{size_download}` jumlah byte body yang diunduh. `\n` dalam format curl mengakhiri baris; `\` di akhir baris Bash menyambung command. Tidak ada `-L`, sehingga curl tidak mengikuti redirect otomatis.

**Actual Result:** **OBSERVED:** percobaan awal di Kali gagal menemukan curl karena masalah PATH; kegagalan lokal tersebut bukan respons HTTP dan bukan hasil target. Eksekusi berikutnya berhasil di root shell container yang terpisah. Angka berikut berasal dari eksekusi yang berhasil, bukan hasil retest Kali setelah perbaikan PATH.

| Path | HTTP status | Body yang diunduh | Redirect URL |
|---|---|---|---|
| `/` | 200 | 2766 bytes | Kosong |
| `/target/` | 200 | 1150 bytes | Kosong |
| `/target/secret-admin/` | 403 | 13 bytes | Kosong |

Versi HTTP tidak dinyatakan oleh output metrik tersebut. Ukuran body bukan ukuran seluruh response/network traffic dan tidak membuktikan konten identik. Tidak dibuat transcript terminal sintetis dari tabel ini.

**Technical Explanation:** hasil memperjelas perbedaan antar-path pada lingkungan eksekusi yang berhasil. Redirect URL kosong pada tiga request ini tidak membuktikan semua URL di host tidak melakukan redirect; HTTP-14 menguji path lain tanpa slash.

**Important Findings:** **OBSERVED:** dua path menghasilkan 200 dan direktori admin menghasilkan 403. **IMPORTANT:** hasil lintas shell perlu konteks lingkungan; lihat [ENV-01](#env-01--kali-linux-path-issue). **NEXT STEP:** lengkapi metadata jaringan dan command history asli.

**Conclusion:** perbandingan mendukung keteraksesan root/parent dan penolakan direktori admin, tanpa bukti authorization bypass atau perubahan privilege.

## HTTP-14 — Secret Admin Without Trailing Slash

**Objective:** memeriksa perilaku canonicalization saat direktori admin diminta tanpa slash akhir.

**Command:**

```bash
curl -i https://hackerlab.biz.id/target/secret-admin
```

**Command Breakdown:** `curl` memakai GET; `-i` menampilkan header dan body. URL tidak berakhiran `/`. Tidak ada `-L`, sehingga respons 301 ditampilkan tanpa mengikuti nilai Location.

**Actual Result:** **OBSERVED:** cuplikan header aktual yang diberikan:

```http
HTTP/2 301
content-type: text/html; charset=iso-8859-1
location: http://hackerlab.biz.id/target/secret-admin/
server: cloudflare
```

HTML yang dilaporkan berisi `301 Moved Permanently`, tautan ke versi **HTTPS** dengan trailing slash, serta footer `Apache/2.4.58 (Ubuntu)`. HTML lengkap tidak tersedia. Nilai Location pada header menggunakan **HTTP**, walaupun request awal dan tautan pada body menggunakan HTTPS.

**Technical Explanation:** 301 mengarahkan client ke URL lain. Header Location adalah arahan redirect protokol; link body adalah elemen HTML tersendiri. Menambahkan slash merupakan pola normalisasi direktori yang dikenal, tetapi komponen pembuat redirect dan konfigurasi sebenarnya **NOT VERIFIED**.

**Important Findings:** **OBSERVED:** terdapat inkonsistensi scheme pada header Location terhadap URL request dan tautan body. **IMPORTANT:** ini perlu investigasi karena URL tujuan HTTP tidak menyediakan TLS bila benar-benar digunakan. **NOT VERIFIED:** terjadi downgrade aktual, kebocoran cookie/data, atau kondisi yang dapat dieksploitasi; perilaku client, HSTS, dan konfigurasi proxy belum diketahui. **NEXT STEP:** lakukan configuration/log review bersama pengelola, tanpa mengikuti redirect ke HTTP dalam pembaruan ini.

**Conclusion:** ada potential redirect scheme inconsistency yang teramati; belum ada kerentanan exploitable atau bypass yang terbukti.

## HTTP-15 — Secret Admin With Trailing Slash

**Objective:** membandingkan respons path slash-terminated terhadap path tanpa slash pada HTTP-14 dan hasil HTTP-12.

**Command:**

```bash
curl -i https://hackerlab.biz.id/target/secret-admin/
```

**Command Breakdown:** `curl` mengirim GET; `-i` menampilkan header/body. URL sudah memiliki slash akhir dan tetap memakai HTTPS. Ini request HTTPS tersendiri, bukan bukti curl mengikuti Location HTTP dari HTTP-14.

**Actual Result:** **OBSERVED:** cuplikan header aktual yang diberikan:

```http
HTTP/2 403
content-type: text/html; charset=UTF-8
content-length: 13
server: cloudflare
```

Body aktual yang dilaporkan:

```text
403 Forbidden
```

**Technical Explanation:** normalisasi bentuk path tidak memberi authorization. Header 301 HTTP-14 dan respons 403 HTTPS HTTP-15 tidak boleh digabung sebagai chain redirect yang sudah dijalankan.

**Important Findings:** **OBSERVED:** menambahkan slash tidak memberikan akses. **IMPORTANT:** Content-Length adalah metadata respons; jangan menyimpulkan kesamaan seluruh byte body antar-test dari ukuran saja. **NEXT STEP:** korelasikan handler dan keputusan akses melalui log jika tersedia.

**Conclusion:** direktori admin dengan slash akhir tetap menolak request; challenge belum terselesaikan.

## HTTP-17 — Complete Redirect Chain Analysis

**Objective:** memahami seluruh rantai redirect dari direktori admin tanpa slash, termasuk perubahan scheme/versi protokol, dan menentukan hasil akses akhir.

**Command:** rekonstruksi reproduksi dari perilaku chain yang dilaporkan, **bukan** command history aktual. Command asli, opsi yang dipakai, dan lingkungan eksekusi belum diberikan. Perintah berikut tidak dijalankan oleh maintainer.

```bash
curl -i -L https://hackerlab.biz.id/target/secret-admin
```

**Command Breakdown:** `curl` melakukan GET ke URL HTTPS admin tanpa slash akhir. `-i` menampilkan header respons bersama body. `-L` mengikuti redirect berdasarkan Location, sehingga dapat memperlihatkan respons perantara dan hasil akhir. URL hanya menjadi titik awal; target request berikutnya mengikuti respons server. Opsi ini tidak memberi hak akses dan tidak membuat chain aman secara otomatis.

**Actual Result:** **OBSERVED:** hasil chain yang dilaporkan pengguna:

| Tahap | Scheme request | Versi dan status respons | Peran Location / hasil |
|---|---|---|---|
| Awal | HTTPS | HTTP/2 301 | Mengarahkan ke URL HTTP |
| Perantara | HTTP | HTTP/1.1 301 | Mengarahkan kembali ke URL HTTPS |
| Akhir | HTTPS | HTTP/2 403 | Akses secret-admin tetap ditolak |

Ada dua redirect. Nilai header lengkap, body setiap tahap, timestamp, serta metadata TLS/client tidak disertakan untuk HTTP-17. Nilai Location lengkap pada HTTP-14 tidak ditranskripsikan sebagai raw header HTTP-17. Tidak ada transcript baru atau hasil sintetis yang dibuat dari tabel ini. Tanggal eksekusi belum diberikan; hasil didokumentasikan 17 November 2026.

**Technical Explanation:** 301 memberi arahan pindah URL; Location menentukan target redirect pada tingkat protokol. HTTP/1.1 dan HTTP/2 adalah versi protokol, sedangkan HTTP/HTTPS adalah scheme dan menentukan penggunaan TLS untuk koneksi tersebut. HTTP/2 terlihat pada hop awal/akhir HTTPS dan HTTP/1.1 pada hop HTTP perantara; hasil ini tidak membuktikan satu versi tertentu selalu dipakai pada seluruh host. **IMPORTANT:** “HTTP 403” adalah status respons. Request terakhir memakai HTTPS, sehingga respons akhir 403 diterima melalui HTTPS, bukan hop HTTP tambahan.

TLS termination berarti reverse proxy dapat mengakhiri TLS dari client, lalu meneruskan request ke backend melalui koneksi berbeda. Scheme eksternal perlu diteruskan secara tepercaya agar backend membangun redirect ke HTTPS yang benar. **NOT VERIFIED:** konfigurasi Apache, Cloudflare, atau informasi forwarded scheme menjadi penyebab chain ini. Header Cloudflare dan footer Apache dari histori sebelumnya tidak menetapkan komponen pembuat masing-masing 301/403. Untuk Java, konsep yang sama relevan pada API Gateway dan penanganan forwarded headers Spring Boot; target tidak dinyatakan menggunakan Spring.

**Important Findings:** **OBSERVED:** HTTPS → HTTP → HTTPS dengan respons HTTP/2 → HTTP/1.1 → HTTP/2 dan hasil akhir 403. **IMPORTANT:** redirect bukan authorization bypass. Hop HTTP berpotensi mengekspos traffic yang benar-benar melaluinya tanpa perlindungan TLS, tetapi **NOT VERIFIED:** data sensitif terkirim, terjadi penyadapan, atau ada dampak keamanan yang exploitable. Penyebab konfigurasi redirect yang tidak konsisten juga **NOT VERIFIED**. **NEXT STEP:** tinjau canonical URL, TLS termination, forwarded scheme, dan log komponen bersama pengelola; arahkan normalisasi langsung ke HTTPS tanpa hop HTTP. Lihat [penjelasan transport dan proxy](GLOSSARY.md#4-https-redirect-dan-cloudflare-reverse-proxy).

**Conclusion:** chain yang diamati menambah hop HTTP sebelum kembali ke HTTPS, tetapi akses admin tetap ditolak. Challenge tetap **IN PROGRESS — NOT SOLVED**; tidak ada bypass atau exploit yang dibuktikan.

## HTTP-18 — Browser-like Request Headers

**Objective:** memeriksa apakah User-Agent sederhana menyerupai browser dan preferensi HTML mengubah penolakan pada parent PHP endpoint `/target/ujian.php`.

**Command:** command aktual dari pengguna, ditampilkan sebagai Bash karena merupakan perintah shell, bukan YAML. Tidak dijalankan ulang oleh maintainer.

```bash
curl -i \
  -H "User-Agent: Mozilla/5.0" \
  -H "Accept: text/html" \
  https://hackerlab.biz.id/target/ujian.php
```

**Command Breakdown:** `curl` mengirim GET ke URL terakhir. `-i` menampilkan response header bersama body. Setiap `-H` menambahkan request header; kutip menjaga nama dan nilai sebagai satu argumen. User-Agent menyatakan identitas software client, tetapi nilai `Mozilla/5.0` tidak mengubah curl menjadi browser lengkap. Accept menyampaikan preferensi representasi HTML, bukan kredensial atau hak akses. Backslash pada akhir baris menyambung command Bash.

**Actual Result:** **OBSERVED:** output yang diberikan pengguna:

```http
HTTP/2 403
content-type: text/html; charset=UTF-8
content-length: 13
server: cloudflare
cf-cache-status: DYNAMIC
server-timing: cfCacheStatus;desc="DYNAMIC"
server-timing: cfEdge;dur=24,cfOrigin;dur=43

403 Forbidden
```

Tanggal/jam eksekusi, lingkungan sumber, dan command history lengkap tidak diberikan. Sumber: SRC-05 / E-HDR-14 pada [register evidence](../evidence/README.md#1-register-sumber).

Server-Timing menyampaikan metrik performa yang dipilih server/perantara: `desc` memberi deskripsi dan `dur` menyatakan durasi, lazimnya milidetik. **OBSERVED:** cfCacheStatus bernilai DYNAMIC, cfEdge memiliki dur=24 dan cfOrigin dur=43. Nama metrik adalah label implementasi; angka itu bukan bukti durasi eksekusi PHP, total waktu browser, atau sumber keputusan 403. Jangan menjumlahkannya sebagai latency total tanpa definisi dan cakupan pengukuran yang terverifikasi. **NOT VERIFIED:** komponen pembuat 403 dan detail pemrosesan origin. Referensi: [W3C Server Timing](https://www.w3.org/TR/server-timing/) dan [Cloudflare cache responses](https://developers.cloudflare.com/cache/concepts/cache-responses/).

**Technical Explanation:** 403 berarti server memahami request dan menolak memenuhinya; status ini tidak menetapkan pembuat penolakan atau membuktikan authentication berhasil. Hasil sejalan dengan HTTP-06 pada **path yang sama**, yang juga menghasilkan HTTP/2 403. Baseline HTTP-01 juga 403, tetapi memakai path berbeda, `/target/ujian.php/secret-admin`. Karena dua header diatur bersamaan, test ini tidak mengisolasi pengaruh User-Agent dan Accept secara terpisah; kesamaan semua kondisi sesi/jaringan juga belum diverifikasi.

Server-Timing adalah metadata diagnostik untuk menyampaikan metrik pemrosesan yang dinamai server. Parameter `desc` berisi deskripsi dan `dur` menyatakan durasi dalam milidetik; lihat [spesifikasi Server Timing](https://www.w3.org/TR/server-timing/). **OBSERVED:** respons melaporkan `cfCacheStatus` dengan deskripsi DYNAMIC, `cfEdge` bernilai 24, dan `cfOrigin` bernilai 43. Ini metrik yang dinyatakan respons, bukan hasil tracing independen. Batas pengukuran dan hubungan antar-metrik tidak dibuktikan pada lab ini: jangan menjumlahkannya sebagai total latency client atau menyamakan cfOrigin dengan durasi eksekusi PHP. [Dokumentasi cache Cloudflare](https://developers.cloudflare.com/cache/concepts/cache-responses/) menjelaskan DYNAMIC sebagai keputusan tidak menggunakan cache untuk resource tersebut; status cache dan timing tidak cukup untuk menentukan komponen yang membuat 403 atau aturan authorization yang berlaku.

**Important Findings:** **OBSERVED:** respons tetap 403, bertipe HTML UTF-8, dengan Content-Length 13 dan body `403 Forbidden`. **IMPORTANT:** User-Agent dapat ditentukan client dan bukan bukti identitas browser tepercaya; preferensi HTML bukan authorization. **NOT VERIFIED:** Cloudflare atau origin menjadi pembuat penolakan, serta apakah masing-masing header memengaruhi jalur internal request. **NEXT STEP:** rencanakan pengujian X-Original-URL sebagai client-controlled routing header, dengan baseline dan satu variabel yang terkontrol. Nilai header, endpoint pembanding, dan batas scope perlu ditentukan sebelum pelaksanaan; belum ada command yang dijalankan atau hasil untuk rencana tersebut.

**Conclusion:** kombinasi header yang diuji tidak memberikan akses pada parent PHP endpoint. Belum ada authorization bypass yang terbukti. Status tetap **IN PROGRESS — NOT SOLVED**.

## HTTP-20 — SHA-256 Response Body Comparison

**Objective:** membandingkan response body request normal ke root dengan request yang menambahkan X-Original-URL menuju direktori admin.

**Command:** dua command aktual dari pengguna, ditulis sebagai Bash; command kedua adalah perintah shell, bukan YAML. Maintainer tidak menjalankan ulang pengujian.

Baseline:

```bash
curl -sS https://hackerlab.biz.id/ | sha256sum
```

Dengan X-Original-URL:

```bash
curl -sS \
  -H "X-Original-URL: /target/secret-admin/" \
  https://hackerlab.biz.id/ \
  | sha256sum
```

**Command Breakdown:** `curl` melakukan GET ke root HTTPS pada kedua command. `-sS` menyembunyikan progress tetapi tetap menampilkan pesan error. `-H` menambahkan request header; nilainya meminta path `/target/secret-admin/` menurut konvensi routing yang sedang diuji, tanpa mengubah URL tujuan curl. Backslash menyambung baris Bash. Operator pipe `|` mengalirkan stdout command di kiri menjadi stdin command di kanan. `sha256sum` membaca byte dari stdin dan menghitung SHA-256. SHA-256 adalah fungsi hash kriptografis yang menghasilkan digest 256 bit, biasanya ditampilkan sebagai 64 karakter heksadesimal.

**Actual Result:** **OBSERVED:** pengguna melaporkan hash yang sama untuk kedua command:

```text
f99f739d2378e0e915d095347f0b32d621e842d0c57cc538b17860c9236bc5f9
```

Ini nilai digest yang diberikan, bukan rekonstruksi seluruh output terminal. Status HTTP, header respons, panjang body, tanggal/jam, dan lingkungan eksekusi tidak diberikan pada HTTP-20. Sumber: SRC-06 / E-HDR-15 pada [register evidence](../evidence/README.md#1-register-sumber). HTTP-19 belum diberikan; nomor tersebut tidak diisi dengan hasil rekaan.

**Technical Explanation:** perubahan byte pada data umumnya menghasilkan digest SHA-256 berbeda; digest identik memberikan bukti praktis yang kuat bahwa byte body yang masuk ke sha256sum sama, tanpa perlu menampilkan seluruh konten. Kemungkinan collision secara teoretis tetap ada. **OBSERVED:** tidak ada perubahan konten yang terdeteksi melalui perbandingan hash pada dua hasil ini.

Command tidak memakai `-i` atau `-D -`, sehingga yang masuk ke pipe adalah body keluaran curl, bukan status dan response header. Karena itu digest ini bukan hash seluruh HTTP response, framing HTTP/2, atau traffic TLS. Hash body sama tidak menetapkan status, header, keputusan authorization, ataupun jalur pemrosesan internal yang sama. Pesan error curl melalui stderr tidak masuk pipe ini; kelengkapan transfer perlu dicatat pada pengujian berikutnya, tanpa mengasumsikan terjadi kegagalan pada hasil yang diberikan.

**Important Findings:** **OBSERVED:** kedua response body mempunyai hash identik. **IMPORTANT:** X-Original-URL belum menunjukkan pengaruh routing yang terlihat pada konten. **NOT VERIFIED:** header diterima lalu diabaikan aplikasi, dihapus/ditimpa reverse proxy, atau diproses melalui jalur lain yang menghasilkan body sama. Hash identik tidak membuktikan server pasti mengabaikan header. **NOT VERIFIED:** terdapat authorization bypass melalui mekanisme lain. **NEXT STEP:** uji X-Rewrite-URL dan bandingkan dengan baseline; ini rencana, belum dijalankan dan belum memiliki hasil. Catat status, header, kelengkapan transfer, dan hash body agar perbandingan lebih lengkap.

**Conclusion:** tidak ditemukan perubahan body yang terdeteksi oleh SHA-256 pada pasangan request ini. Belum ada authorization bypass yang terbukti. Status tetap **IN PROGRESS — NOT SOLVED**.

## HTTP-21 — X-Rewrite-URL Header Testing

**Objective:** mengetahui apakah X-Rewrite-URL yang dikendalikan client memengaruhi routing yang terlihat atau mengubah response body. Entri ini melanjutkan rencana historis pada HTTP-20; hasil sebelumnya tidak diubah.

### Command 1 — Header Manipulation

**Tujuan:** melihat status, metadata respons, dan konten saat root diminta dengan header routing menuju direktori admin.

**Command:** command aktual pengguna, ditampilkan sebagai Bash karena merupakan perintah shell, bukan YAML. Maintainer tidak menjalankannya.

```bash
curl -i \
  -H "X-Rewrite-URL: /target/secret-admin/" \
  https://hackerlab.biz.id/
```

**Command Breakdown:** `curl` mengirim GET ke root HTTPS. `-i` menampilkan response header dan body. `-H` menambahkan header sebagai satu argumen yang diapit tanda kutip. X-Rewrite-URL membawa path yang ingin diuji sebagai metadata routing; URL tujuan curl tetap root, bukan direktori admin. Backslash di akhir baris menyambung command Bash.

**Actual Result:** **OBSERVED:** ringkasan aktual yang diberikan pengguna:

| Properti | Hasil |
|---|---|
| HTTP status | 200 OK |
| Content-Type | `text/html` |
| Server | `cloudflare` |
| CF-Cache-Status | `DYNAMIC` |
| Response body yang diidentifikasi pengguna | Pendaftaran VPS Ujian |

Ini ringkasan hasil, bukan raw transcript atau HTML lengkap. Versi HTTP, timestamp, dan lingkungan eksekusi tidak diberikan untuk HTTP-21.

**Technical Explanation:** respons 200 menunjukkan request root berhasil, tetapi konten yang dilaporkan tetap halaman pendaftaran VPS. Status tersebut tidak membuktikan akses admin. Nama header saja tidak menjamin proxy atau aplikasi menggunakan nilainya.

**Important Findings:** **OBSERVED:** konten yang terlihat tetap halaman pendaftaran. **NOT VERIFIED:** komponen mana yang menerima, menghapus, mengabaikan, atau tidak menggunakan header. Server cloudflare dan DYNAMIC bukan bukti lokasi keputusan routing.

**Lessons Learned:** bandingkan identitas konten dan resource yang diminta, bukan status 200 saja; keberhasilan mengakses halaman publik berbeda dari authorization pada resource admin.

### Command 2 — SHA-256 Verification

**Tujuan:** membandingkan byte body dengan digest baseline dan X-Original-URL yang dicatat pada HTTP-20.

**Command:** command aktual pengguna, ditampilkan sebagai Bash.

```bash
curl -sS \
  -H "X-Rewrite-URL: /target/secret-admin/" \
  https://hackerlab.biz.id/ \
  | sha256sum
```

**Command Breakdown:** `-sS` menyembunyikan progress namun tetap menampilkan error. `-H` mengirim X-Rewrite-URL dengan nilai yang sama seperti command pertama. URL tetap root HTTPS. Pipe `|` mengalirkan stdout curl ke stdin `sha256sum`, yang menghitung SHA-256 atas byte yang diterima. Tanpa `-i`, header dan status tidak ikut di-hash; backslash menyambung baris. Penjelasan SHA-256 ada pada [HTTP-20](#http-20--sha-256-response-body-comparison).

**Actual Result:** **OBSERVED:** digest aktual yang diberikan:

```text
f99f739d2378e0e915d095347f0b32d621e842d0c57cc538b17860c9236bc5f9
```

Pengguna menyatakan hash identik dengan baseline dan X-Original-URL. Status/header dari command pertama tidak otomatis dianggap sebagai capture command kedua yang terpisah.

**Technical Explanation:** hash identik merupakan bukti praktis yang sangat kuat bahwa byte body yang dibandingkan sama; kemungkinan collision secara teoretis tetap ada. Ini hash body, bukan seluruh respons. Jalur internal berbeda masih dapat menghasilkan konten sama, sehingga digest tidak menentukan apakah header diabaikan.

**Important Findings:** **OBSERVED:** SHA-256 identik dengan baseline dan tidak ada perubahan konten yang terdeteksi. **IMPORTANT:** X-Rewrite-URL belum menunjukkan pengaruh routing yang terlihat pada hasil ini. **NOT VERIFIED:** adanya authorization bypass; tidak ada bypass yang terbukti.

**Lessons Learned:** hash melengkapi inspeksi halaman dengan perbandingan byte, tetapi tidak menggantikan bukti routing, keputusan authorization, atau log proxy/origin.

### Perbandingan baseline dan routing header

Semua URL tujuan adalah `https://hackerlab.biz.id/`. Digest lengkap pada blok di atas berlaku untuk ketiga baris, berdasarkan laporan pengguna.

| Request | Sumber | SHA-256 body | Batas informasi |
|---|---|---|---|
| Baseline GET tanpa header routing tambahan | HTTP-20 | Identik dengan digest di atas | Status/header pasangan hash HTTP-20 tidak diberikan |
| `X-Original-URL: /target/secret-admin/` | HTTP-20 | Identik dengan digest di atas | Tidak menetapkan apakah header digunakan server |
| `X-Rewrite-URL: /target/secret-admin/` | HTTP-21 | Identik dengan digest di atas | Command pertama terpisah melaporkan 200 dan halaman Pendaftaran VPS Ujian |

**Conclusion:** tidak ditemukan perubahan routing yang terlihat atau konten akibat X-Rewrite-URL pada pengujian ini. Routing internal dan penanganan header tetap **NOT VERIFIED**. Sumber: SRC-07 / E-HDR-16 pada [register evidence](../evidence/README.md#1-register-sumber). Status tetap **IN PROGRESS — NOT SOLVED**.

### Next Planned Investigation — Inspeksi HTML directory listing

**NEXT STEP:** periksa HTML `/target/` untuk petunjuk. Command berikut adalah rencana pengguna, **belum dijalankan**, dan tidak memiliki hasil yang didokumentasikan:

```bash
curl -sS https://hackerlab.biz.id/target/ \
  | grep -Ein 'secret|admin|header|hint|comment|<!--|ujian'
```

`curl -sS` mengambil body tanpa progress tetapi tetap menampilkan error. Pipe meneruskan HTML ke `grep`. `-E` menggunakan extended regular expression, `-i` mengabaikan kapitalisasi, dan `-n` menampilkan nomor baris. Karakter `|` di dalam pola yang dikutip berarti pilihan kata/pola, berbeda dari pipe shell di luar kutip. Pencarian ini hanya menyaring baris; tidak mem-parsing HTML secara menyeluruh. Jika petunjuk kelak ditemukan, petunjuk itu tetap memerlukan validasi tersendiri.

## HTTP-22 — HTML Source Inspection

**Objective:** mencari petunjuk challenge HTTP Header Manipulation dalam HTML directory listing `/target/`. Pengujian ini merealisasikan rencana inspeksi setelah HTTP-21; rencana terdahulu dipertahankan sebagai histori.

**Command:** command aktual pengguna, ditampilkan sebagai Bash karena merupakan perintah shell, bukan Perl. Maintainer tidak menjalankan ulang request.

```bash
curl -sS https://hackerlab.biz.id/target/ \
  | grep -Ein 'secret|admin|header|hint|comment|<!--|ujian'
```

**Command Breakdown:** `curl` mengambil body halaman `/target/` melalui GET. `-sS` menggabungkan mode senyap (`-s`, tanpa progress) dengan penampilan pesan error (`-S`). Backslash melanjutkan command ke baris berikutnya. Pipe shell `|` mengalirkan stdout curl ke stdin `grep`; pesan error pada stderr tidak ikut disaring oleh pipe ini. `grep` memilih baris yang cocok dengan pola. Opsi gabungan `-Ein` berarti `-E` menggunakan extended regular expression, `-i` mengabaikan kapitalisasi, dan `-n` menambahkan nomor baris. Tanda kutip tunggal menjaga pola sebagai satu argumen literal shell. Di dalam pola itu, `|` berarti pilihan pola, bukan pipe shell; `<!--` mencari pembuka komentar HTML.

**Actual Result:** **OBSERVED:** pengguna melaporkan dua baris yang cocok:

| Nomor baris | Elemen yang dilaporkan |
|---|---|
| 12 | Direktori `secret-admin/` |
| 13 | File `ujian.php` |

Tidak ditemukan petunjuk tambahan terkait header atau hint dalam hasil pencarian tersebut. Tabel ini merupakan ringkasan hasil, bukan rekonstruksi HTML/baris grep lengkap. Status HTTP, header, waktu eksekusi, dan lingkungan sumber tidak diberikan untuk HTTP-22. Sumber: SRC-08 / E-HDR-17 pada [register evidence](../evidence/README.md#1-register-sumber).

**Technical Explanation:** nomor yang ditambahkan `grep -n` menunjukkan urutan baris pada input HTML yang diterima grep, bukan posisi elemen pada tampilan browser, nomor file server, atau nomor hasil pencarian. Contohnya, nomor 12 berarti kecocokan terjadi pada baris input ke-12. Temuan konsisten dengan listing yang sebelumnya dilaporkan pada HTTP-11. Directory listing dapat mengungkap nama dan struktur resource; keberadaan nama tidak membuktikan izin mengakses isinya.

**Important Findings:** **OBSERVED:** listing memuat `secret-admin/` dan `ujian.php`; pencarian ini tidak menghasilkan petunjuk header tambahan. **IMPORTANT:** grep hanya menyaring pola tertentu pada satu body yang diterima, bukan mem-parsing seluruh situs atau seluruh HTML secara semantik. Petunjuk dapat menggunakan kata lain, encoding berbeda, resource terpisah, atau endpoint lain. **NOT VERIFIED:** apakah petunjuk tersedia pada halaman/endpoint lain. Tidak adanya kecocokan tambahan bukan bukti bahwa petunjuk tersebut tidak ada di tempat lain.

**Lessons Learned:** gunakan hasil pencarian untuk mempersempit investigasi dan nyatakan batas cakupannya. Dua nama resource merupakan observasi; asumsi bahwa seluruh petunjuk telah ditemukan atau tidak tersedia memerlukan bukti tambahan.

**Conclusion:** pencarian HTML menegaskan dua resource yang sudah dikenal tanpa petunjuk header tambahan dalam hasil yang dilaporkan. Belum ada authorization bypass terbukti; status tetap **IN PROGRESS — NOT SOLVED**.

### Next Planned Test — HTTP-23

**NEXT STEP:** analisis pengaruh X-Forwarded-Proto terhadap redirect HTTPS/HTTP pada path admin tanpa trailing slash. Command berikut merupakan rencana pengguna, **belum dijalankan** dan belum memiliki hasil:

```bash
curl -i \
  -H "X-Forwarded-Proto: https" \
  https://hackerlab.biz.id/target/secret-admin
```

`-i` menampilkan response header dan body; `-H` menambahkan X-Forwarded-Proto dengan nilai `https`. Header tersebut menyampaikan informasi scheme menurut konvensi proxy, tetapi nilai dari client tidak otomatis dipercaya server. Jika pengujian kelak dijalankan dalam scope, bandingkan status dan Location dengan HTTP-14. Command tidak memakai `-L`, sehingga tidak mengikuti redirect otomatis. **NOT VERIFIED:** header ini memengaruhi redirect atau dipakai oleh proxy/origin. Tidak ada hasil HTTP-23 yang diklaim.

## ENV-01 — Kali Linux PATH Issue

**Objective:** memisahkan error penemuan command lokal dari hasil pengujian HTTP dan memahami perbedaan dua lingkungan shell.

**Command:** verifikasi yang dilaporkan pengguna:

```bash
command -v curl
curl --version
```

Contoh reproduksi koreksi sementara berikut memakai direktori yang dilaporkan hilang. **IMPORTANT:** baris export ini adalah contoh penjelasan; sintaks export asli sesi tidak diberikan dan tidak diklaim sebagai command history aktual.

```bash
export PATH="/usr/bin:/bin:$PATH"
```

**Command Breakdown:** `command -v curl` meminta shell menjelaskan command `curl` yang akan ditemukan; `-v` pada builtin `command` menampilkan hasil pencarian. `curl --version` meminta versi tool secara lokal tanpa request HTTP. `export` mengekspor nilai PATH ke proses anak; `/usr/bin:/bin` adalah direktori pencarian yang dipisahkan `:`, dan `$PATH` mempertahankan nilai sebelumnya. Tidak ada perubahan pada file startup shell atau isi filesystem dalam contoh export tersebut.

**Actual Result:** **OBSERVED:** percobaan awal melaporkan `curl: command not found`; shell menyatakan `/usr/bin:/bin` hilang dari PATH. Pengguna melaporkan koreksi PATH sementara dan hasil verifikasi lokasi `/usr/bin/curl`, versi `8.20.0`. Output versi lengkap dan nilai PATH lengkap sebelum/sesudah tidak diberikan. Tidak ada bukti instalasi ulang curl, dan tidak ada hasil loop HTTP-13 yang berhasil dari Kali setelah koreksi tersebut.

**Technical Explanation:** PATH adalah daftar direktori untuk mencari command; filesystem menyimpan file/direktori itu sendiri. Binary dapat tetap terpasang pada `/usr/bin/curl` walaupun pencarian `curl` gagal karena `/usr/bin` tidak ada dalam daftar. Shell Kali dan root shell container adalah lingkungan terpisah; keduanya dapat memiliki PATH, filesystem, tool, identitas, dan jalur jaringan berbeda. Label root shell tidak menunjukkan akses root pada target lab atau keberhasilan container escape.

**Important Findings:** **OBSERVED:** HTTP-13 berhasil pada root shell container yang terpisah dari Kali yang mengalami masalah PATH. **IMPORTANT:** export sementara berlaku pada shell yang diubah dan proses anak yang dibuat setelahnya; tidak mengubah shell lain yang telah berjalan atau sesi baru secara otomatis. **NOT VERIFIED:** nilai PATH yang sama berlaku di kedua lingkungan, atau versi curl root shell sama dengan `8.20.0` yang dilaporkan pada verifikasi Kali. **NEXT STEP:** catat lingkungan asal, versi tool, dan perubahan PATH per sesi sebelum membandingkan pengujian.

**Conclusion:** error lokal penemuan curl tidak membuktikan gangguan target. Perbaikan PATH membuat tool terdeteksi dalam sesi terkait; hasil HTTP yang berhasil tetap harus diatribusikan ke root shell container.
