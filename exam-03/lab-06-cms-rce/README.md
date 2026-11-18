# Lab 06 — CMS Remote Code Execution

[Overview Exam 3](../README.md) · [Evidence](../evidence/README.md)

**Challenge ID:** CMS-01 · **Current Status:** NOT STARTED

## 1. Overview

Rencana analisis keamanan CMS dan kemungkinan dampak RCE. **Not Verified:** produk/versi CMS mengandung kelemahan yang dapat dieksploitasi.

## 2. Learning Objectives

- Mengidentifikasi CMS/komponen tanpa mengasumsikan nama produk.
- Memeriksa kecocokan advisori, konfigurasi, dan kebutuhan autentikasi sebelum validasi.

## 3. Target Information

`192.168.9.245` — CMS RCE menurut SRC-02. Produk, versi, port, TLS, dan konektivitas belum dikonfirmasi. **Not Verified:** jalur Allow menuju `192.168.9.122` tersedia menurut penyelenggara.

## 4. Tools

Rencana: browser, curl, proxy manual, dan advisori resmi produk setelah produk teridentifikasi. Belum ada penggunaan tool atau pencocokan CVE yang dilaporkan.

## 5. Methodology

Konfirmasi layanan dalam scope, kumpulkan indikasi produk/versi secara manual, dan cocokkan dengan advisori resmi beserta prasyaratnya. Validasi dampak minimum hanya bila relevan dan diizinkan; jangan menyamakan banner versi dengan kerentanan terbukti.

## 6. Test Cases

| ID rencana | Pemeriksaan | Status |
|---|---|---|
| CMS-T01 | Baseline layanan dan identifikasi CMS | Belum dijalankan |
| CMS-T02 | Kecocokan versi/konfigurasi dengan advisori resmi | Belum dijalankan |
| CMS-T03 | Validasi dampak minimum sesuai scope jika prasyarat terpenuhi | Belum dijalankan |

## 7. Results

Belum ada hasil aktual, produk teridentifikasi, CVE yang cocok, atau RCE terbukti.

## 8. Evidence

[SRC-02](../evidence/README.md#1-register-sumber) menyediakan alamat dan kebijakan yang dilaporkan. Belum ada respons layanan, versi, log, atau screenshot.

## 9. Findings

Tidak ada temuan terkonfirmasi. **Not Verified:** CMS atau komponen tambahan memiliki jalur input menuju eksekusi kode yang tidak aman.

## 10. Mitigation

Rekomendasi umum: patch core/plugin/theme, hapus komponen tidak terpakai, batasi hak administrator, cegah eksekusi file upload, dan jalankan service dengan least privilege serta segmentasi jaringan. Belum ada root cause target terverifikasi.

## 11. Lessons Learned

Fokus pembelajaran: kecocokan nama produk dan versi belum memastikan exploit berlaku; konfigurasi dan autentikasi ikut menentukan. Belum ada pembelajaran empiris dari target.

## 12. Current Status

**NOT STARTED** per 17 November 2026. Langkah berikutnya: mengonfirmasi scope layanan serta identitas CMS tanpa asumsi produk.
