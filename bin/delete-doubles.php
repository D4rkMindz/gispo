<?php
$dsn = "mysql:host=127.0.0.1;dbname=gispo;port=3306";
$db = new PDO($dsn, "root","");

$query = 'SELECT n1.id FROM users n1, users n2 WHERE n1.id < n2.id AND n1.email = n2.email AND n1.barcode IS NULL;';
$stmt = $db->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll();
$query = 'DELETE FROM users WHERE id = ';
foreach ($rows as $row){
    $id = $row['id'];
    $eQuery = $query . $id;
    echo "Deleting " . $row['id'];
    $stmt = $db->prepare($eQuery);
    if($stmt->execute()) {
        echo "\tdeleted successfully\n";
    } else {
        echo "\tFAILED\n";
        echo "-------------------------------------------------------------------------\n";
    }
}