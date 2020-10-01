<?php
include('../vendor/autoload.php'); 
use Telegram\Bot\Api; 
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;    

require_once "config/config.php";
require_once "TeleWallet.php";
$telegram = new Api(BOTTOKEN);
$tlw = new TeleWallet('apikey','apAccount');
$ri = mysqli_query($link,"SELECT * FROM `popolnenie` WHERE `id`={$_POST['payId']}");
$pay_info = mysqli_fetch_assoc($ri);
if($tlw->testPayIn($_POST) && $pay_info['sum']==$_POST['sum']) {
	echo "YES";
	mysqli_query($link,"UPDATE `users` SET `balance`=`balance`+{$pay_info['sum']} where `id`={$pay_info['user_id']}");
	try {	
		$telegram->sendMessage(["text"=>"Ваш баланс пополнен на {$pay_info['sum']} руб","chat_id"=>$pay_info['user_id']]);
	}
	catch(Exception $e) {}
}
else echo "NO";
?>
