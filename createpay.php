<?php
require_once "TeleWallet.php"; //Подключаем базу данных и TeleWallet API
$tlw = new TeleWallet('apikey','apAccount'); //создаем объект для работы с платежами
$paysum = 100;
$payId = genPay($user_id,$paysum); //genPay -- некоторый метод, который создает в вашей системе запись о платеже, и возвращает уникальный id этой записи
$checkObj = $tlw->getСheque($paysum,$payId); //создаем чек
if($checkObj['error']==0) { //всё хорошо
  //отдаем нашему пользователю ссылку $checkObj['url']  по которой он должен перейти и произвести платеж
}
else { //что-то не так с параметрами. Смотрите коды ошибок в описании
}
