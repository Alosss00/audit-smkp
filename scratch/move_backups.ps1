$src = 'c:\laragon\www\audit\resources\views'
$dst = 'c:\laragon\www\audit\backup_blade_views'

Get-ChildItem -Path $src -Recurse -Filter '*.blade.php.bak' | ForEach-Object {
    $rel = $_.FullName.Substring($src.Length)
    $targetPath = Join-Path $dst $rel
    $targetDir = Split-Path $targetPath -Parent
    if (!(Test-Path $targetDir)) {
        New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
    }
    Move-Item -Path $_.FullName -Destination $targetPath -Force
    Write-Host "Moved: $($_.Name) -> $targetPath"
}
