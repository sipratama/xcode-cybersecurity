# Lab 04 — Mobile Static and Dynamic Analysis

[Overview Exam 3](../README.md) · [Evidence](../evidence/README.md)

**Challenge ID:** MOB-01 · **Current Status:** NOT STARTED

## 1. Overview

Rencana analisis APK secara statis dan dinamis. **Not Verified:** aplikasi mengekspos data sensitif atau memiliki kelemahan autentikasi/backend.

## 2. Learning Objectives

- Memahami perbedaan inspeksi tanpa eksekusi dan observasi runtime.
- Menelusuri input, penyimpanan, komponen Android, dan request backend berdasarkan bukti.

## 3. Target Information

`https://hackerlab.biz.id/login.apk` — alamat artefak dari SRC-02. APK, hash, package name, versi, dan backend aktual belum dianalisis dalam pembaruan ini.

## 4. Tools

Rencana: JADX, apktool, MobSF, ADB, emulator Android terisolasi, dan proxy manual. Belum ada penggunaan tool yang dilaporkan.

## 5. Methodology

Catat sumber dan hash APK, periksa manifest/permission/exported components serta aliran data secara statis. Lanjutkan dynamic analysis pada emulator terisolasi untuk mengamati login, penyimpanan, dan traffic sesuai scope. Indikasi pada dekompilasi perlu validasi runtime; tidak menjalankan APK di ponsel utama.

## 6. Test Cases

| ID rencana | Pemeriksaan | Status |
|---|---|---|
| MOB-T01 | Integritas APK dan konfigurasi manifest | Belum dijalankan |
| MOB-T02 | Static data flow, penyimpanan, dan komunikasi | Belum dijalankan |
| MOB-T03 | Perilaku login dan traffic pada emulator | Belum dijalankan |

## 7. Results

Belum ada hasil aktual atau kelemahan APK/backend yang terbukti.

## 8. Evidence

[SRC-02](../evidence/README.md#1-register-sumber) hanya menyediakan target. Belum ada APK, hash, hasil dekompilasi, runtime log, atau screenshot. Sensor secret yang mungkin ditemukan sebelum publikasi.

## 9. Findings

Tidak ada temuan terkonfirmasi. **Not Verified:** input login mencapai query yang tidak aman; string pada APK saja tidak membuktikan SQL injection.

## 10. Mitigation

Rekomendasi umum: minimalkan permission dan exported components, lindungi penyimpanan lokal, hindari secret tertanam, validasi TLS, dan tegakkan validasi input/otorisasi pada backend. Gunakan parameterized query bila backend memakai SQL.

## 11. Lessons Learned

Fokus pembelajaran: temuan statis menunjukkan area investigasi, sedangkan dampak perlu bukti yang relevan. Belum ada pembelajaran empiris dari APK ini.

## 12. Current Status

**NOT STARTED** per 17 November 2026. Langkah berikutnya: memperoleh APK beserta hash dan memulai inventaris statis dalam lingkungan terisolasi.
