# Lab 07 — Docker Container Escape

[Overview Exam 3](../README.md) · [Evidence](../evidence/README.md)

**Challenge ID:** MAIN-01 · **Current Status:** NOT STARTED

## 1. Overview

Rencana mempelajari isolasi container dan prasyarat container escape pada target utama. **Not Verified:** isolasi Docker dapat dilanggar pada konfigurasi lab ini.

## 2. Learning Objectives

- Membedakan user/namespace container dengan user dan privilege host.
- Menghubungkan konfigurasi runtime, mount, capabilities, dan versi dengan bukti risiko.
- Memisahkan kebijakan jaringan dari akses proses dan dampak host.

## 3. Target Information

`192.168.9.122` — target utama dari SRC-02. **Not Verified:** hanya Web Server `192.168.9.241` dan CMS Server `192.168.9.245` memiliki jalur Allow; Kali `192.168.9.242` ditolak menurut diagram. OS, runtime, versi, konektivitas, dan konteks container aktual belum dikonfirmasi.

## 4. Tools

Rencana: inventaris konfigurasi melalui akses lab yang sah dan pembacaan metadata runtime/namespace yang diizinkan. Belum ada penggunaan tool yang dilaporkan; tidak ada exploit atau CVE yang diasumsikan berlaku.

## 5. Methodology

Konfirmasi akses sah melalui jalur lab yang diizinkan, catat sumber koneksi, lalu identifikasi konteks proses/container. Evaluasi konfigurasi dan versi berdasarkan bukti serta advisori resmi jika relevan. Dampak host memerlukan bukti terpisah; root pada container tidak cukup. Tidak ada tahap yang dijalankan dalam pembaruan ini.

## 6. Test Cases

| ID rencana | Pemeriksaan | Status |
|---|---|---|
| MAIN-T01 | Prasyarat akses dan jalur Allow dari sumber yang sah | Belum dijalankan |
| MAIN-T02 | Identitas lingkungan dan konfigurasi isolasi | Belum dijalankan |
| MAIN-T03 | Penilaian risiko konfigurasi/versi dan dampak minimum jika diizinkan | Belum dijalankan |

## 7. Results

Belum ada hasil aktual, akses container, maupun bukti dampak host.

## 8. Evidence

[SRC-02](../evidence/README.md#1-register-sumber) hanya menyediakan target dan kebijakan yang dilaporkan. Belum ada output konfigurasi, versi, namespace, log, atau screenshot.

## 9. Findings

Tidak ada temuan terkonfirmasi. **Not Verified:** konfigurasi runtime atau kerentanan komponen memungkinkan escape. Jangan membuat nomor CVE, payload, atau klaim host compromise tanpa bukti.

## 10. Mitigation

Rekomendasi umum: hindari privileged container, minimalkan capabilities/mount, lindungi socket runtime, gunakan non-root/rootless sesuai kebutuhan, aktifkan seccomp dan AppArmor/SELinux yang sesuai, patch host/runtime, dan terapkan segmentasi jaringan.

## 11. Lessons Learned

Fokus pembelajaran: akses internal, root container, dan kontrol host merupakan klaim terpisah dengan kebutuhan bukti masing-masing. Belum ada pembelajaran empiris dari target.

## 12. Current Status

**NOT STARTED** per 17 November 2026. Langkah berikutnya: memastikan prasyarat akses yang sah dan menginventarisasi lingkungan sebelum memilih metode validasi.
