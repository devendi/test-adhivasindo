# Database Backup

Folder `db/` dipakai untuk menyimpan SQL dump database (backup).

## Generate backup di Windows (XAMPP)
```bash
"C:\drive_D\xampp\mysql\bin\mysqldump.exe" -u root adhivasindo_api > db\backup.sql
```

## Generate backup di Linux
```bash
mysqldump -u root -p adhivasindo_api > db/backup.sql
```