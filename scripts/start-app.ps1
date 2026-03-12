param([switch]$NoBuild)
$ErrorActionPreference='Stop'
$root = Split-Path -Path $PSCommandPath -Parent
$root = Split-Path -Path $root -Parent
Set-Location $root
$php = 'D:\xampp\php\php.exe'
if (!(Test-Path $php)) { Write-Error 'PHP not found at D:\xampp\php\php.exe'; exit 1 }
if (-not (Select-String -Path '.env' -Pattern '^APP_KEY=.+$' -SimpleMatch -Quiet)) { & $php artisan key:generate --force }
& $php artisan migrate --force
if (-not $NoBuild) { npm.cmd install; npm.cmd run build }
& $php artisan serve
