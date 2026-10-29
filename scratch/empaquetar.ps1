$dest = "c:\laragon\www\benitours_cpanel.zip"
if (Test-Path $dest) { Remove-Item $dest -Force }

$tempDir = Join-Path $env:TEMP ("benitours_pkg_" + [System.Guid]::NewGuid().ToString("N"))
New-Item -ItemType Directory -Path $tempDir | Out-Null

$items = @("app", "bin", "config", "public", "routes", "storage", "index.php", ".htaccess", ".env")

foreach ($it in $items) {
    $src = Join-Path "c:\laragon\www\benitours" $it
    if (Test-Path $src) {
        Copy-Item -Path $src -Destination $tempDir -Recurse -Force
    }
}

$backupDir = Join-Path $tempDir "storage\uploads\lugares_backup_raw"
if (Test-Path $backupDir) { Remove-Item $backupDir -Recurse -Force }
$logsDir = Join-Path $tempDir "storage\logs"
if (Test-Path $logsDir) { Get-ChildItem $logsDir -File | Remove-Item -Force }

Compress-Archive -Path (Join-Path $tempDir "*") -DestinationPath $dest -CompressionLevel Optimal
Remove-Item $tempDir -Recurse -Force

$zipInfo = Get-Item $dest
Write-Output ("ZIP_CREADO: " + $zipInfo.FullName + " (" + [Math]::Round($zipInfo.Length / 1MB, 2) + " MB)")
