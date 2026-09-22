# Pentest Learning Notes

## Lab 03 — IDOR (Insecure Direct Object Reference) / Broken Access Control

> **Catatan:** Pengujian ini dilakukan pada environment lab/VPS yang memang disediakan dan diizinkan untuk latihan penetration testing. Teknik dalam dokumentasi ini hanya digunakan pada sistem yang memiliki izin untuk diuji.

---

# 1. Tujuan Pembelajaran

Pada Lab 03, target berada pada:

```text
http://192.168.39.87/ujianlemari
```

Tugas utama adalah menemukan informasi yang mengarahkan ke test berikutnya.

Pada latihan ini saya mempelajari:

* Registration
* Login dan authenticated session
* User profile
* URL parameter
* Object identifier
* Horizontal access control
* IDOR (Insecure Direct Object Reference)
* Broken Access Control
* Mengapa ID pada URL tidak boleh dianggap sebagai authorization
* Perbedaan authentication dan authorization

---

# 2. Environment Lab

Target menggunakan IP yang sama dengan lab sebelumnya:

```text
192.168.39.87
```

Path target:

```text
/ujianlemari
```

Sehingga:

```text
http://192.168.39.87/ujianlemari
```

Target berada pada jaringan internal lab.

Secara konseptual:

```text
Laptop
   │
   │ SSH / Tunnel
   ▼
VPS
   │
   │ Internal Network
   ▼
192.168.39.87
   │
   └── /ujianlemari
```

---

# 3. Petunjuk Lab

Pada soal terdapat petunjuk:

```text
Gunakan w3m.
apt install w3m

Untuk mengubah URL:
Shift + U

Link WhatsApp berada pada ID nomor 4.
```

Petunjuk penting dari soal adalah:

```text
ID = 4
```

Artinya terdapat kemungkinan bahwa aplikasi menggunakan **identifier pada URL** untuk menentukan data/profile yang akan ditampilkan.

Hal tersebut menjadi fokus utama pengujian.

---

# 4. Menggunakan w3m

Jika `w3m` belum tersedia pada environment lab, instalasi pada sistem Debian/Ubuntu dapat dilakukan dengan:

```bash
sudo apt update
sudo apt install w3m
```

Kemudian target dapat dibuka menggunakan:

```bash
w3m http://192.168.39.87/ujianlemari
```

Pada `w3m`, shortcut:

```text
Shift + U
```

dapat digunakan untuk memasukkan URL.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 01]**

> Tampilan awal `/ujianlemari` menggunakan w3m.

---

# 5. Identifikasi Registration Form

Pada halaman ditemukan form:

```html
<form method="post">
    Name:
    <input type="text" name="name"><br>

    Email:
    <input type="text" name="email"><br>

    Password:
    <input type="password" name="password"><br>

    <input type="submit"
           name="register"
           value="Register">
</form>
```

Dari HTML tersebut diketahui bahwa aplikasi menyediakan proses registrasi dengan tiga input:

```text
name
email
password
```

Form menggunakan:

```html
method="post"
```

sehingga data registrasi dikirim melalui HTTP POST.

Secara sederhana:

```text
User
 │
 ├── Name
 ├── Email
 └── Password
       │
       ▼
    HTTP POST
       │
       ▼
Application
       │
       ▼
   User dibuat
```

---

# 6. Membuat User

Langkah pertama yang saya lakukan adalah membuat user baru melalui registration form.

Contoh:

```text
Name     : testuser
Email    : test@example.local
Password : ********
```

Setelah registrasi berhasil, user tersebut dapat digunakan untuk login.

Alurnya:

```text
Registration
     │
     ▼
User Created
     │
     ▼
Login
     │
     ▼
Authenticated Session
```

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 02]**

> Registration form sebelum membuat user.

**[PLACEHOLDER SCREENSHOT 03]**

> Response setelah registration berhasil.

---

# 7. Login

Setelah membuat user, langkah berikutnya adalah login menggunakan account yang baru diregistrasikan.

```text
Registered User
      │
      ▼
Login
      │
      ▼
Credential Validation
      │
      ▼
Authenticated
```

Setelah login berhasil, aplikasi memberikan akses menuju halaman profile.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 04]**

> Login menggunakan user yang baru dibuat.

---

# 8. Membuka Profile

Setelah login, saya membuka halaman profile user.

Pada tahap ini saya menemukan URL dengan pola:

```text
http://localhost:8080/ujianlemari/profile.php?id=17028
```

Bagian penting dari URL tersebut adalah:

```text
profile.php?id=17028
```

URL dapat dipecah menjadi:

```text
profile.php
    │
    └── halaman profile

?id=
    │
    └── query parameter

17028
    │
    └── identifier object/user
```

Dengan demikian aplikasi tampaknya menentukan profile yang akan ditampilkan berdasarkan parameter:

```text
id
```

---

# 9. Analisis Parameter `id`

Profile milik user yang saya buat memiliki ID:

```text
17028
```

Sehingga request:

```text
/profile.php?id=17028
```

secara konseptual berarti:

```text
"Tampilkan profile dengan ID 17028"
```

Alurnya kemungkinan seperti:

```text
Browser
   │
   │ ?id=17028
   ▼
profile.php
   │
   ▼
Cari object/user
dengan ID 17028
   │
   ▼
Tampilkan Profile
```

Di sini muncul pertanyaan keamanan yang penting:

> Apakah aplikasi hanya memeriksa bahwa saya sudah login, atau juga memeriksa apakah saya berhak mengakses profile dengan ID yang diminta?

Pertanyaan tersebut berkaitan dengan **authorization**.

---

# 10. Authentication vs Authorization

Dua konsep ini penting untuk dibedakan.

### Authentication

Authentication menjawab:

```text
"Siapa Anda?"
```

Contohnya:

```text
Username + Password
        │
        ▼
      Login
        │
        ▼
Authenticated User
```

### Authorization

Authorization menjawab:

```text
"Apa yang boleh Anda akses?"
```

Contohnya:

```text
User A
  │
  ├── Profile A → ALLOWED
  │
  └── Profile B → DENIED
```

Jadi:

```text
Authentication
      ≠
Authorization
```

User yang berhasil login belum tentu boleh mengakses seluruh object yang ada di aplikasi.

---

# 11. Mengikuti Petunjuk Lab

Petunjuk sebelumnya mengatakan:

```text
Link WA ada di ID nomor 4
```

Sedangkan profile saya menggunakan:

```text
/profile.php?id=17028
```

Karena object ditentukan melalui parameter:

```text
?id=
```

saya mengganti:

```text
17028
```

menjadi:

```text
4
```

Sehingga request menjadi:

```text
http://localhost:8080/ujianlemari/profile.php?id=4
```

Secara sederhana:

```text
SEBELUM

profile.php?id=17028
               │
               └── profile saya


SESUDAH

profile.php?id=4
               │
               └── object ID 4
```

Pada `w3m`, URL dapat diganti menggunakan:

```text
Shift + U
```

kemudian memasukkan URL dengan ID yang baru.

---

# 12. Hasil Pengujian

Setelah parameter:

```text
id=17028
```

diubah menjadi:

```text
id=4
```

aplikasi memberikan response berisi profile lain.

Response yang ditemukan:

```html
Name: virtual<br>
Email: https://chat.whatsapp.com/Loip***<br>
```

Informasi tersebut berisi link yang dibutuhkan untuk melanjutkan ke test berikutnya.

> Pada dokumentasi yang akan dibagikan secara publik, token/invite code sebaiknya tetap disensor.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 05]**

> Profile milik user sendiri dengan URL `profile.php?id=17028`.

**[PLACEHOLDER SCREENSHOT 06]**

> Proses mengubah URL menggunakan `Shift + U` pada w3m.

**[PLACEHOLDER SCREENSHOT 07]**

> URL setelah parameter diubah menjadi `profile.php?id=4`.

**[PLACEHOLDER SCREENSHOT 08]**

> Response profile ID 4 yang berisi informasi untuk melanjutkan lab.

---

# 13. Apa yang Sebenarnya Terjadi?

Alur normal:

```text
Login sebagai User A
        │
        ▼
Profile User A
        │
        ▼
?id=17028
```

Tetapi aplikasi juga menerima:

```text
?id=4
```

dan mengembalikan object tersebut.

Secara konseptual:

```text
Authenticated User
       │
       │ request
       ▼
profile.php?id=4
       │
       ▼
Server mencari
object ID 4
       │
       ▼
Apakah user berhak
mengakses object 4?
       │
       ├── Seharusnya diperiksa
       │
       ▼
Data Object 4
```

Jika aplikasi langsung memberikan object berdasarkan ID tanpa memastikan ownership atau permission user, terdapat masalah pada **object-level authorization**.

---

# 14. IDOR

Kerentanan seperti ini secara tradisional dikenal sebagai:

```text
IDOR
```

atau:

```text
Insecure Direct Object Reference
```

Konsep dasarnya adalah aplikasi mengekspos identifier suatu object, misalnya:

```text
?id=17028
```

dan identifier tersebut dapat dimodifikasi:

```text
?id=4
```

untuk mengakses object lain tanpa pemeriksaan authorization yang semestinya.

Object tersebut tidak harus berupa user.

Contohnya dapat berupa:

```text
/profile.php?id=4

/invoice.php?id=100

/document.php?id=50

/order.php?id=900

/message.php?id=25
```

Masalah utamanya bukan karena menggunakan angka sebagai ID.

Masalah utamanya adalah:

```text
Server tidak memastikan
user berhak mengakses
object yang diminta.
```

---

# 15. IDOR dan Broken Access Control

IDOR termasuk dalam keluarga masalah:

```text
Broken Access Control
```

Mental model sederhananya:

```text
User A
 │
 │ authenticated
 ▼
Application
 │
 │ meminta object A
 ▼
ALLOWED
```

Yang seharusnya terjadi ketika User A meminta object yang tidak diizinkan:

```text
User A
 │
 │ meminta object B
 ▼
Application
 │
 ▼
Authorization Check
 │
 └── NOT ALLOWED
        │
        ▼
       DENY
```

Pada aplikasi yang rentan:

```text
User A
 │
 │ mengganti ID
 ▼
?id=B
 │
 ▼
Application
 │
 │ object ditemukan
 ▼
Object B diberikan
```

Authorization check yang seharusnya melindungi object tidak diterapkan dengan benar.

---

# 16. Mengapa ID Acak Tidak Menyelesaikan Masalah?

Pada lab, ID user saya adalah:

```text
17028
```

sedangkan object tujuan:

```text
4
```

Menggunakan ID yang sulit ditebak dapat mengurangi kemungkinan object ditemukan secara kebetulan, tetapi **bukan pengganti authorization**.

Misalnya aplikasi menggunakan identifier yang sangat panjang:

```text
/profile?id=a8f4e9c2...
```

server tetap harus memeriksa:

```text
Apakah authenticated user
memiliki izin untuk membaca
object tersebut?
```

Keamanan tidak boleh bergantung hanya pada:

```text
"ID sulit ditebak."
```

---

# 17. Root Cause

Akar masalah pada IDOR adalah kegagalan melakukan authorization pada object yang diminta.

Pola yang tidak aman secara konseptual:

```text
id = request["id"]

profile = database.find(id)

return profile
```

Aplikasi hanya melakukan:

```text
Cari berdasarkan ID
```

tanpa melakukan:

```text
Apakah current user
boleh membaca ID ini?
```

Implementasi yang aman membutuhkan:

```text
Request
   │
   ▼
Authenticated User
   │
   ▼
Requested Object
   │
   ▼
Authorization Check
   │
   ├── Allowed ──→ Return Object
   │
   └── Denied ───→ Reject Request
```

---

# 18. Mitigasi

Pertahanan utama bukan menyembunyikan parameter `id`.

Server harus melakukan **authorization check pada setiap object**.

Contohnya secara konseptual:

```text
Requested Profile
       │
       ▼
Get Current User
       │
       ▼
Check Permission
       │
       ├── Authorized
       │      ↓
       │   Return Data
       │
       └── Unauthorized
              ↓
             DENY
```

Beberapa prinsip mitigasi:

* Terapkan authorization pada server side.
* Jangan menganggap user boleh mengakses object hanya karena mengetahui ID-nya.
* Validasi ownership object.
* Terapkan role/permission sesuai kebutuhan aplikasi.
* Terapkan prinsip least privilege.
* Hindari mengandalkan hidden field atau URL sebagai security control.
* Gunakan indirect/random identifier jika sesuai sebagai defense-in-depth, tetapi tetap lakukan authorization.
* Catat percobaan akses object yang tidak diizinkan.

---

# 19. Finding

**Finding**

```text
IDOR / Broken Access Control
```

**Target**

```text
/ujianlemari
```

**Endpoint terdampak**

```text
/profile.php
```

**Parameter**

```text
id
```

**Profile user sendiri**

```text
/profile.php?id=17028
```

**Object yang diuji sesuai petunjuk lab**

```text
/profile.php?id=4
```

**Observed Response**

```text
Name: virtual
Email: https://chat.whatsapp.com/Loip***
```

**Impact pada Lab**

Authenticated user dapat mengakses object/profile lain dengan mengubah identifier pada URL.

---

# 20. Perjalanan Penyelesaian Lab

Langkah yang saya lakukan:

```text
1. Membuka /ujianlemari
          │
          ▼
2. Menemukan Registration Form
          │
          ▼
3. Membuat user baru
          │
          ▼
4. Login menggunakan user tersebut
          │
          ▼
5. Membuka Profile
          │
          ▼
6. Menemukan:
   profile.php?id=17028
          │
          ▼
7. Menyadari profile menggunakan
   parameter "id"
          │
          ▼
8. Mengingat petunjuk:
   "link WA ada di ID 4"
          │
          ▼
9. Mengubah:
   id=17028 → id=4
          │
          ▼
10. Server menampilkan object ID 4
          │
          ▼
11. Mendapatkan informasi untuk
    melanjutkan test berikutnya
```

---

# 21. Hal Penting yang Saya Pelajari

Awalnya saya hanya melihat:

```text
profile.php?id=17028
```

sebagai URL profile biasa.

Tetapi parameter:

```text
?id=17028
```

sebenarnya memberikan informasi penting mengenai bagaimana aplikasi menentukan object.

Ketika parameter berubah:

```text
?id=17028
     ↓
?id=4
```

dan server memberikan data object lain, saya belajar bahwa **identifier yang berasal dari client tidak boleh digunakan sebagai dasar authorization**.

Hal yang perlu saya tanyakan ketika melihat parameter object seperti:

```text
?id=
?user_id=
?account=
?document=
?order=
```

bukan hanya:

```text
"Apakah ID tersebut bisa diganti?"
```

tetapi:

```text
"Jika ID diganti, apakah server
memastikan saya memiliki izin
terhadap object tersebut?"
```

Itulah inti dari pengujian IDOR.

---

# 22. Hubungan dengan Lab Sebelumnya

## Lab 00

**SSH / Tunneling**

Pertanyaan utama:

```text
Bagaimana saya mencapai target?
```

```text
Laptop
  ↓
VPS
  ↓
Internal Network
```

## Lab 01

**Path Traversal / LFI**

Pertanyaan utama:

```text
Bisakah input memengaruhi
file yang dibaca server?
```

```text
?page=
   ↓
../
   ↓
Filesystem
```

## Lab 02

**SQL Injection**

Pertanyaan utama:

```text
Bisakah input memengaruhi
SQL query?
```

```text
Login Input
    ↓
SQL Query
    ↓
Database
```

## Lab 03

**IDOR / Broken Access Control**

Pertanyaan utama:

```text
Apakah saya berhak
mengakses object ini?
```

```text
Authenticated User
       ↓
?id=17028
       ↓
ubah ID
       ↓
?id=4
       ↓
Authorization?
```

Sehingga progres pembelajaran saya:

```text
Lab 00 → Network Access
             │
Lab 01 → Filesystem Security
             │
Lab 02 → Database / Input Security
             │
Lab 03 → Authorization / Access Control
```

---

# 23. Checklist Pemahaman Lab 03

Setelah menyelesaikan lab ini, saya harus dapat menjelaskan:

* [ ] Apa itu authentication?
* [ ] Apa itu authorization?
* [ ] Apa perbedaan authentication dan authorization?
* [ ] Apa fungsi parameter `id`?
* [ ] Apa yang dimaksud object dalam aplikasi?
* [ ] Apa itu IDOR?
* [ ] Mengapa IDOR termasuk Broken Access Control?
* [ ] Mengapa berhasil login tidak berarti boleh mengakses semua data?
* [ ] Mengapa mengganti ID dapat menjadi security test?
* [ ] Mengapa random ID bukan pengganti authorization?
* [ ] Di mana authorization seharusnya dilakukan?
* [ ] Bagaimana developer mencegah IDOR?

---

# 24. Latihan Pemahaman

### Pertanyaan 1

Jika saya login sebagai:

```text
User A
```

kemudian profile saya adalah:

```text
/profile.php?id=100
```

apa masalah keamanannya jika:

```text
/profile.php?id=101
```

menampilkan profile User B tanpa permission check?

### Pertanyaan 2

Jelaskan perbedaan:

```text
Authentication
```

dan:

```text
Authorization
```

dengan kata-kata sendiri.

### Pertanyaan 3

Apakah mengganti:

```text
?id=100
```

menjadi UUID panjang otomatis memperbaiki IDOR?

Jelaskan alasannya.

### Pertanyaan 4

Di mana seharusnya permission diperiksa?

```text
Browser?
Frontend?
Server?
Database?
```

Jelaskan alasannya.

### Pertanyaan 5

Apa pertanyaan pertama yang perlu muncul ketika menemukan URL seperti:

```text
/invoice.php?id=500
```

atau:

```text
/document.php?id=123
```

dalam sebuah authorized pentest?

---

# 25. Kesimpulan

Pada Lab 03 saya memulai dengan membuat user baru:

```text
Register
   ↓
Login
   ↓
Profile
```

Setelah membuka profile, saya menemukan pola URL:

```text
/profile.php?id=17028
```

Hal tersebut menunjukkan bahwa aplikasi menggunakan parameter:

```text
id
```

untuk menentukan object/profile yang akan ditampilkan.

Berdasarkan petunjuk lab bahwa informasi berada pada:

```text
ID 4
```

saya mengubah:

```text
/profile.php?id=17028
```

menjadi:

```text
/profile.php?id=4
```

Server kemudian memberikan response object tersebut:

```text
Name: virtual
Email: https://chat.whatsapp.com/Loip***
```

Dari latihan ini saya memahami bahwa masalah utamanya bukan sekadar:

```text
"ID bisa diganti."
```

Tetapi:

```text
Authenticated User
       │
       ▼
Meminta Object
       │
       ▼
Apakah server melakukan
authorization?
       │
       ├── YES → sesuai permission
       │
       └── NO  → potensi IDOR
```

Pelajaran utama Lab 03 adalah:

> **Authentication membuktikan siapa user, sedangkan authorization menentukan resource apa yang boleh diakses user tersebut.**

---

# 26. Cheat Sheet Lab 03

```text
LAB            : Lab 03
PATH           : /ujianlemari
CATEGORY       : Broken Access Control
VULNERABILITY  : IDOR
ENDPOINT       : /profile.php
PARAMETER      : id
OWN PROFILE    : id=17028
TARGET LAB     : id=4
REQUIREMENT    : Authenticated User
IMPACT         : Access to another object's data
ROOT CAUSE     : Missing / insufficient object authorization
MAIN DEFENSE   : Server-side authorization check
```

Mental model:

```text
LOGIN
  │
  ▼
Authentication
  │
  │ "Siapa saya?"
  ▼
Authenticated User
  │
  │ Request ?id=4
  ▼
Authorization
  │
  │ "Apakah saya boleh
  │  mengakses object 4?"
  │
  ├──── YES ────→ Return Object
  │
  └──── NO ─────→ Deny
```

**Inti yang harus saya ingat:**

```text
Mengetahui Object ID
        ≠
Memiliki Hak Akses
```
