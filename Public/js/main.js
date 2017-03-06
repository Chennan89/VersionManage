/*
	模块主配置文件及公共方法
*/
var APP = {};

APP.ENVIRONMENT = 'bugFix'; //development,production,bugFix
APP.apiPath = 'http://121.42.41.228:8011';
APP.apiUpload = 'http://121.42.41.228:8081'; //upload

APP.XHROK = 'ok'; //成功
APP.XHRFAIL = 'error'; //失败
APP.XHRVERIFYFAIL = 20001; //[失败]验证未通过
APP.TOKENDURATION = 60 * 10 * 60; //token持续时间
APP.PAGESIZE = 15; //通用一页显示数据数量
APP.TPLPATH = './inc/'; //模板路径

APP.TEST=1;//切换线上线下  0表示线上 1表示线下
APP.PATHROOT=APP.TEST ? '../Versions/':'../../Versions/';
APP.RESROOT=APP.TEST ? '../CasinoMatchUpdate/luatest':'../../CasinoMatchUpdate/luatest';//记录资源文件存放的路径

//加载器配置
require.config({
	baseUrl: './Public/js',
	paths: {
		jquery: 'lib/jquery-1.12.1.min', //jquery
		/*art: 'lib/template-native', //artTemplate
		pagination: 'lib/jquery.pagination', //分页插件
		cookie: 'lib/cookies.min', //cookie
		laydate: 'lib/laydate/laydate', //日历
		placeholder: 'lib/jquery.placeholder.min', //低版本IE placeholder
		CryptoJS: 'lib/crypto-js.md5', //md5加密库
		imgScroll: 'lib/jquery.imgScroll.min', //轮播
		store: 'lib/store.min', //全兼容本地存储
		html2canvas: 'lib/html2canvas.min', //html节点转canvas
		QRCode: 'lib/qrcode.min' //生成二维码*/
	},
	/*shim: {
		pagination: ['jquery'],
		placeholder: {
			deps: ['jquery'],
			exports: '$.fn.placeholder'
		},
		laydate: {
			exports: 'laydate'
		},
		CryptoJS: {
			exports: 'CryptoJS'
		},
		imgScroll: ['jquery'],
		QRCode: {
			exports: 'QRCode'
		}
	}*/
});
//URI - query属性查询
APP.URISearch = function(attr, splitChar) {
	var query = window.location.search;
	//location.search是从当前URL的?号开始的字符串 如:http://www.51js.com/viewthread.php?tid=22720 它的search就是?tid=22720 
	var splitChar = splitChar || '&';
	var attrArr = [];
	var attrObj = {};

	query = query.replace(/^\?/, '');
	attrArr = query.split(splitChar);
	for (var i = attrArr.length - 1; i >= 0; i--) {
		attrObj[attrArr[i].split('=')[0]] = attrArr[i].split('=')[1];
	};

	return attrObj[attr];
};

//返回指定元素是否在数组中
APP.inArray = function(arr, el) {
	return arr.indexOf(el) > -1;
};

//寻找拥有className的目标节点
APP.filterNode = function(node, className) {
	var reg = new RegExp(className, 'i');//不区分大小写
	while (!reg.test(node.className)) {
		node = node.parentNode;
	}
	return node;
};

//返回ios或者android
APP.sys = function() {
	var sys = '';
	var ua = navigator.userAgent.toLowerCase();
	if (ua.indexOf('iphone') > -1 || ua.indexOf('ipad') > -1) {
		sys = 'ios';
	} else if (ua.indexOf('android') > -1) {
		sys = 'android';
	}
	return sys;
};

//ua
APP.getUA = function() {
	return navigator.userAgent.toLowerCase();
};

//微信判断
APP.isWechat = function() {
	var ua = navigator.userAgent.toLowerCase();
	if (ua.match(/MicroMessenger/i) == "micromessenger") {
		return true;
	}
	return false;
};

//数字格式化
// http://phpjs.org/functions/number_format/
APP.numberFormat = function(number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function(n, prec) {
			var k = Math.pow(10, prec);
			return '' + (Math.round(n * k) / k)
				.toFixed(prec);
		};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
		.split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '')
		.length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1)
			.join('0');
	}
	return s.join(dec);
};

//提示框
APP.showToast = function(content, delay, opts) {
	var ele = document.createElement('div'),
		text = document.createTextNode(content),
		opts = opts || {};
	document.body.appendChild(ele);
	ele.id = 'toast';
	ele.className = 'toast';
	ele.appendChild(text);
	setTimeout(function() {
		document.body.removeChild(ele);
		if (typeof opts.callback === 'function') {
			opts.callback();
		}
	}, delay || 2000);
};

//字段校验提示信息
APP.validInfo = function(data) {
	var ele = document.getElementById(data.id),
		spaceT = data.spaceT || 5,
		spaceL = data.spaceL || 0;

	//存在则只修改定位和内容
	if (ele) {
		ele.style.left = data.left + spaceL + 'px';
		ele.style.top = data.top + spaceT + 'px';
		ele.innerHTML = data.content;
		return ele;
	}

	var ele = document.createElement('span');

	ele.id = data.id;
	ele.className = 'valid-info ' + data.className;
	ele.style.left = data.left + spaceL + 'px';
	ele.style.top = data.top + spaceT + 'px';
	ele.innerHTML = data.content;
	document.getElementById('validInfoBox').appendChild(ele);

	return ele;
};

//弹层定位
APP.popupPos = function(obj) {
	var scrollTop = document.body.scrollTop + document.documentElement.scrollTop,
		scrollW = document.body.scrollWidth,
		offsetH = Math.max(document.body.offsetHeight, document.documentElement.clientHeight),
		clientH = document.documentElement.clientHeight,
		posMT = Math.ceil(obj.height / 2),
		posML = -Math.ceil(obj.width ? obj.width / 2 : 400),
		posT = '50%',
		boxW = obj.width || 800;

	//大于内容区,直接显示在可视区域中间;小于贴顶
	if (clientH > obj.height) {
		posT = Math.ceil(scrollTop + (clientH - obj.height) / 2);
	} else {
		posT = scrollTop;
	}

	$('#J_PopupBox .m-popup-content').remove();
	$('#J_PopupBox').html(obj.str).css({
		top: posT,
		marginLeft: posML,
		width: boxW
	});
	$('#J_PopupBg').css({
		width: scrollW,
		height: offsetH + 300
	});
	$('#J_PopupBg,#J_PopupBox').show();
};

//数据类型判断
APP.getType = function(obj) {
	return Object.prototype.toString.call(obj).slice(8, -1);//用call或apply中的参数调用前面的方法
};

/*//对象长度
APP.objectSize = function(obj) {
	var len = 0;
	for (var i in obj) {
		if (obj.hasOwnProperty(i)) len++;//排除 原型属性（通过prototype定义的），只得到直接定义的属性
	return len;
};*/

//首字母大写
APP.upFirst = function(str) {
	return str.replace(/^[a-z]/, function(m) {
		return m.toUpperCase();
	});
};

//年龄
APP.age = function(time) {
	return Math.floor((new Date() - time) / (60 * 60 * 24 * 365 * 1000));
};

//性别转换
APP.sex = function(sexV) {
	return sexV == 1 ? '男' : '女';
};


//PHP时间戳转2016-04-07格式
APP.timeToDate = function(time, ms) {
	var now = time ? new Date(time * 1000) : new Date();
	month = (now.getMonth() + 1) < 10 ? ('0' + (now.getMonth() + 1)) : (now.getMonth() + 1);
	date = now.getDate() < 10 ? '0' + now.getDate() : now.getDate();
	hour = now.getHours() < 10 ? '0' + now.getHours() : now.getHours();
	minute = now.getMinutes() < 10 ? '0' + now.getMinutes() : now.getMinutes();
	s = now.getSeconds() < 10 ? '0' + now.getSeconds() : now.getSeconds();

	if (ms) {
		return now.getFullYear() + '-' + month + '-' + date + ' ' + hour + ':' + minute;
	}

	return now.getFullYear() + '-' + month + '-' + date;
};

//跳转
APP.jump = function(url) {
	window.location.href = url;
};

//n月后日期
APP.addMonths = function(base, n) {
	var base = new Date(base),
		year = base.getFullYear(),
		month = base.getMonth() + 1,
		date = base.getDate();

	newMonth = month + (n - 0);
	if (newMonth >= 12) {
		year += Math.floor(newMonth / 12);
		month = newMonth % 12;
	} else {
		month = newMonth;
	}

	ymd = year + '-' + (month < 10 ? '0' + month : month) + '-' + (date < 10 ? '0' + date : date);
	days = Math.ceil((new Date(ymd).getTime() - base.getTime()) / (1000 * 60 * 60 * 24));

	return {
		ymd: ymd,
		days: days
	};
};

//guid
APP.guid = function() {
	function s4() {
		return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
	}
	return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
		s4() + '-' + s4() + s4() + s4();
};

if (!Array.prototype.indexOf) {
	//自定义 判断数组是否包含某元素的方法 prototype这样 数组就可以直接调用该方法了
	Array.prototype.indexOf = function(searchElement, fromIndex) {

		var k;
		if (this == null) {
			throw new TypeError('"this" is null or not defined');
		}
		var O = Object(this);
		var len = O.length >>> 0;

		if (len === 0) {
			return -1;
		}

		var n = +fromIndex || 0;

		if (Math.abs(n) === Infinity) {
			n = 0;
		}

		if (n >= len) {
			return -1;
		}

		k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

		while (k < len) {
			if (k in O && O[k] === searchElement) {
				return k;
			}
			k++;
		}
		return -1;
	};
}
//对象长度
APP.objectSize = function(obj) {
	var len = 0;
	for (var i in obj) {
		if (obj.hasOwnProperty(i)) len++;//排除 原型属性（通过prototype定义的），只得到直接定义的属性
	}
	return len;
};

//PHP时间戳转2016-04-07格式
APP.timeToDate = function(time, ms) {
	var now = time ? new Date(time * 1000) : new Date();
	month = (now.getMonth() + 1) < 10 ? ('0' + (now.getMonth() + 1)) : (now.getMonth() + 1);
	date = now.getDate() < 10 ? '0' + now.getDate() : now.getDate();
	hour = now.getHours() < 10 ? '0' + now.getHours() : now.getHours();
	minute = now.getMinutes() < 10 ? '0' + now.getMinutes() : now.getMinutes();
	s = now.getSeconds() < 10 ? '0' + now.getSeconds() : now.getSeconds();

	if (ms) {
		return now.getFullYear() + '-' + month + '-' + date + ' ' + hour + ':' + minute;
	}

	return now.getFullYear() + '-' + month + '-' + date;
};

//跳转
APP.jump = function(url) {
	window.location.href = url;
};

//n月后日期
APP.addMonths = function(base, n) {
	var base = new Date(base),
		year = base.getFullYear(),
		month = base.getMonth() + 1,
		date = base.getDate();

	newMonth = month + (n - 0);
	if (newMonth >= 12) {
		year += Math.floor(newMonth / 12);
		month = newMonth % 12;
	} else {
		month = newMonth;
	}

	ymd = year + '-' + (month < 10 ? '0' + month : month) + '-' + (date < 10 ? '0' + date : date);
	days = Math.ceil((new Date(ymd).getTime() - base.getTime()) / (1000 * 60 * 60 * 24));

	return {
		ymd: ymd,
		days: days
	};
};

//guid
APP.guid = function() {
	function s4() {
		return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
	}
	return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
		s4() + '-' + s4() + s4() + s4();
};

if (!Array.prototype.indexOf) {
	//自定义 判断数组是否包含某元素的方法 prototype这样 数组就可以直接调用该方法了
	Array.prototype.indexOf = function(searchElement, fromIndex) {

		var k;
		if (this == null) {
			throw new TypeError('"this" is null or not defined');
		}
		var O = Object(this);
		var len = O.length >>> 0;

		if (len === 0) {
			return -1;
		}

		var n = +fromIndex || 0;

		if (Math.abs(n) === Infinity) {
			n = 0;
		}

		if (n >= len) {
			return -1;
		}

		k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

		while (k < len) {
			if (k in O && O[k] === searchElement) {
				return k;
			}
			k++;
		}
		return -1;
	};
}