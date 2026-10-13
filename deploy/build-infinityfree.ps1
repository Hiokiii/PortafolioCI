$ErrorActionPreference = 'Stop'
$projectRoot = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$buildRoot = Join-Path $projectRoot ('dist/infinityfree-' + (Get-Date -Format 'yyyyMMdd-HHmmss'))
$siteRoot = Join-Path $buildRoot 'htdocs'
New-Item -ItemType Directory -Path $siteRoot -Force | Out-Null

# Rebuild only production dependencies in a separate directory.
Copy-Item -LiteralPath (Join-Path $projectRoot 'composer.json') -Destination $buildRoot
Copy-Item -LiteralPath (Join-Path $projectRoot 'composer.lock') -Destination $buildRoot
& (Join-Path $projectRoot '.tools/php/php.exe') (Join-Path $projectRoot '.tools/composer.phar') install --working-dir=$buildRoot --no-dev --prefer-dist --optimize-autoloader --no-interaction
if ($LASTEXITCODE -ne 0) { throw 'Production dependency installation failed' }

$excludedAssets = @('Background5.mp4', 'plugins/pdfmake/pdfmake.js', 'plugins/pdfmake/pdfmake.min.js', 'plugins/jqvmap/maps/jquery.vmap.usa.counties.js')
foreach ($directory in @('app', 'public', 'vendor')) {
    $sourceRoot = if ($directory -eq 'vendor') { Join-Path $buildRoot 'vendor' } else { Join-Path $projectRoot $directory }
    Get-ChildItem -LiteralPath $sourceRoot -File -Recurse -Force | ForEach-Object {
        $relative = [IO.Path]::GetRelativePath($sourceRoot, $_.FullName).Replace([char]92, [char]47)
        if ($relative -match '(^|/)\.git(/|$)') { return }
        if ($directory -eq 'public' -and $relative -in $excludedAssets) { return }
        if ($directory -eq 'vendor' -and $relative -match '^codeigniter4/framework/(tests|user_guide_src|app|public|writable)/') { return }
        $destination = Join-Path $siteRoot ($directory + '/' + $relative)
        New-Item -ItemType Directory -Path ([IO.Path]::GetDirectoryName($destination)) -Force | Out-Null
        Copy-Item -LiteralPath $_.FullName -Destination $destination
    }
}
foreach ($file in @('index.php', '.htaccess', 'favicon.ico', 'robots.txt')) {
    Copy-Item -LiteralPath (Join-Path $projectRoot $file) -Destination $siteRoot
}
# Copy the template, never the developer's real .env.
Copy-Item -LiteralPath (Join-Path $PSScriptRoot 'infinityfree.env.example') -Destination (Join-Path $siteRoot '.env')
New-Item -ItemType Directory -Path (Join-Path $siteRoot 'writable') -Force | Out-Null
Copy-Item -LiteralPath (Join-Path $projectRoot 'writable/.htaccess') -Destination (Join-Path $siteRoot 'writable/.htaccess')
foreach ($directory in @('cache', 'logs', 'session', 'uploads')) {
    New-Item -ItemType Directory -Path (Join-Path $siteRoot ('writable/' + $directory)) -Force | Out-Null
    [IO.File]::WriteAllText((Join-Path $siteRoot ('writable/' + $directory + '/index.html')), '')
}

$files = @(Get-ChildItem -LiteralPath $siteRoot -File -Recurse -Force | Sort-Object FullName)
foreach ($file in $files) {
    $limit = if ($file.Name -eq '.htaccess') { 10000 } elseif ($file.Extension -in @('.php', '.html', '.js')) { 1000000 } else { 10000000 }
    if ($file.Length -gt $limit) { throw ('Hosting file size limit exceeded: ' + $file.FullName) }
}

# ZIP entries are relative to htdocs, and each part extracts into the same folder.
Add-Type -AssemblyName System.IO.Compression
$part = 0
$archive = $null
$stream = $null
try {
    foreach ($file in $files) {
        if ($null -eq $archive -or $stream.Length + $file.Length + 1024 -gt 9000000) {
            if ($null -ne $archive) { $archive.Dispose(); $stream.Dispose() }
            $part++
            $zipPath = Join-Path $buildRoot ('los-alces-parte-{0:D2}.zip' -f $part)
            $stream = [IO.File]::Open($zipPath, [IO.FileMode]::CreateNew)
            $archive = [IO.Compression.ZipArchive]::new($stream, [IO.Compression.ZipArchiveMode]::Create, $true)
        }
        $name = [IO.Path]::GetRelativePath($siteRoot, $file.FullName).Replace([char]92, [char]47)
        $entry = $archive.CreateEntry($name, [IO.Compression.CompressionLevel]::Optimal)
        $entryStream = $entry.Open()
        $sourceStream = [IO.File]::OpenRead($file.FullName)
        try { $sourceStream.CopyTo($entryStream) } finally { $sourceStream.Dispose(); $entryStream.Dispose() }
    }
} finally {
    if ($null -ne $archive) { $archive.Dispose() }
    if ($null -ne $stream) { $stream.Dispose() }
}
foreach ($zip in Get-ChildItem -LiteralPath $buildRoot -Filter '*.zip') {
    if ($zip.Length -ge 10000000) { throw ('ZIP exceeds hosting upload size: ' + $zip.Name) }
}
Write-Output $buildRoot
Get-ChildItem -LiteralPath $buildRoot -Filter '*.zip' | Select-Object Name, Length
