const querystring = require('querystring');
const http = require('http');
const sha1=require('sha1');

module.exports = function(apikey,accnumber) { //TeleWallet
	const domen = 'api.telewallet.ru';
	const createpaylink = "/create";
	const outpaylink = "/payout";
	const statuslink = "/paystatus";
	let sendRequest = function(obj,url,callback) {		
		obj.accnumber=accnumber;
		let data = querystring.stringify(obj);
		let options = {
			host: domen,
			port: 80,
			path: url,
			method: 'POST',
			headers: {
			  'Content-Type': 'application/x-www-form-urlencoded',
			  'Content-Length': Buffer.byteLength(data)
			}
		  };
		  let httpreq = http.request(options, function (response) {
			response.setEncoding('utf8');
			response.on('data', function (chunk) {
				let retobj = JSON.parse(chunk)
				callback(retobj)
			});
			response.on('end', function() {
			  console.log('ok');
			})
		  });
		  httpreq.write(data);
		  httpreq.end();	 
	}
	this.getСheque = function(sum,payId,target,callback) {		
		let obj = {sum:sum,payId:payId}
		if(target) obj.target=target
		sendRequest(obj,createpaylink,callback)
	}
	this.sendOutpay = function(sum,recepient,callback) {		
		let thash = sha1(accnumber+'&'+sum+'&'+recepient+'&'+apikey);
		let obj = {'sum':sum,'recepient':recepient,'hash':thash};		
		sendRequest(obj,outpaylink,callback)
	}
	this.payStatus = function(payId,callback) {
		let obj = {payId:payId}
		sendRequest(obj,statuslink,callback)
	}
}
