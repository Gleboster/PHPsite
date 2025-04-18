<?php
$con = dbConnect();

function dbConnect()
{
    // Указываем хост, имя пользователя, пароль, базу данных и порт
    $con = mysqli_connect("localhost", "root", "", "mlhu5y21_201311")
        or die("Could not connect: " . mysqli_connect_error());

    // Проверяем, успешно ли подключение
    if (!$con) {
        die("con failed: " . mysqli_connect_error());
    }

    // Выбираем базу данных (этот шаг не обязателен, если база данных указана в mysqli_connect)
    mysqli_select_db($con, "mlhu5y21_201311") or die("Could not select database: " . mysqli_error($con));

    return $con;
}

function dbDisconnect($con)
{
    mysqli_close($con);
}
