# Exam 02 — Technical Walkthrough (Sanitized)

> **Konteks:** HackersBootcamp/XCODE enterprise cyber range berizin. **Selesai:** 13 November 2026 (WIB). Catatan ini menjelaskan urutan kejadian dan alasan teknis. IP, password, flag, serta endpoint akses eksternal disamarkan.

## 1. Scope, tujuan, dan akses awal

Objective ujian adalah mencapai host internal `[TARGET_IP]`, memperoleh hak akses root, dan membaca `/opt/pass.txt`. Infrastruktur bersegmen melibatkan jump host, Kali bantuan, server Samba `[SMB_HOST]`, server Debian/FTP `[FTP_HOST]`, dan router whitelist `[ROUTER_IP]`.

Dari laptop Windows, koneksi dilakukan lewat **PuTTY → VPS/jump host → SSH Kali bantuan**. Ini menyelesaikan masalah ketika koneksi SSH langsung dari laptop tidak berhasil.

Ujian juga mencakup MD5, Base64, Caesar cipher, analisis ELF, dan wordlist; semuanya dilaporkan selesai sebelum jalur enterprise utama.

## 2. Recon dari Kali

```bash
ip -br addr
ip route
ping -c 3 [SMB_HOST]
ping -c 3 [FTP_HOST]
nmap -sT -sV -p 21,22,80,139,445 [SMB_HOST]
nmap -sT -sV -p 21,22,80,139,445 [FTP_HOST]
```

**Observasi yang tercatat:**

| Host | Layanan teramati | Catatan |
|---|---|---|
| Samba | OpenSSH 7.3p1, Apache 2.4.18, Samba 4.4.5 (139/445) | Samba teridentifikasi sebagai `ubuntu16data` |
| Debian/FTP | ProFTPD 1.3.3a (21), OpenSSH 5.5p1 (22) | Anonymous login FTP ditolak |
| Target internal | Tidak dapat di-ping langsung dari Kali | Gateway memberikan unreachable/redirect |

Layanan HTTP Samba menampilkan halaman Apache default. Tidak ditemukan aplikasi khusus dari respons awal tersebut.

## 3. Mengidentifikasi writable SMB share

```bash
smbclient -L //[SMB_HOST] -N
smbclient //[SMB_HOST]/profiles -N
smbmap -H [SMB_HOST] -u guest -p ''
```

Ditemukan share `profiles` yang dapat diakses tanpa kredensial. `smbmap` melaporkan `READ, WRITE`. Daftar direktori berisi nama seperti `.X11-unix`, `.ICE-unix`, dan `.font-unix`, sehingga share diduga mengarah ke direktori sementara Linux. Dugaan ini kemudian dikonfirmasi oleh output modul eksploit yang menunjukkan path server `/tmp`.

Uji aman menggunakan file penanda berhasil melakukan upload, download, pemeriksaan isi, dan penghapusan. Izin tulis saja **tidak sama dengan** eksekusi perintah pada host.

## 4. Validasi Samba RCE

Versi Samba dan kondisi writable share cocok dengan prasyarat **CVE-2017-7494**. Modul yang digunakan di cyber range:

```text
exploit/linux/samba/is_known_pipename
```

Konfigurasi target menunjuk ke `[SMB_HOST]` port 445 serta share `profiles`. Pemeriksaan `check` melaporkan *appears to be vulnerable*.

Saat modul dijalankan dalam scope ujian, log menunjukkan upload shared library, percobaan pemuatan library, dan akhirnya `Command shell session opened`. Output awal beberapa kali berisi `STATUS_OBJECT_NAME_NOT_FOUND`, tetapi pemuatan selanjutnya berhasil.

**Verifikasi sesungguhnya:** pada shell hasil eksploit (bukan prompt `msf >`), perintah `whoami`, `id`, `hostname` menunjukkan:

```text
whoami   -> root
id       -> uid=0(root) gid=0(root)
hostname -> ubuntu16data
pwd      -> /tmp
```

Sesi awal sempat hilang. Pelajaran troubleshooting: perintah `whoami` pada prompt `msf exploit(...) >` menjalankan perintah di **Kali**, sedangkan setelah `run` sukses modul ini langsung menyediakan shell target tanpa prompt mencolok. `sessions -i` yang diketik pada shell target diperlakukan sebagai perintah shell biasa.

## 5. Routing dan jaringan internal

Pada shell Samba, `ip -br addr` menampilkan alamat `[SMB_HOST]/16`. Tabel rute memiliki connected route `192.168.0.0/16`; target dianggap satu jaringan on-link meski subnet lab sebenarnya dipisah router.

Hasil awal:

```text
ip route get [TARGET_IP] -> dev enp0s3 (on-link)
ping [TARGET_IP]         -> Destination Host Unreachable
```

Router whitelist `[ROUTER_IP]` dapat dijangkau dari Samba. Dalam lab, dipakai **host route /32 via router**; setelah itu `ip route get` menunjuk router dan ping ke target utama sukses dengan 0% packet loss.

Secara konseptual:

```text
Samba /16 --[host route /32]--> Router (whitelist) --> Target internal
```

Rute spesifik lebih diprioritaskan daripada connected route /16. Ini adalah perbaikan jalur dari host yang memang masuk whitelist, **bukan bypass whitelist dari Kali**. Setelah uji coba rute sempat dihapus dan kemudian ditambahkan kembali untuk tahap lanjut.

## 6. Recon server target dari Samba

Nmap tersedia di server Samba. Scan layanan terbatas ke target menghasilkan:

| Port | Status | Service/version |
|---|---|---|
| 21 | open | ProFTPD 1.3.5rc3 |
| 22 | open | OpenSSH 6.6p1 Ubuntu |
| 80 | open | Apache 2.4.7, PHP 5.5.9 terlihat pada header |
| 443 | closed | HTTPS |
| 445 | closed | SMB |

Perintah Nmap dengan skrip `ftp-syst` gagal karena instalasi Nmap lama di host Samba tidak mengenali skrip tersebut. Perintah `searchsploit` juga tidak tersedia pada host Samba; itu adalah masalah lokasi tool, bukan kegagalan target.

**Penting:** ProFTPD pada target hanya ditemukan lewat enumerasi, **tidak dieksploitasi** dalam jalur penyelesaian ini.

## 7. Web local file read / path traversal

Halaman HTTP target menunjukkan objective latihan: menemukan user di `/etc/passwd`, lalu autentikasi dan eskalasi privilege hingga membaca `/opt/pass.txt`.

Navigasi memakai parameter `page`, seperti `page=home.php`, `page=about.php`, dan `page=contact.php`.

Pengujian path traversal pada parameter tersebut menyebabkan isi `/etc/passwd` tampil di respons HTML. Ini mendukung finding **local file read melalui path traversal**. Tanpa source code aplikasi, tidak dapat memastikan apakah fungsi internal memakai PHP `include()` atau hanya pembacaan file.

Akun non-sistem yang terlihat: `data`, `paijo`, `nana`, dan `butoijo` (nama-nama akun lab, tanpa informasi kredensial). User `data` dan `butoijo` tampak memiliki shell `/bin/bash`; kolom shell kosong pada dua user lain tidak membuktikan bahwa login mereka mustahil.

## 8. Credential testing dan SSH login

Wordlist yang disediakan ujian dipakai untuk pengujian password SSH secara terbatas terhadap empat akun lab tersebut, dari server yang sudah masuk scope. Hasil Hydra: **satu kombinasi username/password valid ditemukan**; password tidak dicatat di repo publik.

Sesi SSH awal gagal karena shell hasil eksploit Samba bukan TTY penuh (*Pseudo-terminal will not be allocated because stdin is not a terminal*). Setelah membuat PTY pada shell Samba, autentikasi interaktif SSH berhasil.

Hasil di target:

```text
whoami -> paijo
id     -> uid=1001(paijo) gid=1001(paijo)
hostname -> ubuntu
```

Terdapat pesan `Could not chdir to home directory /home/paijo`; login tetap berhasil. Ini menunjukkan direktori home tidak tersedia, bukan kesalahan kredensial.

## 9. Privilege escalation dan objective

Pemeriksaan `sudo -l` pada host target menunjukkan:

```text
User paijo may run the following commands on ubuntu:
    (ALL : ALL) ALL
```

Akun memiliki hak sudo untuk semua perintah, sehingga **tidak dibutuhkan eksploitasi kernel**. Autentikasi sudo berhasil, sesi root diperoleh menggunakan hak yang telah diberikan, dan file tujuan `/opt/pass.txt` berhasil diakses.

**Flag dan nilai grup privat tidak dipublikasikan**, sesuai keamanan dokumentasi dan etika kompetisi.

Kesalahan pengetikan `ld -ld /opt` sempat menghasilkan `a.out: Permission denied`; perintah yang dimaksud adalah `ls -ld /opt`. Pesan tersebut berasal dari linker `ld`, bukan bukti bahwa akses sudo ditolak.

## 10. Kesimpulan teknis

Rantai yang benar-benar dibuktikan:

```text
SMB anonymous write
  -> Samba RCE (CVE-2017-7494)
  -> root on intermediate host
  -> authorized routed path into internal network
  -> web file read of /etc/passwd
  -> weak SSH credential
  -> target-user SSH login
  -> unrestricted sudo
  -> root and objective completion
```

**Keterbatasan:** tidak ada bukti eksploit ProFTPD, kerentanan kernel, atau eksploit akses-control pada router. Detail paket, timestamps rinci, dan tingkat keparahan formal memerlukan evidence tambahan. Lihat [Findings & Mitigations](FINDINGS-AND-MITIGATIONS.md) dan [Evidence Checklist](EVIDENCE-CHECKLIST.md).
