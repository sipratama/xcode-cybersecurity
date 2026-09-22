# Pentest Learning Notes

## Lab 00 — PuTTY, SSH, dan SSH Tunneling

> **Catatan:** Dokumentasi ini digunakan untuk environment lab/pentest yang memang memberikan izin akses. Host, username, port, dan alamat IP pada contoh dapat diganti sesuai environment lab masing-masing.

---

# 1. Tujuan Pembelajaran

Sebelum melakukan pengujian pada sebuah lab penetration testing, saya perlu memahami terlebih dahulu bagaimana cara terhubung ke server atau VPS yang menjadi pintu masuk menuju jaringan lab.

Pada Lab 00 ini saya mempelajari:

* Apa itu PuTTY
* Apa itu SSH
* Perbedaan SSH dan PuTTY
* Konsep SSH Client dan SSH Server
* Apa itu port SSH
* Apa itu SSH Tunnel
* Perbedaan SSH biasa dengan SSH Tunnel
* Local Port Forwarding
* Remote Port Forwarding
* Dynamic Port Forwarding
* Mengakses SSH menggunakan PuTTY
* Mengakses SSH menggunakan Windows PowerShell
* Mengakses SSH menggunakan macOS
* Mengakses SSH menggunakan Ubuntu/Linux
* Memahami hubungan laptop → VPS → jaringan internal lab

---

# 2. Gambaran Environment

Dalam penetration testing lab, terkadang target tidak dapat diakses langsung dari internet.

Contohnya:

```text
Laptop Saya
    │
    │ Internet
    ▼
VPS / Jump Server
    │
    │ Internal Network
    ▼
Target Lab
192.168.x.x
```

Alamat seperti:

```text
192.168.x.x
```

umumnya merupakan private IP address.

Artinya target tersebut belum tentu dapat diakses langsung dari laptop saya.

Sebagai contoh:

```text
Laptop
   │
   │ SSH
   ▼
VPS
   │
   ├── 192.168.39.87
   ├── 192.168.39.88
   └── 192.168.39.89
```

VPS tersebut dapat berfungsi sebagai **jump server** atau titik masuk menuju jaringan internal lab.

---

# 3. Apa Itu SSH?

SSH merupakan singkatan dari:

```text
Secure Shell
```

SSH adalah protokol yang memungkinkan kita terhubung ke komputer lain melalui jaringan secara aman.

Secara sederhana:

```text
Komputer A
   │
   │ SSH Connection
   ▼
Komputer B
```

Setelah koneksi berhasil, kita dapat memperoleh terminal pada komputer tujuan.

Contoh:

```bash
ssh user@server
```

Misalnya:

```bash
ssh student@192.168.1.10
```

Setelah autentikasi berhasil:

```text
Laptop
   │
   │ SSH
   ▼
Server
   │
   └── Shell / Terminal
```

Kita kemudian dapat menjalankan command pada server tersebut sesuai permission akun yang diberikan.

---

# 4. SSH Client dan SSH Server

Dalam koneksi SSH terdapat dua komponen utama.

## SSH Client

SSH Client adalah aplikasi yang digunakan dari komputer kita untuk memulai koneksi.

Contohnya:

```text
PuTTY
Windows OpenSSH
macOS ssh
Ubuntu ssh
```

## SSH Server

SSH Server berjalan pada komputer tujuan dan menerima koneksi SSH.

Pada Linux biasanya menggunakan:

```text
sshd
```

atau:

```text
OpenSSH Server
```

Hubungannya:

```text
┌─────────────────┐
│ Laptop Saya     │
│                 │
│ SSH Client      │
└────────┬────────┘
         │
         │ SSH
         ▼
┌─────────────────┐
│ VPS / Server    │
│                 │
│ SSH Server      │
│ sshd            │
└─────────────────┘
```

---

# 5. Apa Itu PuTTY?

**PuTTY** adalah aplikasi SSH Client yang populer, terutama pada Windows.

PuTTY bukan SSH itu sendiri.

Perbedaannya:

```text
SSH   = protokol
PuTTY = aplikasi/client yang menggunakan protokol SSH
```

Analogi sederhananya:

```text
HTTP     = protokol
Browser  = aplikasi yang menggunakan HTTP

SSH      = protokol
PuTTY    = aplikasi yang menggunakan SSH
```

PuTTY menyediakan GUI sehingga pengguna Windows dapat mengatur koneksi SSH tanpa harus menghafalkan semua command.

---

# 6. Informasi yang Dibutuhkan untuk SSH

Biasanya lab memberikan informasi seperti:

```text
Host     : lab.example
Port     : 7398
Username : student
Password : ********
```

Empat informasi tersebut memiliki fungsi berbeda.

### Host

Menentukan server yang akan dihubungi.

```text
lab.example
```

Host juga dapat berupa IP:

```text
203.0.113.10
```

### Port

Menentukan port tempat SSH Server menerima koneksi.

Default SSH adalah:

```text
22
```

Tetapi administrator dapat menggunakan port lain.

Contoh:

```text
7398
```

### Username

Menentukan user Linux yang digunakan untuk login.

Contoh:

```text
student
```

### Password

Digunakan untuk proses autentikasi apabila server menggunakan password authentication.

SSH juga dapat menggunakan:

```text
SSH Key
```

sebagai pengganti password.

---

# 7. Mengakses SSH Menggunakan PuTTY

Pada Windows, buka aplikasi PuTTY.

Pada bagian:

```text
Host Name (or IP address)
```

masukkan:

```text
lab.example
```

Kemudian pada:

```text
Port
```

masukkan:

```text
7398
```

Connection type:

```text
SSH
```

Sehingga konfigurasinya:

```text
Host : lab.example
Port : 7398
Type : SSH
```

Klik:

```text
Open
```

Saat pertama kali terkoneksi, PuTTY dapat menampilkan peringatan mengenai **SSH Host Key**.

Host key digunakan untuk membantu memastikan identitas server SSH yang sedang dihubungi. Pada environment nyata, fingerprint sebaiknya dibandingkan dengan fingerprint yang diberikan administrator/lab sebelum dipercaya.

Setelah koneksi terbuka:

```text
login as:
```

masukkan username yang diberikan.

Kemudian masukkan password.

Saat mengetik password pada terminal SSH:

```text
tidak ada karakter yang muncul
```

Ini normal pada terminal Linux.

Jika autentikasi berhasil, kita akan mendapatkan shell server.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 01]**

> Tampilan konfigurasi PuTTY berisi Host, Port, dan SSH.

**[PLACEHOLDER SCREENSHOT 02]**

> Tampilan terminal PuTTY setelah login berhasil.

---

# 8. Apakah Harus Menggunakan PuTTY?

Tidak.

PuTTY hanyalah salah satu SSH Client.

Koneksi yang sama dapat dilakukan menggunakan:

```text
Windows PowerShell
macOS Terminal
Ubuntu Terminal
WSL
```

Konsepnya tetap:

```text
SSH Client
     │
     │ SSH Protocol
     ▼
SSH Server
```

Yang berbeda hanya aplikasi/client yang digunakan.

---

# 9. SSH Menggunakan Windows PowerShell

Windows modern biasanya menyediakan OpenSSH Client.

Buka:

```text
PowerShell
```

Periksa apakah SSH tersedia:

```powershell
ssh -V
```

Jika tersedia, koneksi dapat dilakukan menggunakan:

```powershell
ssh -p <port> <username>@<host>
```

Contoh:

```powershell
ssh -p 7398 student@lab.example
```

Perhatikan:

```text
-p
```

menggunakan huruf kecil.

Artinya:

```text
ssh
│
├── -p 7398
│      └── SSH port
│
└── student@lab.example
       │       │
       │       └── server
       └── username
```

Kemudian masukkan password apabila diminta.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 03]**

> Koneksi SSH menggunakan Windows PowerShell.

---

# 10. SSH Menggunakan macOS

macOS sudah memiliki SSH Client melalui Terminal.

Buka:

```text
Terminal
```

Kemudian gunakan:

```bash
ssh -p <port> <username>@<host>
```

Contoh:

```bash
ssh -p 7398 student@lab.example
```

Jika muncul konfirmasi host:

```text
Are you sure you want to continue connecting?
```

verifikasi fingerprint jika tersedia, kemudian lanjutkan apabila server memang benar.

Masukkan password.

Jika berhasil:

```text
MacBook
   │
   │ SSH
   ▼
VPS
```

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 04]**

> SSH dari macOS Terminal.

---

# 11. SSH Menggunakan Ubuntu / Linux

Pada Ubuntu, periksa SSH Client:

```bash
ssh -V
```

Kemudian:

```bash
ssh -p <port> <username>@<host>
```

Contoh:

```bash
ssh -p 7398 student@lab.example
```

Konsepnya sama seperti macOS karena keduanya menyediakan command-line OpenSSH.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 05]**

> SSH dari Ubuntu/Linux Terminal.

---

# 12. Ringkasan SSH pada Berbagai Sistem

PuTTY:

```text
Host : lab.example
Port : 7398
Type : SSH
```

Windows PowerShell:

```powershell
ssh -p 7398 student@lab.example
```

macOS:

```bash
ssh -p 7398 student@lab.example
```

Ubuntu/Linux:

```bash
ssh -p 7398 student@lab.example
```

Semua metode tersebut memiliki tujuan yang sama:

```text
Laptop
   │
   │ SSH
   ▼
Server / VPS
```

---

# 13. Apa Itu Tunnel?

Tunnel adalah mekanisme membawa traffic jaringan melalui koneksi lain.

Analogi sederhananya adalah membuat sebuah **terowongan**.

Misalnya laptop saya tidak dapat mengakses:

```text
192.168.39.87
```

tetapi VPS dapat mengaksesnya.

Kondisinya:

```text
Laptop ─────X─────> 192.168.39.87
                       ▲
                       │
                       │ bisa diakses
                       │
                      VPS
```

Karena saya dapat SSH ke VPS:

```text
Laptop ───SSH───> VPS
```

maka SSH dapat digunakan sebagai jalur untuk membawa traffic tertentu melalui VPS.

Konsep inilah yang disebut:

```text
SSH Tunneling
```

atau:

```text
SSH Port Forwarding
```

---

# 14. SSH Biasa vs SSH Tunnel

SSH biasa:

```text
Laptop
   │
   │ SSH
   ▼
VPS
   │
   ▼
Terminal VPS
```

Tujuan utamanya adalah mendapatkan shell pada server.

SSH Tunnel:

```text
Laptop
   │
   │ encrypted SSH connection
   ▼
VPS
   │
   │ forwarded traffic
   ▼
Internal Service
```

Tujuan utamanya adalah **meneruskan traffic** melalui koneksi SSH.

Jadi:

```text
SSH Login   → bekerja di terminal remote
SSH Tunnel  → meneruskan koneksi/network traffic
```

Keduanya dapat digunakan secara bersamaan.

---

# 15. Jenis SSH Tunneling

Terdapat tiga konsep penting:

```text
Local Port Forwarding
Remote Port Forwarding
Dynamic Port Forwarding
```

Untuk tahap awal, yang paling penting dipahami adalah **Local Port Forwarding**.

---

# 16. Local Port Forwarding

Bayangkan terdapat web server internal:

```text
192.168.39.87:80
```

Laptop tidak dapat mengaksesnya langsung.

Namun VPS dapat.

```text
Laptop
   │
   │ SSH
   ▼
VPS
   │
   │ Internal Network
   ▼
192.168.39.87:80
```

Kita dapat membuat local tunnel dengan format:

```bash
ssh -L <local-port>:<target-host>:<target-port> -p <ssh-port> <user>@<ssh-server>
```

Contoh:

```bash
ssh -L 8080:192.168.39.87:80 -p 7398 student@lab.example
```

Artinya:

```text
-L
│
├── 8080
│   └── port pada laptop
│
├── 192.168.39.87
│   └── target yang dapat dijangkau VPS
│
└── 80
    └── port target
```

Alur traffic:

```text
Browser Laptop
      │
      │ localhost:8080
      ▼
┌──────────────────┐
│ Laptop           │
│ Local Port 8080  │
└────────┬─────────┘
         │
         │ SSH Tunnel
         ▼
┌──────────────────┐
│ VPS              │
└────────┬─────────┘
         │
         │ Internal Network
         ▼
┌──────────────────┐
│ 192.168.39.87    │
│ Port 80          │
└──────────────────┘
```

Setelah tunnel dibuat, browser pada laptop dapat membuka:

```text
http://localhost:8080
```

Traffic kemudian diteruskan melalui SSH menuju:

```text
192.168.39.87:80
```

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 06]**

> Terminal ketika Local Port Forwarding aktif.

**[PLACEHOLDER SCREENSHOT 07]**

> Browser membuka `localhost:8080` dan menampilkan aplikasi internal lab.

---

# 17. Local Tunnel di Windows PowerShell

OpenSSH pada PowerShell menggunakan syntax:

```powershell
ssh -L 8080:192.168.39.87:80 -p 7398 student@lab.example
```

Kemudian buka browser:

```text
http://localhost:8080
```

Selama terminal SSH tersebut masih aktif, tunnel tetap tersedia.

---

# 18. Local Tunnel di macOS

Pada macOS:

```bash
ssh -L 8080:192.168.39.87:80 -p 7398 student@lab.example
```

Kemudian:

```text
Browser
   ↓
http://localhost:8080
```

Traffic akan melewati SSH menuju target internal.

---

# 19. Local Tunnel di Ubuntu/Linux

Pada Ubuntu:

```bash
ssh -L 8080:192.168.39.87:80 -p 7398 student@lab.example
```

Kemudian akses:

```text
http://localhost:8080
```

Konsep dan syntax OpenSSH pada:

```text
Windows PowerShell
macOS
Ubuntu
WSL
```

pada dasarnya sama.

---

# 20. Local Tunnel Menggunakan PuTTY

PuTTY juga mendukung SSH tunneling.

Buka:

```text
Connection
└── SSH
    └── Tunnels
```

Untuk contoh target:

```text
192.168.39.87:80
```

isi:

```text
Source port:
8080
```

Destination:

```text
192.168.39.87:80
```

Pilih:

```text
Local
```

kemudian:

```text
Add
```

Setelah itu kembali ke:

```text
Session
```

dan lakukan koneksi SSH seperti biasa.

Setelah login berhasil:

```text
http://localhost:8080
```

akan diteruskan melalui VPS menuju target internal.

### Dokumentasi

**[PLACEHOLDER SCREENSHOT 08]**

> Menu `Connection → SSH → Tunnels` pada PuTTY.

**[PLACEHOLDER SCREENSHOT 09]**

> Source Port `8080` dan Destination `192.168.39.87:80`.

---

# 21. Dynamic Port Forwarding

Selain meneruskan satu port tertentu, SSH juga dapat membuat **SOCKS proxy**.

Format:

```bash
ssh -D <local-port> -p <ssh-port> <user>@<server>
```

Contoh:

```bash
ssh -D 1080 -p 7398 student@lab.example
```

Secara konseptual:

```text
Application
     │
     │ SOCKS
     ▼
localhost:1080
     │
     │ SSH Tunnel
     ▼
    VPS
     │
     ▼
Network yang dapat
dijangkau VPS
```

Berbeda dengan:

```text
-L
```

yang meneruskan koneksi ke satu host/port tertentu, `-D` membuat local SOCKS proxy sehingga aplikasi yang mendukung SOCKS dapat mengirim koneksinya melalui SSH server.

Untuk tahap awal pembelajaran, saya cukup memahami konsepnya terlebih dahulu sebelum menggunakan konfigurasi proxy yang lebih kompleks.

---

# 22. Remote Port Forwarding

SSH juga memiliki:

```text
-R
```

atau **Remote Port Forwarding**.

Konsep arahnya berlawanan dengan Local Port Forwarding.

Local forwarding:

```text
Laptop → SSH Server → Destination
```

Remote forwarding secara konseptual:

```text
Remote Side → SSH Tunnel → Service di sisi client
```

Format umumnya:

```bash
ssh -R <remote-port>:<destination>:<destination-port> user@server
```

Penggunaan `-R` sangat bergantung pada konfigurasi dan izin server SSH.

Untuk tahap awal penetration testing, saya akan memprioritaskan memahami:

```text
SSH
↓
Local Port Forwarding (-L)
↓
Dynamic Port Forwarding (-D)
↓
Remote Port Forwarding (-R)
```

---

# 23. Memahami localhost

Saat menggunakan tunnel, istilah:

```text
localhost
```

akan sering muncul.

`localhost` berarti komputer tempat command atau aplikasi tersebut sedang berjalan.

Alamat IPv4 yang umum:

```text
127.0.0.1
```

Contohnya:

```text
http://localhost:8080
```

berarti:

```text
akses port 8080 pada komputer saya sendiri
```

Tetapi jika port `8080` telah dibuat sebagai SSH tunnel:

```text
localhost:8080
       │
       ▼
SSH Tunnel
       │
       ▼
VPS
       │
       ▼
192.168.39.87:80
```

maka request ke localhost tersebut sebenarnya diteruskan menuju target internal.

---

# 24. Kenapa Tunnel Penting dalam Pentest?

Dalam penetration testing, target sering berada pada jaringan internal.

Contohnya:

```text
Internet
    │
    ▼
┌─────────────┐
│ Jump Server │
│ / VPS       │
└──────┬──────┘
       │
       │ Internal Network
       ▼
┌───────────────────────────┐
│ 192.168.39.0/24           │
│                           │
│ 192.168.39.87 Web Server  │
│ 192.168.39.88 Service A   │
│ 192.168.39.89 Service B   │
└───────────────────────────┘
```

Laptop saya mungkin hanya mengetahui dan dapat menjangkau VPS.

VPS kemudian mempunyai akses ke jaringan:

```text
192.168.39.0/24
```

SSH tunneling memungkinkan traffic tertentu dari laptop melewati VPS menuju service yang memang diizinkan dalam scope lab.

---

# 25. Hubungan dengan Lab 01

Pada Lab 01 sebelumnya, target:

```text
http://192.168.39.87/ujianmonitor
```

hanya dapat diakses dari VPS.

Cara pertama adalah login ke VPS:

```text
Laptop
   │
   │ SSH
   ▼
VPS
```

kemudian menggunakan:

```bash
lynx http://192.168.39.87/ujianmonitor
```

atau:

```bash
w3m http://192.168.39.87/ujianmonitor
```

Sehingga:

```text
Laptop
   │
   │ SSH
   ▼
VPS
   │
   │ HTTP
   ▼
192.168.39.87
```

Dengan local port forwarding, apabila environment lab mengizinkannya, konsepnya dapat berubah menjadi:

```text
Browser Laptop
      │
      │ localhost:8080
      ▼
SSH Tunnel
      │
      ▼
VPS
      │
      ▼
192.168.39.87:80
```

Dengan demikian saya mulai memahami mengapa **SSH dan tunneling menjadi pengetahuan dasar yang penting sebelum mempelajari pengujian aplikasi pada jaringan internal**.

---

# 26. Troubleshooting Dasar

Jika muncul:

```text
Connection timed out
```

kemungkinan:

* host tidak dapat dijangkau;
* firewall memblokir koneksi;
* VPN/lab network belum terhubung;
* alamat atau port salah.

Jika muncul:

```text
Connection refused
```

host berhasil dijangkau, tetapi tidak ada service yang menerima koneksi pada port tersebut atau koneksi ditolak.

Jika muncul:

```text
Permission denied
```

kemungkinan autentikasi gagal.

Periksa:

```text
username
password
SSH key
```

Jika muncul:

```text
Address already in use
```

saat membuat tunnel:

```text
localhost:8080
```

kemungkinan port `8080` pada laptop sudah digunakan aplikasi lain.

Gunakan local port lain yang tersedia, misalnya:

```text
8081
```

sehingga:

```bash
ssh -L 8081:192.168.39.87:80 -p 7398 student@lab.example
```

---

# 27. Checklist Lab

Sebelum melanjutkan ke latihan penetration testing, saya harus bisa menjelaskan:

* [ ] Apa itu SSH?
* [ ] Apa perbedaan SSH dan PuTTY?
* [ ] Apa fungsi host?
* [ ] Apa fungsi port?
* [ ] Apa fungsi username?
* [ ] Apa itu SSH Client?
* [ ] Apa itu SSH Server?
* [ ] Apa itu VPS/jump server?
* [ ] Apa itu localhost?
* [ ] Apa itu SSH Tunnel?
* [ ] Apa perbedaan SSH biasa dengan SSH Tunnel?
* [ ] Apa fungsi `-p`?
* [ ] Apa fungsi `-L`?
* [ ] Apa konsep `-D`?
* [ ] Apa konsep `-R`?
* [ ] Mengapa private IP target terkadang hanya dapat diakses dari VPS?

---

# 28. Latihan Mandiri

### Latihan 1

Jelaskan dengan kata-kata sendiri apa yang terjadi pada command:

```bash
ssh -p 7398 student@lab.example
```

Identifikasi:

```text
SSH Client :
SSH Server :
SSH Port   :
Username   :
```

### Latihan 2

Jelaskan command:

```bash
ssh -L 8080:192.168.39.87:80 -p 7398 student@lab.example
```

Identifikasi:

```text
Local Port       :
Target IP        :
Target Port      :
SSH Server       :
SSH Server Port  :
SSH Username     :
```

### Latihan 3

Gambarkan alur:

```text
http://localhost:8080
```

hingga request mencapai:

```text
192.168.39.87:80
```

### Latihan 4

Jelaskan perbedaan:

```text
ssh -p ...
ssh -L ...
ssh -D ...
ssh -R ...
```

menggunakan kata-kata sendiri.

---

# 29. Kesimpulan Pembelajaran

Dari Lab 00 saya memahami bahwa **PuTTY bukanlah SSH**.

PuTTY merupakan:

```text
SSH Client
```

sedangkan SSH adalah:

```text
network protocol
```

PuTTY juga bukan satu-satunya cara untuk menggunakan SSH.

Saya dapat menggunakan:

```text
Windows → PuTTY / PowerShell
macOS   → Terminal
Ubuntu  → Terminal
WSL     → Terminal
```

Untuk koneksi biasa:

```text
ssh -p <port> <username>@<server>
```

Sedangkan SSH Tunnel memungkinkan traffic jaringan diteruskan melalui SSH.

Contoh Local Port Forwarding:

```text
localhost:8080
      │
      ▼
SSH Tunnel
      │
      ▼
VPS
      │
      ▼
192.168.39.87:80
```

Command:

```bash
ssh -L 8080:192.168.39.87:80 -p 7398 student@lab.example
```

Dengan memahami konsep ini, saya memiliki dasar untuk memahami bagaimana sebuah lab penetration testing menggunakan VPS atau jump server untuk memberikan akses menuju target yang berada pada jaringan internal.

---

# 30. Cheat Sheet Lab 00

SSH biasa:

```bash
ssh -p <SSH_PORT> <USER>@<SSH_SERVER>
```

Windows PowerShell:

```powershell
ssh -p <SSH_PORT> <USER>@<SSH_SERVER>
```

macOS:

```bash
ssh -p <SSH_PORT> <USER>@<SSH_SERVER>
```

Ubuntu/Linux:

```bash
ssh -p <SSH_PORT> <USER>@<SSH_SERVER>
```

Local Port Forwarding:

```bash
ssh -L <LOCAL_PORT>:<TARGET_IP>:<TARGET_PORT> -p <SSH_PORT> <USER>@<SSH_SERVER>
```

Dynamic Port Forwarding:

```bash
ssh -D <LOCAL_PORT> -p <SSH_PORT> <USER>@<SSH_SERVER>
```

Remote Port Forwarding:

```bash
ssh -R <REMOTE_PORT>:<DESTINATION>:<DESTINATION_PORT> -p <SSH_PORT> <USER>@<SSH_SERVER>
```

Konsep utama:

```text
SSH
│
├── Remote Shell
│
└── Port Forwarding / Tunneling
     │
     ├── -L → Local
     ├── -R → Remote
     └── -D → Dynamic / SOCKS
```

---

## Catatan Pribadi

Hal yang tidak boleh hanya saya hafalkan adalah command-nya.

Saya harus memahami perjalanan koneksinya:

```text
Dari mana traffic berasal?
        ↓
Port mana yang digunakan?
        ↓
Siapa SSH Server?
        ↓
Traffic keluar dari komputer mana?
        ↓
Apa destination akhirnya?
```

Jika saya sudah dapat menjawab lima pertanyaan tersebut ketika melihat sebuah command SSH tunnel, berarti saya sudah mulai memahami konsep tunneling dan bukan sekadar menghafal syntax.
