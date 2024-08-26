<?php

require_once("DovizKurlari.Class.php");

// Veritabanı bağlantı bilgileri
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doviz_kurlari";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $doviz = new DovizKurlari();
    $doviz->saveToDatabase($conn);

    echo "Veri başarıyla kaydedildi";
} catch(PDOException $e) {
    echo "Bağlantı veya kayıt hatası: " . $e->getMessage();
}

?>
