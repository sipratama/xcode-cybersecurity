# XCODE Pentest Learning Notes

Repository ini berisi dokumentasi hasil belajar penetration testing dari rangkaian exam XCODE.

## Struktur Exam

- Total exam: **12**
- Exam 01: **10 lab**
- Status saat ini: **Lab 00 sampai Lab 05 selesai didokumentasikan**

## Struktur Repository

```text
xcode-pentest-notes/
├── README.md
├── .gitignore
└── exam-01/
    ├── README.md
    ├── lab-00-ssh-tunneling/
    ├── lab-01-path-traversal/
    ├── lab-02-sql-injection/
    ├── lab-03-idor/
    ├── lab-04-dictionary-attack/
    └── lab-05-arbitrary-file-read/
```

Setiap lab menggunakan format Markdown dan memiliki folder `images/` untuk menyimpan screenshot.

## Workflow Dokumentasi

1. Buka repository di VS Code.
2. Edit file `README.md` pada lab yang sedang dikerjakan.
3. Simpan screenshot ke folder `images/` pada lab terkait.
4. Tampilkan screenshot di Markdown dengan format:

```md
![Deskripsi screenshot](images/nama-file.png)
```

5. Gunakan Markdown Preview di VS Code:
   - Windows/Linux: `Ctrl + Shift + V`
   - macOS: `Cmd + Shift + V`

## Catatan Keamanan

Dokumentasi ini dibuat untuk pembelajaran pada lab yang memang memiliki izin pengujian. Sebelum repository dipublikasikan, sensor atau hapus informasi sensitif seperti password, token, invite link privat, host internal, atau credential lab.
