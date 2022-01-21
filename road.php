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
            <ul><a href = "navigate.php">Узнать время приезда<br>пожарно-спасательного гарнизона</a></ul>
            </div>
            <div class = "go-to-menu">
            <ul><a href = "index.php">Вернуться на главную</a></ul>
            </div>
        </div>
    </nav>
</div>
</header>
<div id="map" style="width: 100%; height:600px"></div>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=17c1d609-afcb-47ea-92bf-749c4f395a2f"></script>
<?php
$sql_map ="SELECT * FROM `objects`";
$result_map = mysqli_query($connect, $sql_map);
$number=0;
// $map_element = mysqli_fetch_assoc($result_map);
?>
<script type="text/javascript">
ymaps.ready(init);
function init() {
<?php
while($map_element = mysqli_fetch_assoc($result_map)){
$number++;
if($number==1) {
?>


var myMap = new ymaps.Map("map", {
center: [<?php echo $map_element['point']; ?>],
zoom: 10
}, {
searchControlProvider: 'yandex#search'
});
<?php
}   
?>
var myCollection = new ymaps.GeoObjectCollection();

// Добавим метку красного цвета.
var myPlacemark = new ymaps.Placemark([
<?php echo $map_element['point']; ?>
], {}, {});
myCollection.add(myPlacemark);

myMap.geoObjects.add(myCollection);
// myMap.setBounds(myCollection.getBounds(),{checkZoomRange:false, zoomMargin:9});
<?php
}
?>
}
myMap.setBounds(myCollection.getBounds(),{checkZoomRange:true, zoomMargin:9});
</script>
<?php
//}
?>
</script>
<footer class="footer">
        <div class="container">
            <b><i>&copy; Информация получена из карт Москвы.</i></b>
        </div>
    </footer>
</body>
</html>