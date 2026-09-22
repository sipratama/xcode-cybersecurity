# Pentest Learning Notes

## Lab 02 — SQL Injection: Authentication Bypass

> **Catatan:** Pengujian ini dilakukan pada environment lab/VPS yang memang disediakan dan diizinkan untuk latihan penetration testing. Teknik pada dokumentasi ini hanya digunakan pada sistem yang memiliki izin untuk diuji.

---

# 1. Tujuan Pembelajaran

Pada Lab 02, tugas yang diberikan adalah melakukan **login** ke aplikasi:

```text
http://192.168.39.87/ujianmiegoreng
```

Pada latihan ini saya mempelajari:

* Cara kerja login form
* HTTP POST pada proses login
* Input `username` dan `password`
* Interaksi aplikasi dengan database
* Dasar SQL query
* SQL Injection
* Authentication Bypass
* Boolean condition pada SQL
* Mengapa kondisi `'1'='1'` selalu bernilai TRUE
* Penyebab SQL Injection
* Cara mencegah SQL Injection

---

# 2. Environment Lab

Target masih menggunakan IP yang sama seperti Lab 01:

```text
192.168.39.87
```

Namun path aplikasi berubah menjadi:

```text
/ujianmiegoreng
```

Sehingga target lengkapnya:

```text
http://192.168.39.87/ujianmiegoreng
```

Karena target berada pada jaringan internal lab, akses dilakukan melalui VPS yang telah dibahas pada **Lab 00 — PuTTY, SSH, dan SSH Tunneling**.

Secara sederhana:

```text
Laptop
   │
   │ SSH
   ▼
VPS / Lab Server
   │
   │ HTTP
   ▼
192.168.39.87
   │
   └── /ujianmiegoreng
```

Halaman dapat diakses dari VPS menggunakan browser berbasis terminal sesuai environment lab.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 01]**

> Halaman `/ujianmiegoreng` ketika pertama kali dibuka.

---

# 3. Identifikasi Login Form

Pada halaman target terdapat form login.

HTML yang ditemukan:

```html
<h2>Login</h2>

<form method="post">
    Username:
    <input type="text" name="username" required><br>

    Password:
    <input type="password" name="password" required><br>

    <button type="submit" name="login">
        Login
    </button>
</form>
```

Dari HTML tersebut terdapat dua input utama:

```text
username
password
```

Form menggunakan:

```html
method="post"
```

Artinya data login dikirim menggunakan HTTP:

```text
POST
```

Alurnya:

```text
User
 │
 ├── username
 │
 └── password
       │
       ▼
    HTTP POST
       │
       ▼
   Web Application
       │
       ▼
     Database
```

---

# 4. Cara Kerja Login Secara Normal

Secara konseptual, aplikasi login biasanya menerima:

```text
username
password
```

kemudian membandingkannya dengan data yang terdapat pada database.

Sebagai ilustrasi pembelajaran, backend yang tidak aman mungkin membentuk query seperti:

```sql
SELECT *
FROM users
WHERE username = '<username>'
AND password = '<password>';
```

Misalnya pengguna memasukkan:

```text
Username : singgih
Password : password123
```

maka secara konseptual query dapat menjadi:

```sql
SELECT *
FROM users
WHERE username = 'singgih'
AND password = 'password123';
```

Database kemudian memeriksa:

```text
Apakah username cocok?
        │
        ▼
Apakah password cocok?
        │
        ▼
       YES
        │
        ▼
Login berhasil
```

Jika salah satu tidak cocok:

```text
FALSE
  │
  ▼
Login gagal
```

> Query di atas merupakan ilustrasi untuk memahami vulnerability. Implementasi asli aplikasi belum tentu menggunakan query yang persis sama.

---

# 5. Identifikasi Kemungkinan SQL Injection

Pada pengujian ditemukan bahwa input login dapat dimanipulasi menggunakan karakter dan operator SQL.

Payload yang digunakan pada lab:

```text
' OR '1'='1
```

Payload tersebut berhasil menyebabkan proses login dapat dilewati tanpa mengetahui credential normal.

Temuan ini mengarah pada:

```text
SQL Injection
      │
      ▼
Authentication Bypass
```

---

# 6. Payload yang Digunakan

Payload:

```text
' OR '1'='1
```

Bagian pentingnya dapat dipahami sebagai:

```text
'
│
└── menutup string

OR
│
└── operator logika

'1'='1'
│
└── kondisi yang selalu TRUE
```

Bagian:

```sql
'1'='1'
```

membandingkan nilai:

```text
1
```

dengan:

```text
1
```

Sehingga:

```text
'1' = '1'
     ↓
    TRUE
```

Inilah konsep **always-true condition** atau **tautology** yang sering digunakan untuk mendemonstrasikan SQL Injection pada lab login yang sengaja dibuat rentan.

---

# 7. Memahami Operator OR

Dalam SQL terdapat operator logika seperti:

```text
AND
OR
NOT
```

Contoh:

```text
FALSE OR TRUE
```

hasilnya:

```text
TRUE
```

Sedangkan:

```text
FALSE AND TRUE
```

hasilnya:

```text
FALSE
```

Truth table sederhananya:

```text
A       B       A OR B
-----------------------
FALSE   FALSE   FALSE
FALSE   TRUE    TRUE
TRUE    FALSE   TRUE
TRUE    TRUE    TRUE
```

Artinya pada operator `OR`, cukup salah satu kondisi bernilai TRUE agar keseluruhan ekspresi dapat bernilai TRUE.

---

# 8. Bagaimana SQL Injection Terjadi?

Misalnya aplikasi memiliki implementasi yang tidak aman seperti:

```text
query =
"SELECT ... WHERE username = '" + username + "' ..."
```

Masalahnya adalah:

```text
Input pengguna
      │
      ▼
Digabung langsung
dengan SQL query
      │
      ▼
Input dapat memengaruhi
struktur SQL
```

Seharusnya input pengguna diperlakukan sebagai:

```text
DATA
```

tetapi pada aplikasi yang rentan, sebagian input dapat ditafsirkan database sebagai:

```text
SQL SYNTAX
```

Inilah inti dari SQL Injection.

---

# 9. Mengapa `' OR '1'='1` Menarik?

Input normal:

```text
singgih
```

hanya dianggap sebagai data.

Tetapi input:

```text
' OR '1'='1
```

mengandung karakter dan operator yang memiliki arti dalam SQL:

```text
'     → berkaitan dengan batas string
OR    → operator boolean
=     → comparison
```

Akibatnya, apabila backend membangun SQL dengan **string concatenation tanpa parameterization**, input pengguna dapat mengubah struktur/logika query.

Secara konseptual:

```text
Input seharusnya
menjadi DATA
     │
     ▼
Tidak divalidasi /
query dibangun tidak aman
     │
     ▼
Sebagian input dianggap
sebagai SQL
     │
     ▼
Logika query berubah
```

---

# 10. Authentication Bypass

Tujuan tugas Lab 02 bukan mengambil data database, melainkan:

```text
LOGIN
```

Hasil pengujian menunjukkan bahwa autentikasi dapat dilewati menggunakan SQL Injection.

Karena itu finding pada lab ini dapat dikategorikan sebagai:

```text
SQL Injection
      │
      └── Authentication Bypass
```

Secara konseptual:

```text
Login Form
    │
    ▼
User Input
    │
    ▼
SQL Query
    │
    ▼
Boolean condition dimanipulasi
    │
    ▼
Authentication logic berubah
    │
    ▼
Login berhasil
```

---

# 11. Hasil Pengujian

Target:

```text
http://192.168.39.87/ujianmiegoreng
```

Endpoint menyediakan form login dengan:

```text
username
password
```

Payload yang digunakan pada environment lab:

```text
' OR '1'='1
```

Hasil:

```text
Authentication Bypass
        │
        ▼
Login berhasil
```

Hal tersebut menunjukkan bahwa input login memengaruhi logika query database sehingga proses autentikasi dapat dilewati.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 02]**

> Tampilan form login sebelum pengujian.

**[PLACEHOLDER SCREENSHOT 03]**

> Input payload `' OR '1'='1` pada field yang rentan.

**[PLACEHOLDER SCREENSHOT 04]**

> Halaman setelah authentication bypass berhasil.

---

# 12. Finding

**Finding**

```text
SQL Injection — Authentication Bypass
```

**Target**

```text
http://192.168.39.87/ujianmiegoreng
```

**Input terdampak**

```text
Login Form
```

**Payload Lab**

```text
' OR '1'='1
```

**Dampak pada Lab**

```text
Authentication dapat dilewati tanpa
mengetahui credential login yang valid.
```

---

# 13. Root Cause

Akar masalah SQL Injection pada umumnya adalah input pengguna dimasukkan ke dalam SQL query dengan cara yang memungkinkan input tersebut mengubah struktur query.

Pola berbahaya secara konseptual:

```text
SQL
 +
User Input
 +
SQL
```

atau:

```text
"SELECT ... '" + userInput + "' ..."
```

Akibatnya:

```text
User Input
    │
    ▼
SQL Parser
    │
    ├── Data
    │
    └── SQL Syntax
```

Database tidak lagi dapat membedakan secara aman mana bagian query yang dibuat developer dan mana data yang berasal dari pengguna.

---

# 14. Cara Mencegah SQL Injection

Pertahanan utama terhadap SQL Injection adalah menggunakan:

```text
Parameterized Query
```

atau:

```text
Prepared Statement
```

Konsepnya:

```text
SQL Structure
      │
      │ dipisahkan
      ▼
User Data
```

Misalnya secara konseptual:

```sql
SELECT *
FROM users
WHERE username = ?
AND password = ?;
```

Kemudian:

```text
? pertama → username
? kedua   → password
```

Dengan parameterization, input pengguna diperlakukan sebagai **nilai/data**, bukan digabungkan langsung menjadi struktur SQL.

Selain itu aplikasi sebaiknya:

* menggunakan library/database API yang mendukung parameterized queries;
* menghindari dynamic SQL berbasis string concatenation;
* menerapkan database permission seminimal mungkin;
* tidak menampilkan database error sensitif kepada pengguna;
* menyimpan password menggunakan password hashing yang sesuai, bukan plaintext;
* melakukan monitoring dan logging terhadap authentication failure dan aktivitas mencurigakan.

---

# 15. Hal Penting yang Saya Pelajari

Sebelum Lab 02, saya mungkin melihat:

```text
' OR '1'='1
```

hanya sebagai sebuah payload.

Setelah memahami mekanismenya:

```text
'
↓
memengaruhi batas string

OR
↓
operator boolean

'1'='1'
↓
TRUE
```

maka saya memahami bahwa inti SQL Injection bukanlah menghafalkan payload.

Hal yang sebenarnya terjadi adalah:

```text
Input pengguna
      ↓
masuk ke SQL query secara tidak aman
      ↓
input mengubah struktur/logika SQL
      ↓
database menjalankan query dengan
logika berbeda dari yang dimaksud developer
```

---

# 16. Hubungan Lab 00, Lab 01, dan Lab 02

### Lab 00 — SSH & Tunneling

Saya belajar:

```text
Bagaimana mencapai target?
```

Konsep:

```text
Laptop
   ↓
SSH / Tunnel
   ↓
VPS
   ↓
Internal Network
```

### Lab 01 — Path Traversal / LFI

Saya belajar:

```text
Bagaimana input memengaruhi filesystem?
```

Konsep:

```text
User Input
   ↓
?page=
   ↓
../../
   ↓
Filesystem
```

### Lab 02 — SQL Injection

Saya belajar:

```text
Bagaimana input memengaruhi database query?
```

Konsep:

```text
User Input
   ↓
Login Form
   ↓
SQL Query
   ↓
Database
```

Dengan demikian:

```text
Lab 00
Network Access
     │
     ▼
Lab 01
Filesystem Input
     │
     ▼
Lab 02
Database Input
```

---

# 17. Checklist Pemahaman Lab 02

Setelah menyelesaikan lab ini, saya harus dapat menjelaskan:

* [ ] Apa fungsi form login?
* [ ] Apa perbedaan GET dan POST secara dasar?
* [ ] Apa fungsi database dalam autentikasi?
* [ ] Apa itu SQL?
* [ ] Apa itu SQL Injection?
* [ ] Mengapa karakter `'` penting dalam konteks SQL string?
* [ ] Apa fungsi operator `OR`?
* [ ] Mengapa `'1'='1'` bernilai TRUE?
* [ ] Apa itu Authentication Bypass?
* [ ] Mengapa string concatenation pada SQL berbahaya?
* [ ] Apa itu Prepared Statement?
* [ ] Apa itu Parameterized Query?
* [ ] Mengapa mitigasi lebih penting daripada sekadar memblokir satu payload?

---

# 18. Latihan Pemahaman

### Pertanyaan 1

Mengapa:

```sql
'1'='1'
```

menghasilkan:

```text
TRUE
```

### Pertanyaan 2

Apa perbedaan logika:

```text
FALSE AND TRUE
```

dengan:

```text
FALSE OR TRUE
```

### Pertanyaan 3

Mengapa masalah sebenarnya bukan terletak pada string:

```text
' OR '1'='1
```

tetapi pada cara backend membangun SQL query?

### Pertanyaan 4

Jelaskan dengan kata-kata sendiri alur berikut:

```text
Login Form
    ↓
User Input
    ↓
Backend
    ↓
SQL Query
    ↓
Database
    ↓
Authentication Result
```

### Pertanyaan 5

Mengapa prepared statement lebih aman dibanding:

```text
"SQL QUERY " + userInput
```

---

# 19. Kesimpulan

Pada Lab 02 saya berhasil melakukan login pada:

```text
/ujianmiegoreng
```

dengan memanfaatkan SQL Injection pada proses autentikasi.

Payload yang berhasil digunakan dalam environment lab:

```text
' OR '1'='1
```

Payload tersebut menggunakan konsep **boolean condition**:

```text
'1'='1'
   ↓
 TRUE
```

dikombinasikan dengan:

```text
OR
```

untuk memengaruhi logika SQL pada implementasi login yang rentan.

Pelajaran terpenting dari lab ini bukan menghafalkan:

```text
' OR '1'='1
```

melainkan memahami prinsip:

```text
User Input
     ↓
Tidak dipisahkan secara aman dari SQL
     ↓
Input dapat menjadi bagian SQL syntax
     ↓
SQL logic dapat berubah
     ↓
Security control dapat dilewati
```

Mitigasi utamanya adalah memastikan **input pengguna diperlakukan sebagai data, bukan sebagai bagian dari SQL query**, terutama melalui prepared statement/parameterized query.

---

# 20. Cheat Sheet Lab 02

```text
LAB            : Lab 02
PATH           : /ujianmiegoreng
CATEGORY       : SQL Injection
TYPE           : Authentication Bypass
INPUT          : Login Form
METHOD         : POST
PAYLOAD LAB    : ' OR '1'='1
CORE CONCEPT   : Boolean / Always-True Condition
ROOT CAUSE     : Unsafe SQL query construction
MAIN DEFENSE   : Parameterized Query / Prepared Statement
```

Mental model:

```text
Normal
======

Input
  ↓
DATA
  ↓
SQL Query
  ↓
Database


Vulnerable
==========

Input
  ↓
DATA + SQL Syntax
  ↓
Query Logic Berubah
  ↓
Database
  ↓
Authentication Bypass


Secure
======

SQL Structure ─────────┐
                       ├── Database
User Data ─ Parameter ─┘
```
