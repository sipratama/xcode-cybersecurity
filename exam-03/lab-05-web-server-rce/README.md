# Lab 05 — Web Server Remote Code Execution

[Overview Exam 3](../README.md) · [Evidence](../evidence/README.md)

**Challenge ID:** WEB-01 · **Current Status:** NOT STARTED

## 1. Overview

Rencana analisis keamanan web server sebagai kemungkinan entry point lab. **Not Verified:** terdapat RCE atau akses dari server ini menuju target utama.

## 2. Learning Objectives

- Memisahkan identifikasi layanan dari bukti eksekusi kode.
- Memahami konteks user proses dan prasyarat akses internal.

## 3. Target Information

`192.168.9.241` — Web Server RCE menurut SRC-02. Port, layanan, versi, konektivitas, dan kerentanan aktual belum dikonfirmasi. **Not Verified:** jalur Allow menuju `192.168.9.122` tersedia menurut penyelenggara.

## 4. Tools

Rencana: browser, curl, proxy HTTP manual, dan pencatatan log tersensor setelah scope jaringan jelas. Belum ada penggunaan tool yang dilaporkan.

## 5. Methodology

Konfirmasi jaringan dan layanan melalui informasi lab, dokumentasikan fungsi aplikasi yang tersedia, lalu analisis aliran input berdasarkan bukti. Validasi dampak minimum hanya setelah scope/prasyarat jelas. Bedakan eksekusi kode, shell interaktif, user proses, serta konteks container/host; pembaruan ini tidak menjalankan tahapan tersebut.

## 6. Test Cases

| ID rencana | Pemeriksaan | Status |
|---|---|---|
| WEB-T01 | Ketersediaan layanan dan baseline aplikasi | Belum dijalankan |
| WEB-T02 | Analisis input dan prasyarat potensi RCE | Belum dijalankan |
| WEB-T03 | Validasi dampak minimum dan konteks proses jika diizinkan | Belum dijalankan |

## 7. Results

Belum ada hasil aktual; koneksi, RCE, shell, dan akses internal belum terbukti.

## 8. Evidence

[SRC-02](../evidence/README.md#1-register-sumber) menyediakan alamat dan kebijakan yang dilaporkan. Belum ada request/response, output proses, atau screenshot.

## 9. Findings

Tidak ada temuan terkonfirmasi. **Not Verified:** input aplikasi dapat mencapai fungsi eksekusi kode/perintah tanpa pembatasan memadai.

## 10. Mitigation

Rekomendasi umum: hindari fungsi eksekusi command berbasis input client, gunakan API aman dan validasi ketat, terapkan least privilege, patch dependensi, serta batasi egress/internal access sesuai kebutuhan layanan. Tidak ada root cause target terverifikasi.

## 11. Lessons Learned

Fokus pembelajaran: nama challenge RCE dan jalur Allow bukan bukti eksploitasi atau reachability. Belum ada pembelajaran empiris dari target.

## 12. Current Status

**NOT STARTED** per 17 November 2026. Langkah berikutnya: mengonfirmasi scope layanan dan prasyarat jaringan dari penyelenggara.
