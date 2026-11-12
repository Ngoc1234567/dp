$ErrorActionPreference = 'Stop'
$projectPath = $PSScriptRoot
$runtimePath = Join-Path (Split-Path $projectPath -Parent) '.runtime'
if (!(Test-Path (Join-Path $runtimePath 'mysql/my.ini'))) { throw 'Chưa có database local. Xem README-WORDPRESS.md.' }
if (!(Get-NetTCPConnection -LocalPort 3307 -State Listen -ErrorAction SilentlyContinue)) {
 Start-Process 'C:\xampp\mysql\bin\mysqld.exe' -ArgumentList "--defaults-file=$runtimePath\mysql\my.ini",'--bind-address=127.0.0.1','--port=3307','--console' -WindowStyle Hidden -RedirectStandardOutput "$runtimePath\mysql-out.log" -RedirectStandardError "$runtimePath\mysql-error.log"
}
if (!(Get-NetTCPConnection -LocalPort 8080 -State Listen -ErrorAction SilentlyContinue)) {
 Start-Process 'C:\xampp\php\php.exe' -ArgumentList '-S','localhost:8080','-t',$projectPath -WindowStyle Hidden -RedirectStandardOutput "$runtimePath\php-out.log" -RedirectStandardError "$runtimePath\php-error.log"
}
Write-Output 'Website: http://localhost:8080 | Admin: http://localhost:8080/wp-admin/'
