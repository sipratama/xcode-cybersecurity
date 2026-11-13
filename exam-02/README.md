# Exam 02 — Enterprise Cyber Range (Red Team)

**Status:** Completed · **Tanggal penyelesaian:** 13 November 2026 (WIB) · **Program:** XCODE / HackersBootcamp Asia


> Dokumentasi pengalaman pribadi dalam **lingkungan cyber range berizin**. Ini adalah rekonstruksi berdasarkan output terminal dan screenshot selama ujian, bukan audit produksi atau petunjuk untuk menguji sistem tanpa izin. Alamat IP, password, nilai flag, tautan grup, dan artefak autentikasi disamarkan untuk publikasi.

## Tujuan dan hasil

Menguji pemahaman kriptografi dan reverse engineering, initial access, eksploitasi layanan, lintas-subnet, enumerasi akun, autentikasi, privilege escalation, dan pencapaian objective utama di server Linux yang dibatasi aksesnya.

| Objective | Hasil terverifikasi |
|---|---|
| Soal 1–4: MD5, Base64, Caesar, ELF | Selesai berdasarkan catatan ujian |
| Mengakses Kali bantuan melalui jump host | Berhasil |
| Enumerasi Samba dan FTP | Berhasil |
| Foothold pada Samba melalui CVE-2017-7494 | Root shell terverifikasi |
| Konektivitas lintas-subnet dari Samba | Berhasil setelah perbaikan route host |
| Pembacaan file akun via parameter web | Berhasil membaca /etc/passwd |
| Pengujian kredensial SSH dengan wordlist | Satu kombinasi valid ditemukan (dirahasiakan) |
| Login target dan peningkatan hak akses | Login user berhasil, sudo ALL terverifikasi, root tercapai |
| Objective /opt/pass.txt | Diselesaikan; isi tidak dipublikasikan |

## Topologi logis (identitas target disamarkan)
```text
Laptop (PuTTY)
  |
  v
VPS / Jump Host ----> Kali bantuan (pengujian)
                            |
              +-------------+--------------+
              |                            |
        Samba / Ubuntu                Debian / FTP
        [SMB_HOST]                    [FTP_HOST]
         ROOT SHELL
              |
              | via router / whitelist
              v
        [ROUTER_IP]  --->  [TARGET_IP]
                            |  SSH + Web + FTP
                            |  user shell -> sudo
                            v
                        root / flag
```


Diagram menggambarkan urutan akses yang benar-benar digunakan. Jalur alternatif melalui server FTP di-enumerasi, tetapi **tidak diperlukan** untuk menyelesaikan objective utama.

## Ringkasan tahapan
| Tahap | Teknik | Bukti ringkas |
|---|---|---|
| 1. Initial access | SSH melalui VPS ke Kali bantuan | Kali memperoleh konektivitas ke Samba/FTP |
| 2. Reconnaissance | Nmap, smbclient, smbmap, curl | Share profiles dapat dibaca/ditulis anonim |
| 3. Exploitation | Samba is_known_pipename / CVE-2017-7494 | Command shell pada Samba, uid=0(root) |
| 4. Routing | Host route /32 via router yang diizinkan | Ping target utama berhasil (0% packet loss) |
| 5. Internal reconnaissance | TCP service/version scan | FTP 21, SSH 22, HTTP 80 pada target utama |
| 6. Web file read | Path traversal melalui parameter page | Konten /etc/passwd tampil pada HTTP response |
| 7. Credentials | Wordlist SSH (Hydra, terbatas) | Satu akun valid ditemukan |
| 8. Lateral access | SSH interaktif dari Samba | uid=1001 (akun lab) pada target utama |
| 9. Privilege escalation | sudo -l dan sudo -i | Hak sudo (ALL : ALL) ALL; akses root |
| 10. Objective | Baca /opt/pass.txt | Flag berhasil didapat; disimpan di luar repository |

## Dokumen detail

- [Technical walkthrough](TECHNICAL-WALKTHROUGH.md) — catatan langkah, output penting, kendala, dan interpretasi.
- [Findings & mitigations](FINDINGS-AND-MITIGATIONS.md) — temuan, dampak, akar masalah yang terbukti vs dugaan, serta saran perbaikan.
- [Evidence checklist](EVIDENCE-CHECKLIST.md) — daftar screenshot yang perlu dipilih dan disensor sebelum publikasi.

## Batasan klaim

- **Eksploitasi RCE Samba terbukti** oleh root shell pada host Samba; pemeriksaan versi saja sebelumnya tidak cukup.
- **Path traversal / local file read terbukti** melalui konten /etc/passwd. Implementasi PHP belum dilihat, sehingga mekanisme internal include() belum dipastikan.
- **Routing /32 dari Samba** menunjukkan jalur via router yang diizinkan; ini bukan bukti bahwa kontrol whitelist dapat dilewati dari Kali langsung.
- **ProFTPD** pada target utama hanya di-enumerasi dan **tidak dieksploitasi** dalam jalur penyelesaian.
- **Privilege escalation** menggunakan hak sudo yang tersedia pada akun hasil login; tidak ada bukti eksploitasi kernel.

## Kebijakan keamanan publikasi

Jangan commit password nyata, file wordlist ujian, private key, nilai flag, screenshot yang masih menampilkan kredensial, IP/hostname lab asli, alamat VPS, atau tautan grup privat. Bukti mentah disimpan privat, sedangkan versi publik diberi redaksi. Lihat [evidence checklist](EVIDENCE-CHECKLIST.md).

## Kesimpulan

Exam 02 membuktikan bahwa kelemahan berlapis dapat membentuk jalur serangan lengkap: akses SMB yang tidak aman, eksekusi kode di server perantara, keterjangkauan jaringan internal, pembacaan file melalui aplikasi web, kredensial lemah, dan izin sudo luas. Pelajaran utama: risiko timbul dari **kombinasi kontrol yang gagal**, bukan semata-mata satu CVE.

