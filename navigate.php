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
<div class="container">
    <nav>
        <div class="menu">
            <div class="image-svg">
            <img src="images/ccfc35674a6649b6c0c170554b7d287b.svg">
            </div>
            <div class="menu-txt">
            <ul><a href = "road.php">Узнать месторасположение всех<br>пожарно-спасательногых гарнизонов</a></ul>
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
$min = 1000000000000000000000;
$lat = 55.874272;
$long = 37.575274;
$sql_map ="SELECT * FROM `objects`";
$result_tmap = mysqli_query($connect, $sql_map);
while ($map_dot = mysqli_fetch_assoc($result_tmap)) {
    $point = $map_dot['point'];
    $arrPoint = explode(", ", $point);
    $lat1 = $arrPoint[0];
    $long1 = $arrPoint[1];

    $S = calculateTheDistance($lat1, $long1, $lat, $long);
    if($min > $S){
        $min = $S;
        $minLat = $lat1;
        $minLong = $long1;
    }
}
?> <i><p>Расстояние между вашим домом и ближайшей пожарной станцией: <?php echo calculateTheDistance($minLat, $minLong, $lat, $long) . " метров";?></p></i>
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
  
     // Добавим метку красного цвета.
     var myPlacemark = new ymaps.Placemark([<?php echo $lat;?>, <?php echo $long; ?>], {}, {
         preset: 'islands#icon',
         iconColor: '#ff0000'
     });
     var myPlacemark1 = new ymaps.Placemark([<?php echo $minLat;?>, <?php echo $minLong; ?>], {}, {
         preset: 'islands#icon',
         iconColor: '#aaa'
     });
     myCollection.add(myPlacemark);
     myCollection.add(myPlacemark1);
  
     myMap.geoObjects.add(myCollection);
 }
</script>
</body>
</html>