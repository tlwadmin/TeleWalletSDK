<?php
class TeleWallet {
	private $apikey;
	private $accnumber;
	private $createpaylink = "https://api.telewallet.ru/create";
	private $outpaylink = "https://api.telewallet.ru/payout";
	function __construct($apikey,$accnumber) {
		$this->apikey=$apikey;
		$this->accnumber=$accnumber;
	}
	protected function sendRequest($obj,$url) {		
		$obj['accnumber']=$this->accnumber;		
		$postdata = http_build_query($obj);
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);
		$context  = stream_context_create($opts);	 
		return file_get_contents($url, false, $context);
	}
	public function getСheque($sum, $payId,$target="") {
		return json_decode($this->sendRequest(["sum"=>$sum,"payId"=>$payId,"target"=>$target],$this->createpaylink),true);
	}
	public function sendOutpay($sum,$recepient) {
		$hash = sha1($this->accnumber.'&'.$sum.'&'.$recepient.'&'.$this->apikey);
		return json_decode($this->sendRequest(['sum'=>$sum,'recepient'=>$recepient,'hash'=>$hash],$this->outpaylink),true);
	}
	public function testPayIn($post_obj) {
		$hash = sha1($this->accnumber.'&'.$post_obj['sum'].'&'.$post_obj['payId'].'&'.$this->apikey);
		return ($post_obj['hash']==$hash);
	}
}
?>
