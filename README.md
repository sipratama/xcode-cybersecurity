# XCODE Pentest Learning Notes

Dokumentasi pembelajaran **penetration testing pada laboratorium XCODE/HackersBootcamp yang diizinkan**. Catatan meliputi recon, web security, networking, Linux, exploit validation, dan mitigasi.

## Indeks Exam

| Exam | Fokus | Status dokumentasi |
|---|---|---|
| [Exam 01](exam-01/README.md) | Kumpulan lab aplikasi web, API, SSH, dan autentikasi | Catatan lab tersedia; lihat status di folder |
| [Exam 02](exam-02/README.md) | Enterprise Cyber Range: exploit chaining, routing, lateral access, privilege escalation | **Completed — 13 November 2026** |
| [Exam 03](exam-03/README.md) | Full-Stack Offensive Security Simulation Lab; fokus awal HTTP Header Manipulation | **IN PROGRESS — NOT SOLVED · dokumentasi 17 November 2026** |

Program direncanakan mencakup 12 exam; folder dibuat sesuai pembelajaran yang benar-benar didokumentasikan. README Exam 01 memiliki progres tersendiri dan tidak diubah dalam pembaruan ini.

## Struktur Repository

```text
xcode-cybersecurity/
├── README.md
├── .gitignore
├── exam-01/
│   ├── README.md
│   ├── TEMPLATE-LAB.md
│   └── lab-*/
├── exam-02/
│   ├── README.md
│   ├── TECHNICAL-WALKTHROUGH.md
│   ├── FINDINGS-AND-MITIGATIONS.md
│   └── EVIDENCE-CHECKLIST.md
└── exam-03/
    ├── README.md
    ├── XCODE_Exam_03_Guide.md
    ├── lab-*/README.md
    └── evidence/README.md
```

## Cara Membaca Dokumentasi

Mulai dari README masing-masing exam untuk tujuan, batasan scope, dan ringkasan hasil. Gunakan technical walkthrough untuk memahami urutan observasi; findings untuk risiko dan mitigasi; dan evidence checklist untuk screenshot yang dapat dibagikan dengan aman.

Untuk menampilkan Markdown pada VS Code, gunakan **Ctrl+Shift+V** (Windows/Linux) atau **Cmd+Shift+V** (macOS).

Untuk Exam 03, mulai dari [overview dan indeks challenge](exam-03/README.md). Laporan [HTTP Header Manipulation](exam-03/lab-01-http-header-manipulation/README.md) mencatat HTTP-01–HTTP-15, HTTP-17, dan ENV-01, termasuk listing, masalah PATH, serta redirect HTTPS → HTTP → HTTPS yang berakhir 403. Gunakan [learning journal](exam-03/lab-01-http-header-manipulation/TEST-LOG.md) untuk command dan penjelasan per test, [glosarium](exam-03/lab-01-http-header-manipulation/GLOSSARY.md) untuk konsep dasar, serta [register evidence](exam-03/evidence/README.md) untuk sumber dan keterbatasan.

## Etika dan Keamanan

Semua pengujian dalam repo ini diceritakan dalam konteks **cyber range dengan otorisasi**. Materi tidak boleh dipakai untuk menyerang sistem lain. Repo bersifat publik: jangan mengunggah password, secret, flag, invite link privat, IP internal aktif, host akses lab, token, private key, atau screenshot yang menampilkannya. Gunakan placeholder seperti `[TARGET_IP]` dan simpan bukti mentah secara privat.

Pengecualian scope dokumentasi Exam 03: alamat target yang secara eksplisit diberikan dan diminta pengguna dicatat pada inventaris lab. Ini tidak memverifikasi konektivitas atau memberi izin pengujian di luar scope; alamat akses privat lain dan rahasia tetap disensor.

Progres terbaru HTTP-18: **OBSERVED:** parent PHP tetap HTTP/2 403 dengan header menyerupai browser; bypass belum terbukti. [Detail learning journal](exam-03/lab-01-http-header-manipulation/TEST-LOG.md#http-18--browser-like-request-headers). X-Original-URL adalah **NEXT STEP**, belum dijalankan.
