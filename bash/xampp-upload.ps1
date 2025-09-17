# Skrypt PowerShell do kopiowania wszystkich plików do htdocs XAMPP

# Źródło – folder web obok skryptu
$source = Join-Path $PSScriptRoot '..\web'

# Cel – htdocs XAMPP
$destination = 'C:\xampp\htdocs'

# Kopiowanie wszystkich plików i podfolderów, nadpisywanie istniejących
Copy-Item -Path "$source\*" -Destination $destination -Recurse -Force

Write-Host "Kopiowanie zakonczone."
