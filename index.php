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
    <link rel="stylesheet" href="css.css">
    <title>Курсовая</title>
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
            <div class="menu-txt2">
            <ul><a href = "navigate.php">Узнать время приезда</a></ul>
            </div>
        </div>
    </nav>
</div>
</header>
<section>
    <div class="dataorg">Данные о вызовах пожарно-спасательных гарнизонов</div>
</div>
    <div class = "information">
    <div class="image-svg-r">
    <img src="images/41934.svg" width = "350" height = "350">
    </div>
    <div class =information-txt>
<i>Набор данных «Данные вызовов подразделений
<br>пожарно-спасательного гарнизона города Москвы по месяцам»
<br>содержит информацию о количестве вызовов
<br>пожарных подразделений в городе Москве по месяцам.
<br>В Государственную противопожарную службу в городе Москве входят:
<br>- федеральная противопожарная служба на территории города Москвы;
<br>- противопожарная служба города Москвы.</i>
</div>
</div>
</section>
<section>
<div class="answer">
Введите дату
<form name="a" method="GET" action="<?=$_SERVER['PHP_SELF']?>" width = 200px padding = 20px>
<input class = "form_input" name="a" placeholder = "Пример: 2020-01-01">
<input class = "form_button" type="submit">
</form>
</div>
<div class = "db">
    <?php
    if(isset($_GET['a'])){
    $a = $_GET['a'];
    $result = mysqli_query($connect, "SELECT * FROM `Call_Mounth` WHERE dataReport = '".$a."'");
    $stroka = mysqli_fetch_assoc($result);
    echo '<p style= "margin:0">Количество вызовов пожарно-спасательных гарнизонов в '.$a.':<br>'.$stroka['callsCount'].'<p>';
}?>
</div>
</section>
<footer class="footer">
        <div class="container">
            <b>&copy; Информация получена из баз открытых данных Москвы.</b>
        </div>
    </footer>
</body>
</html>