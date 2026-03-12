@echo off
setlocal
set "ROOT=d:\xampp\htdocs\dilgcarlegal\ai_chat"
pushd "%ROOT%"
set "PHP=D:\xampp\php\php.exe"
if not exist "%PHP%" (
  echo PHP not found at %PHP%
  exit /b 1
)
if not exist ".env" (
  copy ".env.example" ".env" >nul
)
findstr /r /c:"^APP_KEY=.+" ".env" >nul || "%PHP%" artisan key:generate --force
"%PHP%" artisan migrate --force
if /i not "%1"=="--no-build" (
  call npm.cmd install
  call npm.cmd run build
)
"%PHP%" artisan serve
popd
