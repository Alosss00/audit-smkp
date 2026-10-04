$backupDir = 'c:\laragon\www\audit\backup_blade_views'
$viewsDir  = 'c:\laragon\www\audit\resources\views'

# 1. First copy matching converted native .php files from resources/views into backup_blade_views with .php extension
Get-ChildItem -Path $backupDir -Recurse -File | ForEach-Object {
    $oldPath = $_.FullName
    $name = $_.Name
    
    # Calculate target .php filename
    $newName = $name -replace '\.blade\.php\.bak$', '.php' -replace '\.blade\.blade\.php\.bak$', '.php' -replace '\.blade\.php$', '.php'
    $targetPath = Join-Path $_.DirectoryName $newName
    
    # Check if there is a corresponding native .php in resources/views
    $relPath = $_.FullName.Substring($backupDir.Length)
    $relPhp = $relPath -replace '\.blade\.php\.bak$', '.php' -replace '\.blade\.blade\.php\.bak$', '.php' -replace '\.blade\.php$', '.php'
    $sourcePhp = Join-Path $viewsDir $relPhp
    
    if (Test-Path $sourcePhp) {
        Copy-Item -Path $sourcePhp -Destination $targetPath -Force
        if ($oldPath -ne $targetPath) {
            Remove-Item -Path $oldPath -Force
        }
        Write-Host "Replaced & Renamed: $name -> $newName"
    } else {
        # If no direct match in resources/views (e.g. inside _smkp_relasi_feature backup), rename to .php
        if ($oldPath -ne $targetPath) {
            Move-Item -Path $oldPath -Destination $targetPath -Force
            Write-Host "Renamed: $name -> $newName"
        }
    }
}
