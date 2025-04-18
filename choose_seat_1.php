<?php

include 'header.php';
include 'connect_database.php';

if (isset($_POST['Running_Movie_ID']) || isset($_SESSION['Running_Movie_ID'])) {
    // Get or maintain the Running_Movie_ID from POST or session
    $Running_Movie_ID = $_POST['Running_Movie_ID'] ?? $_SESSION['Running_Movie_ID'];
    $_SESSION['Running_Movie_ID'] = $Running_Movie_ID;

    // Get or maintain the ticket price
    if (isset($_POST['Price'])) {
        $ticketPrice = $_POST['Price'];
        $_SESSION['Price'] = $ticketPrice;
    } elseif (isset($_SESSION['Price'])) {
        $ticketPrice = $_SESSION['Price'];
    }

    // Handle seat selection or removal
    $selectedSeats = isset($_SESSION['selectedSeats']) ? $_SESSION['selectedSeats'] : [];
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'addSeat') {
            $newSeat = [
                'row' => $_POST['Row_Num'],
                'column' => $_POST['Column_Num'],
                'price' => $_SESSION['Price'],
            ];

            // Prevent duplicate selections
            foreach ($selectedSeats as $seat) {
                if ($seat['row'] == $newSeat['row'] && $seat['column'] == $newSeat['column']) {
                    $error = "Ошибка: Это место уже выбрано!";
                    break;
                }
            }

            if (empty($error)) {
                // Add new seat
                $selectedSeats[] = $newSeat;
                $_SESSION['selectedSeats'] = $selectedSeats;
            }
        }

        if ($_POST['action'] === 'removeSeat') {
            $seatIndex = $_POST['seatIndex'];
            if (isset($selectedSeats[$seatIndex])) {
                unset($selectedSeats[$seatIndex]);
                $_SESSION['selectedSeats'] = array_values($selectedSeats);
            }
        }
    }

    // Retrieve seats from the database
    $sql = "SELECT * FROM 12222_seat_on_sale WHERE Running_Movie_ID='$Running_Movie_ID' ORDER BY Row_Num ASC, Column_Num ASC";
    $result = $con->query($sql);

    if (!$result) {
        die("Ошибка при запросе мест: " . $con->error);
    }
?>

    <div class="container">

        <div class="seating-chart">
            <h3>Схема зала</h3>
            <form method="POST" class="seat-selector">
                <table class="seats-table">
                    <?php
                    $currentRow = null;
                    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                        if ($currentRow !== $row['Row_Num']) {
                            if ($currentRow !== null) {
                                echo "</tr>";
                            }
                            $currentRow = $row['Row_Num'];
                            echo "<tr><td>Ряд " . $row['Row_Num'] . "</td>";
                        }

                        $seatNumber = $row['Column_Num'];
                        $reservedClass = $row['Is_Reserved'] ? 'reserved' : 'available';
                        $isSelected = false;

                        foreach ($selectedSeats as $seat) {
                            if ($seat['row'] == $row['Row_Num'] && $seat['column'] == $seatNumber) {
                                $isSelected = true; // Check if the seat is already in the list of selected seats
                            }
                        }

                        $seatClass = $reservedClass;
                        if ($isSelected) {
                            $seatClass = 'selected';
                        }

                        echo "<td class='seat $seatClass'>$seatNumber</td>";
                    }
                    if ($currentRow !== null) {
                        echo "</tr>";
                    }
                    ?>
                </table>

                <div>
                    <label for="rowNum">Номер ряда:</label>
                    <select name="Row_Num" id="rowNum">
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="columnNum">Номер колонки:</label>
                    <select name="Column_Num" id="columnNum">
                        <?php for ($i = 1; $i <= 12; $i++) : ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <input type="hidden" name="action" value="addSeat">
                    <button type="submit">Добавить место</button>
                </div>
            </form>
        </div>

        <div class="selected-seats">
            <h3>Выбранные места</h3>
            <div class="selected-seats-box">
                <form method="POST">
                    <table id="selected-seats-table">
                        <thead>
                            <tr>
                                <th>Ряд</th>
                                <th>Место</th>
                                <th>Цена</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalPrice = 0;
                            foreach ($selectedSeats as $index => $seat) {
                                echo "<tr>"
                                    . "<td>{$seat['row']}</td>"
                                    . "<td>{$seat['column']}</td>"
                                    . "<td>{$seat['price']}</td>"
                                    . "<td>"
                                    . "<button type='submit' name='seatIndex' value='$index'>Удалить</button>"
                                    . "</td>"
                                    . "</tr>";
                                $totalPrice += $seat['price'];
                            }
                            ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="action" value="removeSeat">
                </form>
                <div id="total-price">Общая стоимость: <?= $totalPrice ?> ₽</div>
                <form action="process_payment.php" method="POST">
                    <input type="hidden" name="selectedSeats" value="<?= htmlspecialchars(json_encode($selectedSeats)) ?>">
                    <button type="submit" id="confirm-payment">Подтвердить оплату</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Add the original styles you provided here */
    </style>

<?php
} else {
    echo "<h3>Ошибка: Идентификатор фильма не указан.</h3>";
}

include 'footer.php';
?>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color:rgb(255, 255, 255);
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: rgb(255, 255, 153);
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .seating-chart {
        padding: 15px;
    }

    .seats-table {
        width: 100%;
        border-collapse: collapse;
    }

    .seats-table td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .seat {
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .available {
        background-color: #1ec5e5;
    }

    .reserved {
        background-color: #ff6b6b;
        cursor: not-allowed;
    }

    .selected {
        background-color: #4caf50;
    }

    .selected-seats {
        padding: 15px;
        background:rgb(218, 236, 255);
    }

    .selected-seats-box {
        background: #fff;
        padding: 15px;
    }

    #selected-seats-table {
        width: 100%;
        border-collapse: collapse;
    }

    #selected-seats-table th,
    #selected-seats-table td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }

    #total-price {
        margin-top: 15px;
        font-weight: bold;
        text-align: center;
    }

    button {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }

    .seat-selector {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        text-align: center;
    }

    .seat-selector label {
        display: inline-block;
        margin-right: 10px;
        font-weight: bold;
    }

    .seat-selector select, .seat-selector button {
        padding: 8px;
        margin-right: 10px;
        border: 1px solid #ddd;
    }

    .seat-selector button {
        background-color: #28a745;
        color: #fff;
    }

    .seat-selector button:hover {
        background-color: #218838;
    }
</style>
