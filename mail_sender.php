<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer-master/PHPMailer-master/src/Exception.php';
require 'src/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'src/PHPMailer-master/PHPMailer-master/src/SMTP.php';

/**
 * Отправляет email через SMTP сервер st.guap.ru
 * 
 * @param string $message Текст сообщения (HTML)
 * @param string $emailReciever Email получателя
 * @return array Возвращает массив с результатом отправки
 */
function sendEmail($message, $emailReciever) {
    $mail = new PHPMailer(true);
    $result = ['success' => false, 'error' => ''];

    try {
        // Настройки SMTP
        $mail->isSMTP();
        $mail->Host = 'st.guap.ru';
        $mail->SMTPAuth = true;
        $mail->Username = 'user37455@st.guap.ru';
        $mail->Password = '5YHjFiye';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        // Отправитель
        $mail->setFrom('user37455@st.guap.ru', 'Кинотеатр «Высокого стиля»');
        
        // Получатель
        $mail->addAddress($emailReciever);
        
        // Содержимое письма
        $mail->isHTML(true);
        $mail->Subject = 'Сообщение от кинотеатра «Высокого стиля»';
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message); // Текстовая версия
        
        $mail->send();
        $result['success'] = true;
    } catch (Exception $e) {
        $result['error'] = $mail->ErrorInfo;
    }

    return $result;
}

?>