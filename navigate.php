<?php
include "dbconnection.php";
error_reporting(E_ALL & ~E_NOTICE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="navigation.css">
    <title>navigate</title>
</head>
<body>
<header>
<div class="container">
    <nav>
        <div class="menu">
            <div class="image-svg">
            <img src="images/ccfc35674a6649b6c0c170554b7d287b.svg">
            </div>
            <div class="menu-txt">
            <ul><a href = "road.php">Узнать месторасположение</a></ul>
            </div>
            <div class = "go-to-menu">
            <ul><a href = "index.php">Вернуться на главную</a></ul>
            </div>
        </div>
    </nav>
</div>
</header>
<?php
// Радиус земли
define('EARTH_RADIUS', 6372795);

/*
* Расстояние между двумя точками
* $φA, $λA - широта, долгота 1-й точки,
* $φB, $λB - широта, долгота 2-й точки
*/
function calculateTheDistance ($φA, $λA, $φB, $λB) {
 
// перевести координаты в радианы
$lat1 = $φA * M_PI / 180;
$lat2 = $φB * M_PI / 180;
$long1 = $λA * M_PI / 180;
$long2 = $λB * M_PI / 180;
 
// косинусы и синусы широт и разницы долгот
$cl1 = cos($lat1);
$cl2 = cos($lat2);
$sl1 = sin($lat1);
$sl2 = sin($lat2);
$delta = $long2 - $long1;
$cdelta = cos($delta);
$sdelta = sin($delta);
 
// вычисления длины большого круга
$y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
$x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;
 
// рассчитать расстояние в соответствии с радиусом Земли
$ad = atan2($y, $x);
$dist = $ad * EARTH_RADIUS;
 
return $dist;
}
?>
<div class="answer">
Введите Улицу и номер дома
<form name="a" method="GET" action="<?=$_SERVER['PHP_SELF']?>" width = 200px padding = 20px>
<input class = "form_input" name="Street" placeholder = "Пример: Неделина">
<input class = "form_input" name="House" placeholder = "Пример: д.24">
<input class = "form_button" type="submit">
</form>
<?php
$city = "Москва";
if(isset($_GET['Street']) and isset($_GET['House'])){

    $street = $_GET['Street'];
    $house = $_GET['House'];
    $address = $city.", ".$street.", ".$house;
 
    $ch = curl_init('https://geocode-maps.yandex.ru/1.x/?apikey=17c1d609-afcb-47ea-92bf-749c4f395a2f&format=json&geocode=' . urlencode($address));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);
 
    $res = json_decode($res, true);
    $coordinates = $res['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
    $coordinates = explode(' ', $coordinates);
    $min = 10000000000000000000000;
    $minLat = 0;
    $min2Lat = 0;
    $min3Lat = 0;
    $minLong = 0;
    $min2Long = 0;
    $min3Long = 0;
    $min2 = 1000000000000000000000;
    $min3 = 100000000000000000000;
    $lat = $coordinates[1];
    $long = $coordinates[0];

    $sql_map ="SELECT * FROM `objects`";
    $result_tmap = mysqli_query($connect, $sql_map);
    while ($map_dot = mysqli_fetch_assoc($result_tmap)) {
        $point = $map_dot['point'];
        $arrPoint = explode(", ", $point);
        $lat1 = $arrPoint[0];
        $long1 = $arrPoint[1];

        $S = calculateTheDistance($lat1, $long1, $lat, $long);
        if($min > $S){
            $prev = $min;
            $prevLat = $minLat;
            $prevLong = $minLong;
            $min = $S;
            $minLat = $lat1;
            $minLong = $long1;
            if($min2 > $prev and $min != $prev){
                $boof = $min2;
                $boofLat = $min2Lat;
                $boofLong = $min2Long;
                $min2 = $prev;
                $min2Lat = $prevLat;
                $min2Long = $prevLong;
                $prev = $boof;
                $prevLat = $boofLat;
                $prevLong = $boofLong;
                if($min3 > $prev and $min2 != $prev){
                    $min3 = $prev;
                    $min3Lat = $prevLat;
                    $min3Long = $prevLong;
                }
            }
        }
        if($min2 > $S and $min < $S){
            $prev = $min2;
            $prevLat = $min2Lat;
            $prevLong = $min2Long;
            $min2 = $S;
            $min2Lat = $lat1;
            $min2Long = $long1;
            if($min3 > $prev and $min2 != $prev){
                $min3 = $prev;
                $min3Lat = $prevLat;
                $min3Long = $prevLong;
            }
        }
        if($min3 > $S and $min2 < $S and $min < $S){
            $min3 = $S;
            $min3Lat = $lat1;
            $min3Long = $long1;
        }
    }
    ?> 
    <p>Расстояние между вашим домом и ближайшей пожарной станцией: <?php echo round(calculateTheDistance($minLat, $minLong, $lat, $long), 0) . " метров.";?></p>
    <p>Среднее время приезда пожарной части к вашему дому: <?php echo round(round(calculateTheDistance($minLat, $minLong, $lat, $long), 0)/12.5, 0) . " секунд.";?></p>
    <div id="map" style="width: 100%; height:600px"></div>
 
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=17c1d609-afcb-47ea-92bf-749c4f395a2f" type="text/javascript"></script>
    <script type="text/javascript">
    ymaps.ready(init);
    function init() {
        var myMap = new ymaps.Map("map", {
            center: [<?php echo $lat;?>, <?php echo $long; ?>],
            zoom: 16
        }, {
            searchControlProvider: 'yandex#search'
        });
  
        var myCollection = new ymaps.GeoObjectCollection(); 
  
        var myPlacemark = new ymaps.Placemark([<?php echo $lat;?>, <?php echo $long; ?>], {balloonContentHeader: "Ваш Дом"}, {
            preset: 'islands#blueHomeCircleIcon',
            iconColor: '#4d6b00'
        });
        <?php
        $sql_met ="SELECT name FROM `objects` WHERE point = \"".$minLat.", ".$minLong."\"";
        $result_met = mysqli_query($connect, $sql_met);
        $met = mysqli_fetch_assoc($result_met);
        $metS = (string) $met['name'];
        ?>
        var myPlacemark1 = new ymaps.Placemark([<?php echo $minLat;?>, <?php echo $minLong; ?>],{balloonContentHeader: "<?php echo $metS ?>"}, {
            
            iconLayout: 'default#image',
            iconImageHref: 'images/пожар.png',
            iconImageSize: [60, 60]
        });
        <?php
        $sql_met ="SELECT name FROM `objects` WHERE point = \"".$min2Lat.", ".$min2Long."\"";
        $result_met = mysqli_query($connect, $sql_met);
        $met = mysqli_fetch_assoc($result_met);
        $metS = (string) $met['name'];
        ?>
        var myPlacemark2 = new ymaps.Placemark([<?php echo $min2Lat;?>, <?php echo $min2Long; ?>], {balloonContentHeader: "<?php echo $metS ?>"}, {
            iconLayout: 'default#image',
            iconImageHref: 'images/пожар.png',
            iconImageSize: [60, 60]
        });
        <?php
        $sql_met ="SELECT name FROM `objects` WHERE point = \"".$min3Lat.", ".$min3Long."\"";
        $result_met = mysqli_query($connect, $sql_met);
        $met = mysqli_fetch_assoc($result_met);
        $metS = (string) $met['name'];
        ?>
        var myPlacemark3 = new ymaps.Placemark([<?php echo $min3Lat;?>, <?php echo $min3Long; ?>], {balloonContentHeader: "<?php echo $metS ?>"}, {
            iconLayout: 'default#image',
            iconImageHref: 'images/пожар.png',
            iconImageSize: [60, 60]
        });
        myCollection.add(myPlacemark);
        myCollection.add(myPlacemark1);
        myCollection.add(myPlacemark2);
        myCollection.add(myPlacemark3);
  
        myMap.geoObjects.add(myCollection);
    }
</script><?php
}?>
<footer class="footer">
        <div class="container">
            <b>&copy; Информация получена из карт Москвы.</b>
        </div>
    </footer>
</body>
</html>