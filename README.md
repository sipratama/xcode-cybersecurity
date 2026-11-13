# XCODE Pentest Learning Notes

Dokumentasi pembelajaran **penetration testing pada laboratorium XCODE/HackersBootcamp yang diizinkan**. Catatan meliputi recon, web security, networking, Linux, exploit validation, dan mitigasi.

## Indeks Exam

| Exam | Fokus | Status dokumentasi |
|---|---|---|
| [Exam 01](exam-01/README.md) | Kumpulan lab aplikasi web, API, SSH, dan autentikasi | Catatan lab tersedia; lihat status di folder |
| [Exam 02](exam-02/README.md) | Enterprise Cyber Range: exploit chaining, routing, lateral access, privilege escalation | **Completed — 13 November 2026** |

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
└── exam-02/
    ├── README.md
    ├── TECHNICAL-WALKTHROUGH.md
    ├── FINDINGS-AND-MITIGATIONS.md
    └── EVIDENCE-CHECKLIST.md
```

## Cara Membaca Dokumentasi

Mulai dari README masing-masing exam untuk tujuan, batasan scope, dan ringkasan hasil. Gunakan technical walkthrough untuk memahami urutan observasi; findings untuk risiko dan mitigasi; dan evidence checklist untuk screenshot yang dapat dibagikan dengan aman.

Untuk menampilkan Markdown pada VS Code, gunakan **Ctrl+Shift+V** (Windows/Linux) atau **Cmd+Shift+V** (macOS).

## Etika dan Keamanan

Semua pengujian dalam repo ini diceritakan dalam konteks **cyber range dengan otorisasi**. Materi tidak boleh dipakai untuk menyerang sistem lain. Repo bersifat publik: jangan mengunggah password, secret, flag, invite link privat, IP internal aktif, host akses lab, token, private key, atau screenshot yang menampilkannya. Gunakan placeholder seperti `[TARGET_IP]` dan simpan bukti mentah secara privat.
