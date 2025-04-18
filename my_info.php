<?php
include 'header.php';
include 'connect_database.php';

// Обработка сохранения данных
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $nickname = $con->real_escape_string($_POST['nickname']);
    $tel = $con->real_escape_string($_POST['tel']);
    $email = $con->real_escape_string($_POST['email']);
    $sex = isset($_POST['sex']) ? (int)$_POST['sex'] : 0;
    
    $update_sql = "UPDATE 12222_Customer SET 
                  Nickname = '$nickname',
                  Tel = '$tel',
                  Email = '$email',
                  Sex = $sex
                  WHERE Customer_ID = " . $_SESSION['Customer_ID'];
    
    if ($con->query($update_sql)) {
        echo "<script>
            alert('Данные успешно сохранены!');
            window.location.href = 'my_info.php';
        </script>";
    } else {
        echo "<script>alert('Ошибка при сохранении данных: " . $con->error . "');</script>";
    }
}
?>

<style>
    .info-container {
        width: 500px;
        min-height: 300px;
        background-color: #ffff99;
        padding: 20px;
        margin: 20px auto;
    }
    .info-form {
        text-align: left;
        margin-left: 50px;
    }
    .info-form input[type="text"],
    .info-form input[type="email"],
    .info-form select {
        width: 200px;
        padding: 5px;
        margin-bottom: 10px;
    }
    .action-buttons {
        margin-top: 20px;
    }
    .action-buttons button {
        padding: 8px 15px;
        margin-right: 10px;
        cursor: pointer;
    }
    .edit-btn {
        background-color: #007bff;
        color: white;
        border: none;
    }
    .save-btn {
        background-color: #28a745;
        color: white;
        border: none;
    }
    .cancel-btn {
        background-color: #dc3545;
        color: white;
        border: none;
    }
</style>

<a href="my_info.php">Моя информация</a>
<a href="my_orders.php">Мои заказы</a>
<a href="my_comments.php">Мои комментарии</a>

<div class="info-container">
    <h2>Основная информация</h2>
    <div class="info-form">
    <?php
        $sql = "SELECT * FROM 12222_Customer WHERE Customer_ID=" . $_SESSION['Customer_ID'];
        $result = $con->query($sql);

        if (!$result) {
            die("Ошибка при запросе основной информации: " . $con->error);
        }

        $row = $result->fetch_array(MYSQLI_ASSOC);
        
        if (isset($_GET['edit'])) {
            // Режим редактирования
            echo '<form id="userForm" method="POST">';
            echo '<label>Имя пользователя:</label><br>';
            echo '<input type="text" name="nickname" value="' . htmlspecialchars($row['Nickname']) . '"><br>';
            echo '<label>Телефон:</label><br>';
            echo '<input type="text" name="tel" value="' . htmlspecialchars($row['Tel']) . '"><br>';
            echo '<label>Электронная почта:</label><br>';
            echo '<input type="email" name="email" value="' . htmlspecialchars($row['Email']) . '"><br>';
            echo '<label>Пол:</label><br>';
            echo '<select name="sex">';
            echo '<option value="1"' . ($row['Sex'] == 1 ? ' selected' : '') . '>Мужской</option>';
            echo '<option value="0"' . ($row['Sex'] == 0 ? ' selected' : '') . '>Женский</option>';
            echo '</select><br>';
            
            echo '<div class="action-buttons">';
            echo '<button type="submit" name="save" class="save-btn">Сохранить изменения</button>';
            echo '<button type="button" onclick="window.location.href=\'my_info.php\'" class="cancel-btn">Отмена</button>';
            echo '</div>';
            echo '</form>';
        } else {
            // Режим просмотра
            echo '<div id="userInfo">';
            echo '<p><strong>Имя пользователя:</strong> ' . htmlspecialchars($row['Nickname']) . '</p>';
            echo '<p><strong>Телефон:</strong> ' . htmlspecialchars($row['Tel']) . '</p>';
            echo '<p><strong>Электронная почта:</strong> ' . htmlspecialchars($row['Email']) . '</p>';
            echo '<p><strong>Пол:</strong> ' . ($row['Sex'] == 1 ? "Мужской" : "Женский") . '</p>';
            
            echo '<div class="action-buttons">';
            echo '<button onclick="window.location.href=\'my_info.php?edit=1\'" class="edit-btn">Изменить данные</button>';
            echo '</div>';
            echo '</div>';
        }
    ?>
    </div>
</div>

<?php
include 'footer.php';
?>