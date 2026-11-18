# Lab 02 — Wi-Fi Password Cracking (WPA2 Handshake)

[Overview Exam 3](../README.md) · [Evidence](../evidence/README.md)

**Challenge ID:** WIFI-01 · **Current Status:** NOT STARTED

## 1. Overview

Rencana pembelajaran analisis capture WPA2 dan ketahanan passphrase terhadap pengujian offline. **Not Verified:** capture berisi handshake yang lengkap dan dapat digunakan.

## 2. Learning Objectives

- Memahami frame EAPOL, kelengkapan handshake, dan batas analisis offline.
- Membedakan kandidat yang tidak cocok dari capture yang tidak memadai.

## 3. Target Information

`https://hackerlab.biz.id/wifi-01.cap` — alamat artefak dari SRC-02. Berkas belum diunduh/dianalisis dalam pembaruan ini; hash dan metadata belum tersedia.

## 4. Tools

Rencana: Wireshark/tshark, capinfos, aircrack-ng atau hashcat sesuai format dan dukungan lingkungan. Belum ada penggunaan tool yang dilaporkan.

## 5. Methodology

Setelah scope terkonfirmasi, dapatkan capture dari penyelenggara, catat SHA-256, periksa metadata/frame EAPOL, lalu nilai kelayakan analisis offline. Pengujian kandidat hanya pada artefak lab; tidak melakukan capture aktif/deauthentication terhadap jaringan lain.

## 6. Test Cases

| ID rencana | Pemeriksaan | Status |
|---|---|---|
| WIFI-T01 | Integritas dan metadata capture | Belum dijalankan |
| WIFI-T02 | Kelengkapan handshake dan parameter jaringan | Belum dijalankan |
| WIFI-T03 | Pengujian kandidat offline jika data memadai dan diizinkan | Belum dijalankan |

## 7. Results

Belum ada hasil aktual. SSID/BSSID, handshake, dan passphrase tidak dinyatakan diketahui.

## 8. Evidence

[SRC-02](../evidence/README.md#1-register-sumber) hanya menyediakan target. Belum ada capture, hash, log tool, atau screenshot dalam repository. Simpan passphrase dan bukti mentah secara privat.

## 9. Findings

Tidak ada temuan terkonfirmasi. **Not Verified:** passphrase dapat ditemukan melalui kandidat yang tersedia; belum ada pengujian untuk mendukung dugaan tersebut.

## 10. Mitigation

Rekomendasi umum: gunakan passphrase kuat dan unik, nonaktifkan WPS yang tidak diperlukan, serta evaluasi WPA3 sesuai dukungan perangkat. Tidak ada konfigurasi target yang telah diperiksa.

## 11. Lessons Learned

Fokus pembelajaran: kualitas capture menentukan validitas pengujian; kandidat yang gagal tidak membuktikan passphrase kuat. Belum ada pembelajaran empiris dari pengujian lab ini.

## 12. Current Status

**NOT STARTED** per 17 November 2026. Langkah berikutnya: memperoleh metadata artefak dan mengonfirmasi kelengkapan handshake.
