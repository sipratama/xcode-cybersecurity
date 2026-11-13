# Exam 02 — Evidence Checklist (Public-safe)

Bukti mentah dari ujian bersifat **pribadi** sampai izin publikasi dan proses redaksi selesai. Screenshot yang pernah dikirim dalam chat tidak otomatis disalin ke repository. Tambahkan manual hanya versi yang sudah disensor.

## Bukti yang direkomendasikan

| File yang disarankan | Isi bukti | Sensor sebelum publikasi |
|---|---|---|
| `images/01-kali-connectivity.png` | Akses Kali bantuan dan pengujian konektivitas | Host akses VPS, IP asli |
| `images/02-smb-enumeration.png` | Share `profiles` dan status READ/WRITE | IP, hostname lab |
| `images/03-samba-rce-root.png` | Session shell dan `uid=0(root)` | Alamat IP internal, payload name |
| `images/04-routing-before-after.png` | Route on-link gagal dan via router berhasil | IP router/target asli |
| `images/05-target-service-scan.png` | FTP/SSH/HTTP pada target internal | IP target |
| `images/06-web-file-read.png` | Fragmen aman respons /etc/passwd | Nama akun, IP, data lain |
| `images/07-credential-discovery.png` | Status tes dan indikator satu valid pair | **Password wajib disensor total** |
| `images/08-ssh-login.png` | Login ke host target sebagai akun lab | Username jika diperlukan, IP, password |
| `images/09-sudo-permissions.png` | Bukti `sudo -l` unrestricted | Identitas host |
| `images/10-completion.png` | Indikator penyelesaian objective | **Seluruh flag dan invite link wajib disensor** |

## Pemeriksaan sebelum commit

- [ ] Izin instruktur/penyelenggara untuk memublikasikan write-up dan bukti sudah dipastikan.
- [ ] Tidak ada flag asli, tautan privat, atau kode akses grup.
- [ ] Tidak ada password, token, cookie, private key, credential list atau file `/etc/shadow`.
- [ ] Tidak ada IP/host internal atau alamat jump server asli.
- [ ] Screenshot dipotong agar terminal lain dan informasi pribadi tidak terlihat.
- [ ] Metadata gambar dan nama file diperiksa.
- [ ] Setiap klaim teknis dalam README sesuai bukti; perbedaan antara observasi dan hipotesis dijelaskan.
- [ ] Pemeriksaan `git diff --cached` dan secret scanning lokal sudah dilakukan.

## Catatan

File README menggunakan placeholder `[SMB_HOST]`, `[FTP_HOST]`, `[ROUTER_IP]`, dan `[TARGET_IP]` untuk menjaga keamanan publikasi. Jangan mengganti placeholder dengan IP lab aktif di branch publik.

Untuk diagram jaringan, gunakan diagram konseptual dari `README.md`; jangan mengunggah PDF soal atau gambar resmi penyelenggara ke repo kecuali ada izin redistribusi.
