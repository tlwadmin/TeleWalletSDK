const Telegraf = require('telegraf')
const Keyboard = require('telegraf-keyboard');
const bot = new Telegraf('122008xxx6:AAExxx')
const TeleWallet = new require('./TeleWalletAutopay');

tlw = new TeleWallet('YvCszg9xxxxxxG','ap110xxxxxx8')
let payId = 1240; //должен быть уникальным внутри вашей системы. генерироваться для каждого нового платежа
let tekpay;
let balances={};
let donates={};
bot.start((ctx) => {
		const keyboard = new Keyboard();
		keyboard.add('Баланс', 'Пополнить','Проверить', 'Вывести')
		ctx.reply('Привет. я бот на JS для тестирования оплаты', keyboard.draw());	
	} )
bot.help((ctx) => ctx.reply('Помощь') )
bot.hears('Баланс', (ctx) => {
	if(!balances[ctx.update.message.from.id]) balances[ctx.update.message.from.id]=0;
	ctx.reply('Ваш баланс: '+balances[ctx.update.message.from.id]+' руб') 
})
bot.hears('Пополнить', (ctx) => {
		let sum = 1 //должна выбираться пользователем
		payId++;		
		tlw.getСheque(sum,payId,"оплата в тестовом боте",function(getobj) {
			if(getobj.error==0) { //отправляем сообщение с url-кнопкой для перехода к оплате
				donates[ctx.update.message.from.id]={sum:sum,payId:payId}
				let inline = [[{ text: 'Пополнить', url: getobj.url }],[{ text: 'Проверить оплату', callback_data: 'testpay' }]];
				ctx.reply('Вы собираетесь пополнить счет на '+sum+' руб. Пополнение доступно через @TeleWalletAbot Для продолжения, нажмите кнопку под этим сообщением.\nПосле оплаты нажмите Проверить оплату под этим сообщением',
				{ reply_markup: JSON.stringify({ inline_keyboard: inline }) }
				)
			}
		});
	} )
bot.hears('Вывести', (ctx) => {
			let user_acc = "103941229"  //номер счёта пользователя (просим пользователя его ввести)
			let sum = 1 //должна выбираться пользователем
			tlw.sendOutpay(sum,user_acc,function(obj) {
				if(obj.error==0) ctx.reply('На ваш счет выведена запрошенная сумма');
				else ctx.reply('Выплата не удалась. СВяжитесь с админом');
			});
	} )
bot.hears('Проверить', (ctx)=>{	
	if(!donates[ctx.update.message.from.id]) {
		ctx.reply('Платеж не обнаружен');
		return;
	}
	tlw.payStatus(donates[ctx.update.message.from.id].payId,function(obj) {
		console.log(obj)
		if(!balances[ctx.update.message.from.id]) balances[ctx.update.message.from.id]=0;
		if(obj.sum) {
			donates[ctx.update.message.from.id]=false;
			balances[ctx.update.message.from.id]+=+obj.sum;
			ctx.reply('Зачислено '+obj.sum+" руб");
		}
		else  ctx.reply('Платеж не обнаружен');
	});
});		
bot.startPolling()
