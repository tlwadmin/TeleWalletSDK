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
		file_put_contents("sendpost.log",json_encode($obj,JSON_UNESCAPED_UNICODE)."\n url=$url");		
		$queryData = http_build_query($obj);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_SSL_VERIFYPEER => 0,
		    CURLOPT_POST => 1,
		    CURLOPT_HEADER => 0,
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		    CURLOPT_POSTFIELDS => $queryData,
		));

		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}
	public function getСheque($sum, $payId,$target="") {
		return json_decode($this->sendRequest(["sum"=>$sum,"payId"=>$payId,"target"=>$target],$this->createpaylink),true);
	}
	public function sendOutpay($sum,$recepient) {
		$hash = sha1($this->accnumber.'&'.$sum.'&'.$recepient.'&'.$this->apikey);
		$reqv = $this->sendRequest(['sum'=>$sum,'recepient'=>$recepient,'hash'=>$hash],$this->outpaylink);
		if(empty($reqv)) return ['error'=>8];
		return json_decode($reqv,true);
	}
	public function testPayIn($post_obj) {
		$hash = sha1($this->accnumber.'&'.$post_obj['sum'].'&'.$post_obj['payId'].'&'.$this->apikey);
		return ($post_obj['hash']==$hash);
	}
}
?>
