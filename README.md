# php-ile-ekilen-doviz-kurlarini-veri-tabanina-kaydetme
Merhaba, bu çalışmamda TCMB xml sayfasından güncel döviz kurlarını çekmeyi ve bu çektiğim verileri veritabanına kaydetmeyi gösterdim. veritabanı bağlantısı için iki ayrı gösterimle çalıştım. dosya içeriğini açıklayacak olursam; 
##index.php dosyasında, doviz.php ve DovizKurlari.Class.php dosyaları dahil ediliyor. Bu dosyalar, döviz kurları ile ilgili fonksiyonlar ve sınıflar içeriyor olabilir. doviz.php dosyasının, veritabanı bağlantısı gibi temel ayarları içerdiğini varsayabiliriz.

<code>
    $doviz = new DovizKurlari();
    $doviz->saveToDatabase($conn);
</code>code>

DovizKurlari sınıfından bir nesne ($doviz) oluşturuluyor.
$doviz->saveToDatabase($conn); satırı ile sınıfın saveToDatabase metodu çağrılıyor ve bu metot veritabanı bağlantısını ($conn) kullanarak döviz kurlarını veritabanına kaydediyor.

gerisi html sayfa tasarımıdır. burada sadece php kodlarıyla tablo oluşturmayı açıklayacağım.
<code>
    <td><?php echo $doviz->usd_buy; ?></td>
    <td><?php echo $doviz->eur_buy; ?></td>
    <td><?php echo $doviz->usd_sell; ?></td>
    <td><?php echo $doviz->eur_sell; ?></td>
</code>
PHP kodu kullanılarak DovizKurlari sınıfı içerisindeki döviz kurları (usd_buy, eur_buy, usd_sell, eur_sell) tabloya yazdırılıyor.
Bu sınıf, muhtemelen API veya başka bir kaynaktan döviz kurlarını alarak bu verileri değişkenlerde saklıyor.

##DovizKurlari.class.php dosyasında; 
<code>
    private $tcmb = "http://www.tcmb.gov.tr/kurlar/today.xml";
    private $conn;
    
    public $usd_buy;
    public $usd_sell;
    
    public $eur_buy;
    public $eur_sell;
</code>
<mark>$tcmb:</mark> TCMB'nin günlük döviz kurlarını XML formatında sunduğu URL'dir.
<mark>$conn</mark> TCMB'den alınan XML dosyasını işlemek için kullanılacak olan bağlantıdır (XML verisi).
<mark>usd_buy, usd_sell, eur_buy, eur_sell:</mark> USD ve EUR için alış ve satış kurlarını saklamak için kullanılan sınıf değişkenleridir. Bu değişkenler public olarak tanımlandığı için sınıfın dışından da erişilebilirler.

<mark>Yapıcı Metot (__construct)</mark>
<code>
    public function __construct(){
        $this->conn = simplexml_load_file($this->tcmb);
        $this->USD_Data();
        $this->EUR_Data();
    }
</code>
__construct metodu, sınıfın bir nesnesi oluşturulduğunda otomatik olarak çalıştırılan metottur.
<mark>$this->conn = simplexml_load_file($this->tcmb);</mark>: simplexml_load_file fonksiyonu ile TCMB'den XML dosyası yüklenir ve $conn değişkenine atanır.
<mark>$this->USD_Data(); ve $this->EUR_Data();</mark>: Bu iki metod çağrılarak, USD ve EUR kurları XML dosyasından çekilir ve ilgili değişkenlere atanır.
<code>
public function USD_Data(){
        $this->usd_buy  = $this->conn->Currency[0]->BanknoteBuying;
        $this->usd_sell = $this->conn->Currency[0]->BanknoteSelling;
    }
</code>
Bu metot, XML dosyasından USD'nin alış ve satış kurlarını alır ve $usd_buy ve $usd_sell değişkenlerine atar.
<mark>$this->conn->Currency[0]:</mark> XML dosyasındaki ilk döviz kurunu ifade eder, burada USD temsil edilmektedir. Bu durum EUR döviz kurunda da aynıdır.Currency[3] ise eur doviz kuruna denk gelmektedir. bu durumu arttırabilir veya azaltabilirsiniz.

<mark>saveToDatabase Metodu</mark>
<code>
    public function saveToDatabase($pdo){
        $stmt = $pdo->prepare("INSERT INTO kurlar (usd_buy, usd_sell, eur_buy, eur_sell) VALUES (:usd_buy, :usd_sell, :eur_buy, :eur_sell)");
    
        $stmt->bindParam(':usd_buy', $this->usd_buy);
        $stmt->bindParam(':usd_sell', $this->usd_sell);
        $stmt->bindParam(':eur_buy', $this->eur_buy);
        $stmt->bindParam(':eur_sell', $this->eur_sell);
        $stmt->execute();
    }
</code>
Bu metot, sınıfın içerisindeki USD ve EUR kurlarını veritabanına kaydetmek için kullanılır.
prepare metodu, veritabanı sorgusunu SQL enjeksiyonuna karşı güvenli hale getirmek için kullanılan bir PDO metodudur.
bindParam: SQL sorgusundaki parametrelerin (:usd_buy, :usd_sell, :eur_buy, :eur_sell) sınıftaki ilgili değişkenlere bağlanmasını sağlar. özetle Bu sınıf, TCMB'den döviz kurlarını XML formatında çekip, bu kurları işleyerek ilgili veritabanı tablolarına kaydeder. USD_Data ve EUR_Data metodları, USD ve EUR kurlarını alırken, saveToDatabase metodu ise bu verileri veritabanına kaydeder.

##doviz.php dosyasında ise;

require_once("DovizKurlari.Class.php");

Bu satır, DovizKurlari.Class.php dosyasını projeye dahil eder. Bu dosya, döviz kurlarıyla ilgili işlemleri yapan DovizKurlari sınıfını içerir.
<code>
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "doviz_kurlari";
</code>
bu kod parçası ise veritabanı bağlantı bilgilerini içerir.
<code>
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $doviz = new DovizKurlari();
        $doviz->saveToDatabase($conn);
    
        echo "Veri başarıyla kaydedildi";
    } catch(PDOException $e) {
        echo "Bağlantı veya kayıt hatası: " . $e->getMessage();
    }
</code>
try bloğu, hata oluşabilecek kodları içerir ve hatalar catch bloğunda ele alınır.
<code>
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
</code>
new PDO(...): PHP Data Objects (PDO) kullanılarak bir veritabanı bağlantısı oluşturulur.
setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION): Hata raporlama modunu ERRMODE_EXCEPTION olarak ayarlar, bu da hataların bir istisna (Exception) olarak fırlatılacağı anlamına gelir.
<code>
    $doviz = new DovizKurlari();
    $doviz->saveToDatabase($conn);
</code>
<mark>new DovizKurlari():</mark> DovizKurlari sınıfından bir nesne oluşturulur.
$doviz->saveToDatabase($conn): Bu nesne kullanılarak döviz kurları veritabanına kaydedilir. saveToDatabase metoduna veritabanı bağlantısı ($conn) parametre olarak verilir.

ve sayfanın son görüntüsü ve veritabanına kaydetme durumu aşağıdaki gibidir.
![Ekran Görüntüsü (48)](https://github.com/user-attachments/assets/fd469bc7-570f-4d7f-9b58-598fa4aa4846)

