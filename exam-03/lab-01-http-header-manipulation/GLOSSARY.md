# HTTP Header Manipulation — Fundamentals dan Command Reference

[Laporan challenge](README.md) · [Learning journal](TEST-LOG.md) · [Overview Exam 3](../README.md)

Dokumen ini menjelaskan konsep untuk pembaca pemula. **IMPORTANT:** penjelasan arsitektur dan contoh Spring adalah materi pembelajaran, bukan bukti bahwa target memakai Java, Spring Boot, Spring Security, atau konfigurasi tertentu. Observasi aktual hanya dicatat dalam jurnal dan register evidence.

## 1. HTTP request, response, dan status

Request adalah pesan client ke server: method, URL/path, header, dan body opsional. Response adalah balasan: status, header, dan body sesuai method/status. GET meminta representasi; HEAD memiliki semantik GET tanpa response body; OPTIONS meminta informasi opsi komunikasi. Method berbeda dapat melalui aturan berbeda.

| Status | Makna ringkas | Hubungan dengan hasil lab |
|---|---|---|
| 200 OK | Request berhasil sesuai semantik method | Terlihat pada root dan `/target/`, bukan bukti hak admin |
| 301 Moved Permanently | Resource diarahkan ke URI permanen lain | HTTP-14 memuat Location; konten admin belum terbuka |
| 401 Unauthorized | Memerlukan kredensial autentikasi valid | Konsep pembanding; tidak diamati pada test ini |
| 403 Forbidden | Server memahami request dan menolak memenuhinya | Tidak menentukan identitas pengguna atau pembuat penolakan |
| 405 Method Not Allowed | Method tidak didukung pada resource tersebut | Tidak diamati; berbeda dari OPTIONS 403 |

Kelompok 2xx menyatakan keberhasilan, 3xx pengalihan, 4xx penolakan/kesalahan terkait request, dan 5xx kegagalan server. HTTP/2 adalah versi protokol; `403` adalah status. Teks seperti `Forbidden` yang ditampilkan tool tidak berarti HTTP/2 memiliki reason phrase seperti HTTP/1.1. Referensi: [RFC 9110](https://www.rfc-editor.org/rfc/rfc9110.html).

## 2. Request header dan response header

Request header menyampaikan metadata/preferensi client. Response header menyampaikan metadata/arah respons server. Nama header tidak peka huruf besar/kecil. Client yang bisa mengatur header tidak otomatis dapat mengatur kebijakan server.

| Header | Arah dan fungsi | Batas kepercayaan / interpretasi |
|---|---|---|
| X-Forwarded-For | Request; konvensi alamat client melalui proxy | Nilai dari client harus dinormalisasi oleh trusted proxy; `127.0.0.1` tidak mengubah sumber koneksi |
| X-Real-IP | Request; konvensi proxy untuk alamat client | Makna bergantung konfigurasi; bukan bukti identitas internal |
| Referer | Request; URI perujuk | Dapat diatur curl; bukan bukti login atau navigasi browser sebenarnya |
| Accept | Request; preferensi media type | Preferensi JSON bukan izin akses atau jaminan response JSON |
| Content-Type | Response pada hasil ini; tipe representasi yang dikirim | HTTP-08 tetap HTML meskipun Accept meminta JSON |
| Content-Length | Response pada hasil ini; panjang body yang dinyatakan | `13` tidak membuktikan byte body identik pada semua test |
| Location | Response; URI tujuan redirect | Bedakan nilainya dari tautan yang ada pada body HTML |
| Server | Response; identitas software yang dinyatakan | `cloudflare` tidak menentukan pihak yang membuat 403 |
| CF-Cache-Status | Response; metadata cache Cloudflare | `DYNAMIC` tidak mengidentifikasi sumber penolakan |
| Allow | Response; method yang didukung jika disediakan | Tidak ditemukan pada OPTIONS 403; daftar method belum diketahui |

**IMPORTANT:** reverse proxy harus membatasi header identitas yang dipercaya. Spring menjelaskan trust boundary untuk Forwarded dan X-Forwarded dalam [Forwarded Headers](https://docs.spring.io/spring-framework/reference/web/webmvc/filters.html#filters-forwarded-headers). Header respons bukan salinan pasti konfigurasi internal.

## 3. Directory listing, directory traversal, dan trailing slash

**Directory listing** adalah halaman yang menampilkan isi sebuah direktori. HTTP-11 memperlihatkan `Index of /target/`, `secret-admin/`, dan `ujian.php`. Itu mengungkap struktur yang terlihat, tetapi tidak membuktikan konten `secret-admin/` atau source `ujian.php` dapat dibaca.

**Normal directory access** meminta URL seperti `/target/` atau `/target/secret-admin/`. **Directory traversal** berupaya keluar dari batas direktori yang diizinkan melalui manipulasi path. Tidak ada directory traversal yang dilaporkan pada test ini; membaca listing bukan traversal.

**Trailing slash** adalah `/` di akhir path. Tanpa slash, server dapat mengarahkan ke bentuk canonical dengan slash agar resolusi resource relatif konsisten. Perilaku aktual bergantung server/router. HTTP-14 menghasilkan 301 untuk path tanpa slash, sementara HTTP-12/15 pada HTTPS dengan slash menghasilkan 403; normalisasi path tidak berarti mendapat authorization.

Apache HTTP Server adalah software web server. Dokumentasi [mod_autoindex](https://httpd.apache.org/docs/2.4/mod/mod_autoindex.html) menjelaskan pembuatan listing, dan [mod_dir](https://httpd.apache.org/docs/2.4/mod/mod_dir.html) menjelaskan indeks direktori serta trailing-slash redirect. **NOT VERIFIED:** modul itulah yang menghasilkan halaman/redirect target. Footer `Apache/2.4.58 (Ubuntu)` hanya banner teramati; tidak cukup untuk memastikan paket, patch, CVE, atau pembuat 403.

## 4. HTTPS, redirect, dan Cloudflare reverse proxy

HTTPS memakai TLS untuk melindungi koneksi client ke endpoint TLS; HTTP biasa tidak memberikan perlindungan TLS. Reverse proxy menerima request di depan origin dan dapat meneruskannya ke backend. Protokol client–proxy dan proxy–origin dapat berbeda. **NOT VERIFIED:** konfigurasi TLS internal target dan apakah request tertentu diteruskan ke origin.

Cloudflare adalah layanan yang dapat berperan sebagai reverse proxy di depan origin. **OBSERVED:** beberapa respons memuat `server: cloudflare`. Keberadaannya tidak membuktikan seluruh 403 dibuat di Cloudflare; lihat [Cloudflare Error 403](https://developers.cloudflare.com/support/troubleshooting/http-status-codes/4xx-client-error/error-403/). Korelasi event edge dan log origin diperlukan untuk menentukan pembuat penolakan.

Pada HTTP-14, request **HTTPS** menerima header Location ke **HTTP**, sementara tautan HTML menunjuk **HTTPS**. Header dan body merupakan dua keluaran yang perlu dibandingkan. **IMPORTANT:** jika client memakai tujuan HTTP itu, perlindungan TLS untuk hop tersebut tidak tersedia. Command HTTP-14 tidak memakai `-L`, sehingga test itu sendiri tidak membuktikan redirect diikuti. HTTP-17 kemudian melaporkan dua redirect yang diikuti: HTTPS → HTTP → HTTPS, berakhir pada 403. HSTS dan perilaku client lain belum diperiksa. Tidak ada kebocoran data atau exploitable downgrade yang dibuktikan.

**HTTP/1.1 dan HTTP/2** adalah versi protokol untuk pertukaran pesan HTTP; HTTP/2 memakai framing biner dan multiplexing stream, sebagaimana dijelaskan dalam [RFC 9113](https://www.rfc-editor.org/rfc/rfc9113.html). **HTTP dan HTTPS** pada URL adalah scheme; HTTPS menambahkan TLS. **301 dan 403** adalah status respons. Pada HTTP-17, HTTP/2 muncul pada respons awal/akhir dan HTTP/1.1 pada respons perantara. Ini fakta pada chain tersebut, bukan kesimpulan bahwa seluruh deployment hanya mendukung versi tertentu. Frasa “HTTP 403” tidak berarti scheme URL berubah menjadi HTTP: respons akhir HTTP-17 diterima melalui HTTPS.

**TLS termination** terjadi ketika reverse proxy mengakhiri koneksi TLS client. Koneksi proxy–backend memiliki karakteristik tersendiri. Agar backend menghasilkan URL publik yang benar, scheme/host asli perlu diteruskan dan diproses melalui trust boundary yang jelas. Redirect sebaiknya langsung ke HTTPS canonical untuk menghindari hop HTTP serta round trip tambahan. **NOT VERIFIED:** mekanisme termination/forwarded scheme tertentu menyebabkan perilaku target.

**NOT VERIFIED:** inkonsistensi disebabkan informasi scheme yang hilang setelah TLS termination, konfigurasi canonical URL, atau penulisan ulang respons. Ini daftar alternatif untuk review, bukan diagnosis target. Tidak ada indikasi redirect ke domain yang dikendalikan pihak lain; jangan menyebutnya open redirect dari data ini.

## 5. Authentication, authorization, dan relevansi backend Java

**Authentication** memverifikasi siapa pengguna. **Authorization** memutuskan apa yang boleh diakses pengguna tersebut. Server boleh mengizinkan root/listing publik dan menolak resource admin. Status 403 saja tidak mengungkap apakah pengguna sudah diautentikasi.

Untuk backend Java, terapkan kebijakan resource pada Spring Security; jangan menjadikan Referer atau IP pada header client sebagai hak administrator. Cocokkan aturan method/path dengan resource yang memang dilindungi. Detail mekanisme tersedia dalam [Spring Security request authorization](https://docs.spring.io/spring-security/reference/servlet/authorization/authorize-http-requests.html). Ini relevansi konsep, bukan identifikasi framework target.

API Gateway/reverse proxy sering mengakhiri TLS, menerapkan routing, dan meneruskan request. Backend perlu menerima informasi host/scheme yang benar dari proxy yang dipercaya agar URL dan redirect sesuai endpoint publik. Gateway dan aplikasi perlu menyepakati batas kepercayaan dan keputusan otorisasi, serta memiliki ID korelasi untuk investigasi lintas lapisan.

Spring Boot menyediakan pilihan `server.forward-headers-strategy`, termasuk penanganan native server atau framework. Pemilihan harus sesuai versi, server, dan deployment; jangan mengaktifkan kepercayaan pada semua header dari internet sebagai perbaikan umum. Spring mendokumentasikannya dalam [Running Behind a Front-end Proxy Server](https://docs.spring.io/spring-boot/how-to/webserver.html#howto.webserver.use-behind-a-proxy-server). ForwardedHeaderFilter/Transformer dan sanitasi header pada trust boundary perlu ditinjau bersama konfigurasi proxy; tidak ada perubahan source/configuration aplikasi dalam tugas ini.

## 6. Command reference

| Sintaks | Arti praktis |
|---|---|
| `curl URL` | GET pada penggunaan dalam jurnal |
| `-i` | Tampilkan response header dan body |
| `-I` | Gunakan HEAD; berbeda dari `-i` |
| `-H "Nama: nilai"` | Tambahkan request header sebagai satu argumen |
| `-X OPTIONS` | Pilih method OPTIONS |
| `-sS` | Sembunyikan progress, tetap tampilkan pesan error |
| `-D -` | Tulis response header ke stdout |
| `-o /dev/null` | Buang body secara lokal setelah transfer |
| `-w "format"` | Cetak metrik transfer menurut format |
| `%{http_code}` | Status HTTP hasil transfer |
| `%{redirect_url}` | URL redirect bila respons mengarahkannya |
| `%{size_download}` | Byte body yang diunduh, bukan total traffic |
| `\n` di string `-w` | Newline pada keluaran curl |
| `-L` | Ikuti redirect; dipakai pada command reproduksi HTTP-17 |

Referensi opsi: [curl manual](https://curl.se/docs/manpage.html). Format metrik dijelaskan dalam [everything curl: Write out](https://everything.curl.dev/usingcurl/verbose/writeout.html). Status HTTP yang diterima tidak sama dengan exit status program; curl dapat menyelesaikan transfer respons 403 tanpa menyatakannya sebagai error transfer. Lihat [everything curl: Responses](https://everything.curl.dev/http/response.html). Jangan mengganti hasil HTTP dengan asumsi berdasarkan exit status saja.

## 7. Bash, PATH, dan lingkungan shell

| Istilah | Penjelasan |
|---|---|
| Shell | Proses yang membaca command dan menjalankan program |
| Filesystem | Tempat file/direktori berada; `/usr/bin/curl` adalah lokasi binary yang dilaporkan |
| PATH | Variabel daftar direktori pencarian command, dipisahkan `:` pada Linux |
| `command -v curl` | Tanyakan command curl yang ditemukan shell |
| `curl --version` | Tampilkan versi lokal tanpa request ke server |
| `export` | Jadikan variabel tersedia untuk proses anak shell tersebut |
| `$request_path` | Ekspansi nilai variabel loop dalam HTTP-13 |
| `\` di akhir baris | Lanjutkan satu command Bash pada baris berikutnya |
| Root shell | Shell dengan konteks root pada lingkungannya; bukan bukti root pada target |

File dapat ada pada filesystem tetapi command tidak ditemukan jika direktori file itu tidak ada di PATH. Export sementara mengubah shell terkait dan environment proses anak berikutnya; tidak memutakhirkan shell lain yang sudah berjalan atau menyimpan perubahan permanen. Kali shell dan root shell container memiliki konteks terpisah. IP sumber, route, dan versi tool tidak boleh dianggap sama tanpa bukti. [ENV-01](TEST-LOG.md#env-01--kali-linux-path-issue) mencatat error serta hasil verifikasi yang benar-benar dilaporkan.

## 8. Checklist belajar berikutnya

- Dapat menjelaskan mengapa GET yang body-nya dibuang tetap berbeda dari HEAD.
- Dapat membedakan header dari client, metadata proxy tepercaya, dan keputusan authorization server.
- Dapat memisahkan directory listing, akses resource, dan traversal.
- Dapat membaca 301 serta membandingkan scheme request, Location, dan tautan HTML.
- Dapat menelusuri error lokal PATH tanpa menyatakannya sebagai gangguan jaringan/target.
- Dapat menjelaskan hasil per lingkungan dan memisahkan **OBSERVED**, **NOT VERIFIED**, dan **NEXT STEP**.
