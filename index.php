// index.php
<?php
// Veritabanı bağlantısı ve sınıf dosyası dahil edildi
require_once("doviz.php");
require_once("DovizKurlari.Class.php");

// DovizKurlari sınıfından bir nesne oluşturuldu ve veriler kaydedildi
$doviz = new DovizKurlari();
$doviz->saveToDatabase($conn);

// DovizKurlari sınıfı kullanılarak kurlar ekrana yazdırıldı 
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>PHP Döviz Kuru Çekme Sınıfı</title>

        <style type="text/css">
            table {background: #eee; margin: 0 auto; margin-top: 200px}
            table tr td {border: 1px solid #aeaeae}
        </style>

    </head>
    <body>
        <table border="0" cellpadding="10" cellspacing="0" width="250">
            <tr>
                <td></td>
                <td>USD</td>
                <td>EURO</td>
            </tr>
            <tr>
                <td>ALIŞ</td>
                <td><?php echo $doviz->usd_buy; ?></td>
                <td><?php echo $doviz->eur_buy; ?></td>
            </tr>
            <td>SATIŞ</td>
            <td><?php echo $doviz->usd_sell; ?></td>
            <td><?php echo $doviz->eur_sell; ?></td>
            </tr>
        </table>
    </body>
</html>
