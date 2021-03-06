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
if (is_dir("./app/")) {
    echo "Renaming ./app/ to ./app_$time\n";
    system("mv ./app/ ./app_$time");
}
echo "Renaming ./release/ to ./app/\n";
system("mv ./release/ ./app/");

if (is_dir("./app_$time/public/img/users")) {
    echo "Copying old images";
    copy("./app_$time/public/img/users", "./app/public/img/users");
}

echo "Removing zipfile $argv[1]\n";
system("rm $argv[1] -rf");
if (!is_dir("./app/tmp")) {
    echo "Creating /tmp directory";
    system("mkdir ./app/tmp");
}
if (!is_dir("./app/tmp/logs")) {
    echo "Creating /logs directory";
    system("mkdir ./app/tmp/logs");
}
if (!is_dir("./app/tmp/cache")) {
    echo "Creating /cache directory";
    system("mkdir ./app/tmp/cache");
}
//echo "NOT Updating permissions";
echo "Updating directory permissions to 777\n";
system("chmod -R 777 ./app");
//system("chmod 775 ./app/vendor/bin/phinx && chmod -R 775 ./app/vendor/robmorgan/");
echo "Migrating database";
system("cd ./app/config/ &&../vendor/robmorgan/phinx/bin/phinx migrate && cd ../..");
echo "Deleting old Backups ...";
system("php clean-up.php 31536000");
echo "\n";
echo "--------------------------------------------------------\n";
echo "Server deployment done\n";
echo "--------------------------------------------------------\n";