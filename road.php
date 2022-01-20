<?php
include "dbconnection.php";
$result = mysqli_query($connect, "SELECT * FROM `objects` WHERE `id` = 1");
error_reporting(E_ALL & ~E_NOTICE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="road.css">
    <title>Дорога</title>
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
            <ul><a href = "index.php">Вернуться на главную</a></ul>
            </div>
        </div>
    </nav>
</div>
</header>
<section>
<div id="map" style="width: 100%; height:500px"></div>
</section>
<script src="https://api-maps.yandex.ru/2.1/?apikey=8cbbd30d-041b-4d62-979b-9bc4821f4d2f&lang=ru_RU" type="text/javascript"></script>
<script type="text/javascript">
ymaps.ready(init);
function init() {
    var myMap = new ymaps.Map("map", {
        center: [<?php echo $object['point']; ?>],
        zoom: 16
    }, {
    searchControlProvider: 'yandex#search'
    });
  
    var myCollection = new ymaps.GeoObjectCollection(); 
  
    // Добавим метку красного цвета.
    var myPlacemark = new ymaps.Placemark([
    <?php echo $object['point']; ?>
    ], {
        balloonContent: '<?php echo $object['name']; ?>'
    }, {
        preset: 'islands#icon',
        iconColor: '#ff0000'
    });
    myCollection.add(myPlacemark);
  
    myMap.geoObjects.add(myCollection);
 }
</script>
</body>
</html>