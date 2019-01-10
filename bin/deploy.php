<?php
date_default_timezone_set("Europe/Berlin");
$time = date("Y-m-d_H-i-s");
if (is_dir("/release/")) {
    echo "Removing directory ./release/";
    system("rmdir ./release/");
}
echo "Creating directory ./release/\n";
system("mkdir ./release/");
echo "Unzipping $argv[1]\n";
system("unzip $argv[1] -d ./release/");
if (is_dir("./api/")){
    echo "Renaming ./api/ to ./api_$time\n";
    system("mv ./api/ ./api_$time");
}
echo "Renaming ./release/ to ./api/\n";
system("mv ./release/ ./api/");
echo "Removing zipfile $argv[1]\n";
system("rm $argv[1] -rf");
if (!is_dir("./api/tmp")) {
    echo "Creating /tmp directory";
    system("mkdir ./api/tmp");
}
if (!is_dir("./api/tmp/logs")) {
    echo "Creating /logs directory";
    system("mkdir ./api/tmp/logs");
}
if (!is_dir("./api/tmp/cache")) {
    echo "Creating /cache directory";
    system("mkdir ./api/tmp/cache");
}
//echo "NOT Updating permissions";
echo "Updating directory permissions to 775\n";
system("chmod -R 755 ./api");
//system("chmod 775 ./api/vendor/bin/phinx && chmod -R 775 ./api/vendor/robmorgan/");
echo "Migrating database";
system("cd api/config/ && ../vendor/robmorgan/phinx/bin/phinx migrate");
system("cd ..");
echo "Deleting old Backups ...";
system("php clean-up.php 31536000");
echo "\n";
echo "--------------------------------------------------------\n";
echo "Server deployment done\n";
echo "--------------------------------------------------------\n";