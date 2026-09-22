# Pentest Learning Notes

## Lab 06 — IDOR Enumeration & Data Discovery

> **Catatan:** Pengujian ini dilakukan pada environment lab/VPS yang memang disediakan dan diizinkan untuk latihan penetration testing. Teknik dalam dokumentasi ini hanya digunakan pada sistem yang memiliki izin untuk diuji.

---

## 1. Informasi Lab

| Informasi | Keterangan |
|---|---|
| Lab | 06 |
| Path | `/ujiandata` |
| IP Target | `192.168.39.87` |
| Akses melalui tunnel | `http://localhost:8080/ujiandata/` |
| Kategori | Broken Access Control |
| Vulnerability | IDOR / Object Enumeration |
| Parameter | `id` |
| Range pengujian | `1` sampai `50` |
| Tools | `curl`, `grep`, Bash loop |
| Tujuan | Menemukan object tertentu yang berisi link `whatsapp.com` |

---

## 2. Tujuan Pembelajaran

Pada Lab 06 saya mempelajari bagaimana parameter object seperti:

```text
?id=<number>
```

dapat diuji secara terstruktur untuk mengetahui apakah object lain dapat diakses hanya dengan mengganti nilai identifier.

Konsep utama yang dipelajari:

- Object identifier
- IDOR (Insecure Direct Object Reference)
- Broken Access Control
- Enumeration
- Sequential ID
- Bash loop
- HTTP request menggunakan `curl`
- Filtering response menggunakan `grep`
- Perbedaan enumeration dan exploitation
- Pentingnya object-level authorization

---

## 3. Environment dan Akses Target

Target menggunakan IP lab yang sama seperti sebelumnya:

```text
192.168.39.87
```

Path target:

```text
/ujiandata
```

Pada pengujian lokal melalui SSH tunnel, aplikasi diakses menggunakan:

```text
http://localhost:8080/ujiandata/
```

Alur koneksi:

```text
Laptop
   │
   │ localhost:8080
   ▼
SSH Tunnel
   │
   ▼
VPS / Jump Server
   │
   ▼
192.168.39.87
   │
   └── /ujiandata
```

### Dokumentasi

![Halaman awal Lab 06](images/01-target-page.png)

> Ganti gambar di atas dengan screenshot halaman awal `/ujiandata`.

---

## 4. Identifikasi Parameter

Pada aplikasi ditemukan parameter:

```text
id
```

Contoh pola URL:

```text
http://localhost:8080/ujiandata/?id=1
```

Parameter tersebut menentukan object/data yang akan ditampilkan oleh aplikasi.

Secara konseptual:

```text
Request
   │
   │ ?id=1
   ▼
Application
   │
   ▼
Cari object ID 1
   │
   ▼
Return response
```

Hal yang perlu diperhatikan adalah apakah server melakukan pemeriksaan authorization sebelum memberikan object tersebut.

---

## 5. Enumeration

Karena identifier berbentuk angka, saya melakukan pengujian terhadap beberapa nilai ID secara berurutan.

Range yang digunakan pada lab:

```text
1 - 50
```

Tujuannya bukan mencoba angka secara acak, melainkan melakukan **enumeration** untuk melihat apakah object tertentu menghasilkan response yang berbeda atau memiliki informasi yang dicari.

Secara sederhana:

```text
id=1
id=2
id=3
id=4
...
id=50
```

Tanpa automation, setiap URL harus dibuka satu per satu.

Karena itu saya menggunakan Bash loop.

---

## 6. Automation dengan Bash Loop

Command yang digunakan:

```bash
for i in {1..50}; do
    echo -n "ID $i: "
    curl -s "http://localhost:8080/ujiandata/?id=$i" | grep -i "whatsapp.com"
done
```

Versi satu baris:

```bash
for i in {1..50}; do echo -n "ID $i: "; curl -s "http://localhost:8080/ujiandata/?id=$i" | grep -i "whatsapp.com"; done
```

### Penjelasan

| Bagian | Fungsi |
|---|---|
| `for i in {1..50}` | Melakukan perulangan dari angka 1 sampai 50 |
| `echo -n "ID $i: "` | Menampilkan ID yang sedang diuji |
| `curl -s` | Mengirim HTTP request tanpa progress output |
| `?id=$i` | Mengganti nilai parameter `id` pada setiap iterasi |
| `grep -i` | Memfilter response tanpa memperhatikan huruf besar/kecil |
| `"whatsapp.com"` | String target yang dicari dalam response |

### Dokumentasi

![Enumeration menggunakan curl](images/02-enumeration-command.png)

> Ganti dengan screenshot saat command enumeration dijalankan.

---

## 7. Memahami Alur Command

Command tersebut bekerja dengan alur berikut:

```text
ID 1
  │
  ▼
curl request
  │
  ▼
HTML response
  │
  ▼
grep "whatsapp.com"
  │
  ├── Tidak ditemukan → lanjut ID berikutnya
  │
  └── Ditemukan → tampilkan hasil
```

Kemudian proses diulang:

```text
ID 1  ──→ request ──→ filter
ID 2  ──→ request ──→ filter
ID 3  ──→ request ──→ filter
...
ID 50 ──→ request ──→ filter
```

Dengan pendekatan ini saya tidak perlu membaca seluruh response HTML dari setiap object secara manual.

---

## 8. Fungsi `curl -s`

Command:

```bash
curl -s "http://localhost:8080/ujiandata/?id=1"
```

mengirim HTTP request ke target.

Opsi:

```text
-s
```

berarti:

```text
silent
```

Opsi ini menyembunyikan progress meter milik cURL sehingga output lebih mudah diteruskan ke command lain seperti `grep`.

Contoh alurnya:

```text
curl
 │
 ▼
HTTP Response
 │
 ▼
stdout
```

Output tersebut kemudian diteruskan menggunakan pipe (`|`).

---

## 9. Fungsi Pipe `|`

Pada command:

```bash
curl -s "URL" | grep -i "whatsapp.com"
```

karakter:

```text
|
```

disebut **pipe**.

Pipe mengirim output command di sebelah kiri menjadi input command di sebelah kanan.

```text
curl
 │
 │ HTML response
 ▼
 |
 ▼
grep
 │
 ▼
Filtered output
```

Konsep ini sangat berguna ketika bekerja dengan command-line tools di Linux.

---

## 10. Fungsi `grep`

Command:

```bash
grep -i "whatsapp.com"
```

mencari string:

```text
whatsapp.com
```

pada input yang diterimanya.

Opsi:

```text
-i
```

berarti pencarian bersifat **case-insensitive**.

Sehingga variasi seperti:

```text
whatsapp.com
WhatsApp.com
WHATSAPP.COM
```

dapat tetap cocok.

Pada lab ini, `grep` membantu menampilkan hanya response yang relevan dengan tujuan pengujian.

---

## 11. Mengapa Tidak Membaca Semua HTML?

Tanpa filter, satu request dapat menghasilkan response seperti:

```html
<html>
<head>...</head>
<body>
    ... banyak content ...
</body>
</html>
```

Jika dilakukan terhadap 50 ID, output akan sangat banyak.

Dengan:

```bash
grep -i "whatsapp.com"
```

saya hanya meminta terminal menampilkan bagian response yang mengandung indikator yang dicari.

Secara konseptual:

```text
Full HTML Response
      │
      ▼
     grep
      │
      ├── Tidak relevan → dibuang
      │
      └── whatsapp.com → ditampilkan
```

Ini merupakan contoh **data extraction terarah** dari response HTTP.

---

## 12. Hasil Pengujian

Dari proses enumeration ID `1` sampai `50`, terdapat object yang menghasilkan response berisi string:

```text
whatsapp.com
```

Object tersebut menjadi jawaban atau informasi yang dibutuhkan untuk melanjutkan test berikutnya.

### Dokumentasi

![Hasil enumeration](images/03-enumeration-result.png)

> Ganti dengan screenshot output command yang menunjukkan ID yang menghasilkan link WhatsApp.

![Response object](images/04-object-response.png)

> Ganti dengan screenshot response lengkap object yang ditemukan. Sensor invite code jika dokumentasi dipublikasikan.

---

## 13. Konsep IDOR

Kerentanan yang dipelajari pada Lab 06 berkaitan dengan:

```text
IDOR
```

atau:

```text
Insecure Direct Object Reference
```

Secara umum IDOR terjadi ketika aplikasi mengekspos identifier object kepada client dan tidak melakukan authorization yang memadai ketika identifier tersebut diubah.

Contoh:

```text
/ujiandata/?id=10
```

kemudian user dapat menggantinya menjadi:

```text
/ujiandata/?id=11
/ujiandata/?id=12
/ujiandata/?id=13
```

Jika server mengembalikan object tersebut tanpa memverifikasi hak akses user, terdapat masalah pada **object-level authorization**.

---

## 14. Enumeration vs IDOR

Kedua istilah ini perlu dibedakan.

### Enumeration

Enumeration adalah proses mencari atau memetakan object yang tersedia.

Contoh:

```text
id=1
id=2
id=3
...
```

Enumeration sendiri merupakan **teknik pengujian**.

### IDOR

IDOR adalah **kerentanan authorization**.

Masalah terjadi jika object yang ditemukan melalui enumeration dapat diakses meskipun user tidak seharusnya memiliki izin terhadap object tersebut.

Sehingga:

```text
Enumeration
     │
     ▼
Menemukan object
     │
     ▼
Authorization diuji
     │
     ├── Access ditolak → kontrol bekerja
     │
     └── Object diberikan tanpa izin → IDOR
```

---

## 15. Sequential ID

Pada lab ini identifier berbentuk angka berurutan:

```text
1
2
3
4
...
50
```

Identifier seperti ini disebut mudah untuk dienumerasi karena nilai berikutnya dapat diperkirakan.

Namun hal penting yang perlu dipahami adalah:

> Sequential ID bukan akar utama IDOR.

Walaupun ID diganti dengan UUID atau identifier acak, server tetap harus melakukan authorization.

Contoh:

```text
?id=550e8400-e29b-41d4-a716-446655440000
```

Jika user yang tidak berhak tetap dapat mengakses object tersebut setelah mengetahui identifier-nya, masalah authorization masih tetap ada.

---

## 16. Authentication vs Authorization

Seperti pada Lab 03, konsep ini sangat penting.

### Authentication

Menjawab:

```text
Siapa user ini?
```

### Authorization

Menjawab:

```text
Apakah user ini boleh mengakses object tersebut?
```

Alur yang benar:

```text
Request ?id=20
      │
      ▼
Authenticated User
      │
      ▼
Load Object 20
      │
      ▼
Authorization Check
      │
      ├── Allowed → Return Data
      │
      └── Denied  → Reject Request
```

---

## 17. Root Cause

Akar masalah IDOR biasanya terjadi ketika aplikasi hanya mencari object berdasarkan identifier:

```text
id = request["id"]
object = database.find(id)
return object
```

tanpa memastikan object tersebut dapat diakses oleh current user.

Yang seharusnya dilakukan secara konseptual:

```text
id = request["id"]
object = database.find(id)

if currentUser.canAccess(object):
    return object
else:
    deny
```

Authorization harus dilakukan di sisi server.

---

## 18. Mitigasi

Beberapa pendekatan untuk mencegah IDOR:

### 1. Object-Level Authorization

Setiap request terhadap object harus divalidasi berdasarkan user yang sedang login.

```text
Current User
     │
     ▼
Requested Object
     │
     ▼
Permission Check
     │
     ├── Allowed → Return
     └── Denied  → Reject
```

### 2. Scope Query Berdasarkan User

Daripada:

```text
SELECT * FROM data WHERE id = ?
```

secara konsep aplikasi dapat membatasi query berdasarkan owner atau scope user:

```text
SELECT * FROM data
WHERE id = ?
AND owner_id = ?
```

Implementasi sebenarnya bergantung pada kebutuhan bisnis dan role aplikasi.

### 3. Gunakan Identifier Tidak Mudah Ditebak sebagai Defense-in-Depth

UUID atau identifier acak dapat mengurangi kemudahan enumeration, tetapi **tidak menggantikan authorization**.

### 4. Logging dan Monitoring

Request berurutan seperti:

```text
?id=1
?id=2
?id=3
?id=4
...
```

dalam waktu singkat dapat menjadi sinyal aktivitas enumeration dan dapat dicatat untuk monitoring.

---

## 19. Finding

### IDOR / Broken Object Level Authorization

| Komponen | Detail |
|---|---|
| Endpoint | `/ujiandata/` |
| Parameter | `id` |
| Teknik | Sequential Object Enumeration |
| Range Lab | `1..50` |
| Tool | `curl`, `grep`, Bash |
| Indicator | `whatsapp.com` |
| Dampak Lab | Object lain dapat ditemukan dengan mengganti identifier |
| Root Cause | Authorization object tidak memadai |
| Main Defense | Server-side object authorization |

---

## 20. Perjalanan Penyelesaian Lab

```text
1. Membuka /ujiandata
          │
          ▼
2. Mengidentifikasi parameter ?id=
          │
          ▼
3. Mengamati ID berbentuk angka
          │
          ▼
4. Menentukan range 1 sampai 50
          │
          ▼
5. Membuat Bash loop
          │
          ▼
6. Mengirim request dengan curl
          │
          ▼
7. Memfilter response dengan grep
          │
          ▼
8. Mencari string whatsapp.com
          │
          ▼
9. Menemukan object yang sesuai
          │
          ▼
10. Menggunakan informasi tersebut
    untuk melanjutkan test berikutnya
```

---

## 21. Hubungan dengan Lab 03

Lab 03 dan Lab 06 sama-sama berkaitan dengan IDOR, tetapi pendekatan pengujiannya berbeda.

| Aspek | Lab 03 | Lab 06 |
|---|---|---|
| Vulnerability | IDOR | IDOR / Object Enumeration |
| Endpoint | `/profile.php?id=` | `/ujiandata/?id=` |
| Petunjuk ID | Diberikan (`id=4`) | Dicari melalui enumeration |
| Automation | Tidak diperlukan | Bash loop |
| Filtering | Manual | `grep` |
| Fokus tambahan | Authentication vs Authorization | Enumeration dan response filtering |

Pada Lab 03 saya sudah mengetahui object yang harus diakses.

Pada Lab 06 saya harus **mencari object tersebut terlebih dahulu**.

---

## 22. Checklist Pembelajaran

Setelah menyelesaikan Lab 06, saya harus dapat menjelaskan:

- [ ] Apa fungsi parameter `id`
- [ ] Apa itu enumeration
- [ ] Apa itu sequential identifier
- [ ] Apa fungsi Bash `for` loop
- [ ] Apa fungsi `curl -s`
- [ ] Apa fungsi pipe `|`
- [ ] Apa fungsi `grep -i`
- [ ] Apa perbedaan enumeration dengan IDOR
- [ ] Apa itu object-level authorization
- [ ] Mengapa sequential ID bukan akar utama IDOR
- [ ] Mengapa UUID tidak menggantikan authorization
- [ ] Bagaimana developer mencegah IDOR

---

## 23. Latihan Pemahaman

### Pertanyaan 1

Apa yang dilakukan oleh command berikut?

```bash
for i in {1..50}; do
    curl -s "http://localhost:8080/ujiandata/?id=$i"
done
```

### Pertanyaan 2

Apa fungsi pipe pada command:

```bash
curl -s "URL" | grep "whatsapp.com"
```

### Pertanyaan 3

Mengapa menemukan `id=20` tidak otomatis membuktikan IDOR?

### Pertanyaan 4

Apa perbedaan antara:

```text
Enumeration
```

dan:

```text
Broken Authorization
```

### Pertanyaan 5

Jika server mengganti seluruh ID menjadi UUID, apakah IDOR otomatis hilang? Jelaskan alasannya.

---

## 24. Kesimpulan

Pada Lab 06 saya mempelajari cara melakukan enumeration terhadap parameter:

```text
?id=
```

dengan menguji ID `1` sampai `50` secara otomatis.

Command utama yang digunakan:

```bash
for i in {1..50}; do echo -n "ID $i: "; curl -s "http://localhost:8080/ujiandata/?id=$i" | grep -i "whatsapp.com"; done
```

Command tersebut menggabungkan tiga konsep penting:

```text
Bash Loop
   │
   ▼
curl
   │
   ▼
HTTP Response
   │
   ▼
grep
   │
   ▼
Relevant Data
```

Dari latihan ini saya memahami bahwa enumeration hanyalah cara untuk menemukan object. Temuan keamanan yang sebenarnya berkaitan dengan apakah aplikasi melakukan **authorization pada setiap object yang diminta**.

Pelajaran utama:

> Mengetahui atau menebak identifier sebuah object tidak seharusnya memberikan hak akses terhadap object tersebut.

---

## 25. Cheat Sheet — Lab 06

```text
LAB           : 06
PATH          : /ujiandata
CATEGORY      : Broken Access Control
VULNERABILITY : IDOR / Object Enumeration
PARAMETER     : id
RANGE         : 1..50
TOOLS         : curl, grep, Bash
FILTER        : whatsapp.com
TECHNIQUE     : Sequential Enumeration
ROOT CAUSE    : Missing / insufficient object authorization
MAIN DEFENSE  : Server-side object-level authorization
```

Command utama:

```bash
for i in {1..50}; do echo -n "ID $i: "; curl -s "http://localhost:8080/ujiandata/?id=$i" | grep -i "whatsapp.com"; done
```

Mental model:

```text
ID Enumeration
      │
      ▼
Find Object
      │
      ▼
Authorization Check
      │
      ├── Allowed → valid access
      │
      └── Missing → potential IDOR
```

---

## Screenshot Checklist

Simpan screenshot dengan nama berikut di folder `images/`:

```text
images/
├── 01-target-page.png
├── 02-enumeration-command.png
├── 03-enumeration-result.png
└── 04-object-response.png
```

Setelah screenshot asli dimasukkan, Markdown Preview di VS Code akan otomatis menampilkannya.
