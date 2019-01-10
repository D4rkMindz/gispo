<?php
$backupDirectory = scandir("./backup/");
$homeDirectory = scandir(".");
foreach ($homeDirectory as $dir){
    if (is_dir($dir) && strpos($dir, "htdocs_") !== false){
        echo "Removig $dir \n";
        system("rm -rf $dir");
    }
}
if (!isset($argv[1])) {
    $argv[1] = 0;
}
$time = date("Y-m-d_H-i-s", time() - $argv[1]);
$directories = [];
$directoriesToDelete = [];
foreach ($backupDirectory as $dir) {
    if (is_dir($dir)) {
        if ($dir < $time && strpos($dir, ".") === false) {
            $directoriesToDelete[] = $dir;
        }
    }
}
if ($argv[1] <= 60) {
    $t = "Seconds";
    $val = $argv[1];
} elseif ($argv[1] <= 3600) {
    $t = "Minutes";
    $val = $argv[1] / 60;
} elseif ($argv[1] <= 86400) {
    $t = "Hours";
    $val = ($argv[1] / 60) / 60;
} elseif ($argv[1] <= 31536000) {
    $t = "Days";
    $val = (($argv[1] / 60) / 60) / 24;
} elseif ($argv[1] > 31536000) {
    $t = "Years";
    $val = ((($argv[1] / 60) / 60) / 24) / 365;
}
foreach ($directoriesToDelete as $dir) {
    echo "Deleting directory $dir (older than $val $t)\n";
    system("rm -rf $dir");
}
$dirContent= scandir(".");
$files = [];
if (!is_dir("backup/")){
    echo "Creating ./backup directory\n";
    system("mkdir ./backup/");
}
foreach ($dirContent as $file){
    if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) == "zip"){
        $fulldate = str_replace("contact_form_", "", $file);
        $date = substr($fulldate, 0, -13);
        $files[] = $file;
        if (!is_dir("./backup/$date/")){
            echo "\n";
            echo "Creating ./backup/$date directory\n";
            system("mkdir ./backup/$date/");
        }
        echo "Moving $file to backup/$date/$file\n";
        rename($file, "backup/$date/$file");
    }
}
echo "\nFinished cleaning up zip files\n";