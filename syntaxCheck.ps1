cls
$pathToCheck = "C:\xampp\htdocs"
$phpExePath = "C:\xampp\php\php.exe"

Get-ChildItem $pathToCheck -Filter "*.php" -Recurse | foreach {
$pinfo = New-Object System.Diagnostics.ProcessStartInfo
$pinfo.FileName = $phpExePath
$pinfo.Arguments = "-l", $_.FullName
$pinfo.RedirectStandardError = $true
$pinfo.RedirectStandardOutput = $true
$pinfo.UseShellExecute = $false
$p = New-Object System.Diagnostics.Process
$p.StartInfo = $pinfo
$p.Start() | Out-Null
$p.WaitForExit()
$output = $p.StandardOutput.ReadToEnd()
$output += $p.StandardError.ReadToEnd()
$output
}
$Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")