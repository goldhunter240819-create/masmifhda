# =========================================================================
# AUTO BACKUP SCRIPT UNTUK MASMIFHDA (DATABASE + SOURCE CODE)
# =========================================================================

# 1. KONFIGURASI LOKASI
$BackupDir = "I:\Drive Saya\Backup_masmifhda"
$SourceCodeDir = "c:\xampp\htdocs\masmifhda"
$MySQLBin = "c:\xampp\mysql\bin\mysqldump.exe"
$RetentionDays = 3

# 2. MEMBUAT FOLDER HARIAN
$DateStr = Get-Date -Format "yyyy-MM-dd"
$DailyBackupDir = Join-Path -Path $BackupDir -ChildPath "Backup_$DateStr"

if (-not (Test-Path -Path $DailyBackupDir)) {
    New-Item -ItemType Directory -Path $DailyBackupDir | Out-Null
    Write-Host "Dibuat folder backup harian: $DailyBackupDir"
}

# 3. BACKUP DATABASE (DUMP)
$Databases = @("aula_db", "aula_siakad_db", "aula_absen_db", "aula_absensi_db")

foreach ($db in $Databases) {
    $OutputFile = Join-Path -Path $DailyBackupDir -ChildPath "$db.sql"
    Write-Host "Sedang membackup database $db ..."
    
    # Eksekusi mysqldump (Asumsi user root, tanpa password)
    $args = @("-u", "root", "$db")
    & $MySQLBin $args | Out-File -FilePath $OutputFile -Encoding utf8
}

# 4. BACKUP SOURCE CODE (ZIP)
$ZipFile = Join-Path -Path $DailyBackupDir -ChildPath "masmifhda_source.zip"
Write-Host "Sedang mengompres source code ke dalam file ZIP. Proses ini mungkin memakan waktu beberapa menit..."
if (Test-Path -Path $ZipFile) { Remove-Item -Path $ZipFile -Force }

# Menggunakan System.IO.Compression.ZipFile (Jauh lebih cepat dan kuat untuk file besar daripada Compress-Archive)
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($SourceCodeDir, $ZipFile, [System.IO.Compression.CompressionLevel]::Optimal, $false)
Write-Host "Backup source code selesai!"

# 5. HAPUS BACKUP LAMA (Auto-Cleanup)
Write-Host "Mencari dan menghapus backup yang lebih tua dari $RetentionDays hari..."
Get-ChildItem -Path $BackupDir -Directory | Where-Object {
    $_.Name -match "^Backup_\d{4}-\d{2}-\d{2}$" -and $_.CreationTime -lt (Get-Date).AddDays(-$RetentionDays)
} | Remove-Item -Recurse -Force

Write-Host "========================================="
Write-Host "PROSES BACKUP SELESAI!"
Write-Host "========================================="
