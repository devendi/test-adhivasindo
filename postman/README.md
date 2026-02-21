# Postman Collection

## Import Collection
1. Buka Postman.
2. Klik **Import**.
3. Pilih file `postman/adhivasindo.postman_collection.json`.
4. Collection **Adhivasindo API** akan muncul di sidebar.

## Import Environment
1. Klik **Import** di Postman.
2. Pilih file `postman/adhivasindo.postman_environment.json`.
3. Pilih environment **Adhivasindo Local** di kanan atas Postman sebelum menjalankan request.
4. Pastikan nilai `base_url` sesuai host API lokal Anda (default: `http://127.0.0.1:8000`).

## Cara Pakai Token
1. Jalankan request **Login** terlebih dahulu.
2. Script test pada request Login akan membaca token dari response JSON lalu menyimpannya ke variable `token`.
3. Semua request lain sudah memakai header `Authorization: Bearer {{token}}`.
4. Jika token expired, jalankan ulang request **Login**.