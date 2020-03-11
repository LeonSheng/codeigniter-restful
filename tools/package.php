<?php
define('ROOTPATH', dirname(__DIR__) . '/');
define('APPPATH', ROOTPATH . 'application/');
define('SYSTEMPATH', ROOTPATH . 'system/');
define('OUTPUT', ROOTPATH . 'dist/');

$env = 'production'; //default
$arguments = getopt('', ['env:']);
if (count($arguments) > 0 && key_exists('env', $arguments)) {
    $newEnv = $arguments['env'];
    if ($newEnv === 'development' || $newEnv === 'testing')
        $env = $newEnv;
}

//create / clean output folder
if (!file_exists(OUTPUT)) {
    mkdir(OUTPUT);
} else {
    $files = glob(OUTPUT . '*');
    foreach ($files as $file) {
        unlink($file);
    }
}

function addFolderToZip($folder, ZipArchive& $zip)
{
    $handler = opendir($folder);
    $folderName = str_replace(ROOTPATH, '', $folder);
    while (($filename = readdir($handler)) !== false) {
        if ($filename != "." && $filename != "..") {
            $filePath = $folder . $filename;
            if (is_dir($filePath)) {
                addFolderToZip($filePath . '/', $zip);
            } else {
                if ($folder === APPPATH . 'logs/') {
                    if ($filePath === APPPATH . 'logs/index.html')
                        $zip->addFile($filePath, $folderName . $filename);
                } else {
                    $zip->addFile($filePath, $folderName . $filename);
                }
            }
        }
    }
    closedir($handler);
}

$applicationFolder = APPPATH;
$systemFolder = SYSTEMPATH;
$indexPath = ROOTPATH . 'index.php';
$htaccessPath = ROOTPATH . '.htaccess';

//create or update .htaccess file according to env
if (!file_exists($htaccessPath)) {
    touch($htaccessPath);
    $file = fopen($htaccessPath, 'w');
    $content =
        "<IfModule mod_rewrite.c>\n" .
        "  Options +FollowSymlinks -Multiviews\n" .
        "  SetEnv CI_ENV $env\n" .
        "  SetEnvIf Authorization \"(.*)\" HTTP_AUTHORIZATION=$1\n" .
        "  RewriteEngine On\n" .
        "\n" .
        "  RewriteCond %{REQUEST_FILENAME} !-d\n" .
        "  RewriteCond %{REQUEST_FILENAME} !-f\n" .
        "  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]\n" .
        "</IfModule>";
    fwrite($file, $content);
    fclose($file);
} else {
    $content = file_get_contents($htaccessPath);
    $content = preg_replace('/SetEnv CI_ENV \S*/', "SetEnv CI_ENV $env", $content);
    file_put_contents($htaccessPath, $content);
}

//package all folders and files
$zipPath = OUTPUT . 'dist.zip';
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
    addFolderToZip($applicationFolder, $zip);
    addFolderToZip($systemFolder, $zip);
    $zip->addFile($indexPath, 'index.php');
    $zip->addFile($htaccessPath, '.htaccess');
    $zip->close();
    echo "$env dist.zip created!";
} else {
    echo 'failed to create zip file';
}

