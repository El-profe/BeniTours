Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead('c:\laragon\www\benitours_cpanel.zip')
$found = $zip.Entries | Where-Object { $_.FullName -match 'CatalogoController' }
foreach ($e in $found) {
    Write-Output $e.FullName
}
$zip.Dispose()
