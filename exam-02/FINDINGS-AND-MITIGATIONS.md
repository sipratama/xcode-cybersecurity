# Exam 02 — Findings and Mitigations

> Penilaian kualitatif untuk **lingkungan latihan XCODE/HackersBootcamp**, bukan hasil pemindaian produksi. Tingkat risiko di bawah adalah estimasi untuk skenario lab; tidak ada skor CVSS yang dihitung secara formal.

| ID | Temuan | Tingkat risiko (indikatif) | Bukti |
|---|---|---|---|
| EX02-01 | Samba RCE melalui CVE-2017-7494 | Critical | Root shell pada server perantara |
| EX02-02 | Anonymous writable SMB share | High | Upload / download / delete berhasil tanpa password |
| EX02-03 | Path traversal / local file read di parameter web | High | Konten `/etc/passwd` tampil dalam respons |
| EX02-04 | Password SSH lemah pada akun lab | High | Wordlist ujian menghasilkan satu login valid |
| EX02-05 | Hak sudo tanpa pembatasan untuk akun hasil login | High | Output `(ALL : ALL) ALL` |
| EX02-06 | Perlindungan berlapis jaringan gagal menghentikan rantai | Contextual | Host yang ada di whitelist dapat mencapai target internal |

## EX02-01 — Samba remote code execution

**Observasi:** Samba 4.4.5 dengan writable share `profiles`, pemeriksaan Metasploit *appears vulnerable*, dan pengujian RCE menghasilkan shell `uid=0(root)` pada server Samba.

**Dampak:** pengambilalihan host perantara, pembacaan/perubahan data dan akses jaringan sesuai hak host.

**Mitigasi:** patch Samba ke rilis yang sudah mengatasi CVE-2017-7494, batasi akses SMB sesuai kebutuhan, audit writable shares, harden service account, monitoring perilaku pemuatan library dan aktivitas SMB anomali. Patch adalah kontrol utama, bukan hanya menonaktifkan akses anonim.

## EX02-02 — Writable SMB guest/anonymous

**Observasi:** enumerasi share melalui sesi tanpa kredensial dan uji file penanda menunjukkan baca, tulis, dan hapus dapat dilakukan.

**Dampak:** penyimpanan file tak tepercaya; meningkatkan peluang penyalahgunaan saat dikombinasikan dengan kelemahan service.

**Mitigasi:** nonaktifkan anonymous/guest write bila tidak diperlukan, konfigurasi ACL dan filesystem minimum, segmentasi port 139/445, audit share berkala.

**Catatan:** izin baca/tulis sendiri tidak otomatis berarti eksekusi perintah.

## EX02-03 — Path traversal / arbitrary local file read

**Observasi:** nilai parameter `page` dapat membuat isi `/etc/passwd` muncul dalam HTTP response.

**Dampak:** pengungkapan file internal yang dapat memfasilitasi serangan lanjutan; tidak ada bukti file lain dapat dibaca atau executable include.

**Mitigasi:** mapping ID halaman ke allowlist file statis, jangan terima path arbitrer dari pengguna, canonical path checks, least privilege proses web, lindungi file konfigurasi sensitif, uji regresi terhadap traversal dan encoding alternatif.

**Akar masalah:** validasi/pembatasan path tidak memadai (disimpulkan dari perilaku); fungsi PHP spesifik belum diverifikasi.

## EX02-04 — Password SSH lemah

**Observasi:** pengujian wordlist ujian pada empat akun lokal menemukan satu kombinasi yang valid. Nilai password sengaja tidak disimpan dalam repository.

**Dampak:** penyerang yang sudah dapat mencapai SSH dapat masuk sebagai pengguna lokal.

**Mitigasi:** password kuat dan unik, SSH keys dan/atau MFA bila layak, pembatasan percobaan login, monitoring autentikasi, rotasi kredensial lab setelah aktivitas, kurangi eksposur SSH antar-segmen.

## EX02-05 — Unrestricted sudo

**Observasi:** akun hasil autentikasi memiliki aturan `(ALL : ALL) ALL`. Eskalasi menjadi root menggunakan hak sudo yang sudah terkonfigurasi; bukan eksploit kernel.

**Dampak:** kompromi password akun tersebut dapat berkembang menjadi kompromi root.

**Mitigasi:** least privilege sudoers, pembatasan perintah berbasis peran, review akses admin, logging sudo, batasi akun berprivilege, gunakan approval/just-in-time elevation bila cocok.

## EX02-06 — Risiko kompromi bertingkat pada host whitelist

**Observasi:** dari Kali target internal tidak terjangkau secara langsung. Dari host Samba yang kompromi, jalur khusus melalui router whitelist berfungsi.

**Interpretasi:** router menjalankan pemisahan akses pada lapisan jaringan, tetapi kontrol jaringan tidak dapat mempercayai host yang sudah diambil alih.

**Mitigasi:** kurangi trust berdasarkan alamat sumber saja; pertimbangkan host identity dan autentikasi aplikasi, segmentasi mikro, proteksi endpoint pada jump/intermediate host, egress restriction, inspeksi trafik east-west, deteksi akses lintas-segmen, dan strategi pembatasan dampak saat satu host kompromi.

## Lessons learned untuk Blue Team

Aktivitas dalam lab yang berpotensi relevan untuk alert/korelasi (bukan klaim bahwa Wazuh benar-benar mengeluarkan alert):
- Banyak koneksi SMB dan upload file shared object yang tidak lazim.
- Proses shell yang dijalankan sebagai anak dari layanan Samba.
- Penambahan host route dan koneksi baru lintas-segmen dari perantara.
- HTTP request berisi traversal terhadap parameter file.
- Banyak kegagalan SSH yang diikuti login sukses.
- Penggunaan `sudo -i` setelah login akun lokal.

**Prioritas perbaikan:** patch service RCE, tutup guest write, perbaiki path handling, kuatkan autentikasi, batasi sudo, dan korelasikan deteksi antar-host. Fokus pada rantai, bukan satu temuan terpisah.
