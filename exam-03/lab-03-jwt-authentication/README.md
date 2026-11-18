# Lab 03 — JWT Authentication Testing

[Overview Exam 3](../README.md) · [Evidence](../evidence/README.md)

**Challenge ID:** JWT-01 · **Current Status:** NOT STARTED

## 1. Overview

Rencana evaluasi autentikasi dan otorisasi berbasis JWT. **Not Verified:** implementasi memiliki kelemahan validasi token; format JWT saja tidak membuktikan kelemahan.

## 2. Learning Objectives

- Membedakan decoding token dari verifikasi signature dan klaim.
- Memahami validasi algoritma, masa berlaku, issuer/audience sesuai kontrak aplikasi, dan otorisasi role di server.

## 3. Target Information

`https://app.hackerlab.biz.id/jwt.php` — endpoint dari SRC-02. Respons, mekanisme login aktual, token, dan endpoint terlindungi belum diberikan. Tidak ada kredensial yang dicantumkan.

## 4. Tools

Rencana: curl, jq, browser Developer Tools, dan decoder JWT offline. Belum ada penggunaan tool yang dilaporkan; jangan mengirim token aktif ke decoder publik.

## 5. Methodology

Konfirmasi kontrak API dan akses lab, rekam baseline anonymous, lalu gunakan akun lab yang disediakan secara privat bila diizinkan. Inspeksi token secara offline dan evaluasi penolakan token tidak valid serta kontrol akses resource berdasarkan bukti respons.

## 6. Test Cases

| ID rencana | Pemeriksaan | Status |
|---|---|---|
| JWT-T01 | Baseline anonymous dan respons autentikasi yang sah | Belum dijalankan |
| JWT-T02 | Validasi integritas, algoritma, dan masa berlaku | Belum dijalankan |
| JWT-T03 | Pemisahan autentikasi dan otorisasi resource | Belum dijalankan |

## 7. Results

Belum ada hasil aktual, token yang diamati, atau akses resource yang terbukti.

## 8. Evidence

[SRC-02](../evidence/README.md#1-register-sumber) hanya menyediakan target. Belum ada request/response, token tersensor, atau screenshot. Rahasiakan signature/token aktif dan kredensial.

## 9. Findings

Tidak ada temuan terkonfirmasi. **Not Verified:** perubahan klaim tanpa signature valid dapat diterima; jangan menganggap decoding berarti token dapat dipalsukan.

## 10. Mitigation

Rekomendasi umum: verifikasi signature dengan key dan algoritma yang ditetapkan server, validasi klaim sesuai kontrak, lindungi key, dan periksa otorisasi setiap resource pada server. Belum ada root cause target yang terverifikasi.

## 11. Lessons Learned

Fokus pembelajaran: struktur token dan klaim dapat dibaca tanpa memberi jaminan autentisitas. Belum ada pembelajaran empiris dari target.

## 12. Current Status

**NOT STARTED** per 17 November 2026. Langkah berikutnya: memperoleh baseline API dan kontrak autentikasi tanpa memublikasikan secret.
