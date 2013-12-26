
function WPSNS_getElementsByClassName(tagName, className){
	var list = [], allList = document.getElementsByTagName(tagName);
	for(var i = 0;i < allList.length;i++){
		if(allList[i].className == className){
			list[list.length] = allList[i];
		}
	}
	return list;
}

function WPSNS_init(){
	var WPSNS_block = WPSNS_getElementsByClassName("ul", "WPSNS_ul");
	for(var num = 0;num < WPSNS_block.length;num++){
		var WPSNS_Li_list = WPSNS_block[num].childNodes;
		for(var i = 0;i < WPSNS_Li_list.length;i++){
			var item = WPSNS_Li_list[i];
			if(item.className == "WPSNS_item"){
				var item_a = item.firstChild;
				//not element node || it's element node but not A node
				while(item_a.nodeType != 1 || item_a.tagName.toUpperCase() != "A"){
					item_a = item_a.nextSibling;
				}
				if(item_a.tagName.toUpperCase() == "A"){
					item_a.onmouseover = function(e){
						var evt = window.event ? window.event.srcElement : e.target;
						var ee = evt;
						while(ee.nodeType != 1 || ee.tagName.toLowerCase() != 'em'){
							ee = ee.nextSibling;
						}
						ee.style.display = 'block';
					};
					item_a.onmouseout = function(e){
						var evt = window.event ? window.event.srcElement : e.target;
						var ee = evt;
						while(ee.nodeType != 1 || ee.tagName.toLowerCase() != 'em'){
							ee = ee.nextSibling;
						}
						ee.style.display = 'none';
					};
				}
			}
		}
	}
}

function shareToSNS(sns, use_tiny) {
	var url = encodeURIComponent(location.href);
	var title = document.title;
	if(use_tiny == 0 || !useTinyURL(sns)){
		share(url, title, sns);
	}
	else if(use_tiny == 1){
		var tiny = document.getElementById("wp-sns-share-tiny").value;
		if(tiny != ""){
			share(tiny, title, sns);
		}
		else{
			share(url, title, sns);
		}
	}
}

function share(url, title, sns){
	var shareURL = "";
	var width = 626;
	var height = 436;
	var blog = document.getElementById("wp-sns-share-blog").value;
	var desc = document.getElementById("wp-sns-share-desc").value;
	var from = document.getElementById("wp-sns-share-from").value;
	var weibo_content = '';
	if(from == 'single'){
		weibo_content = blog + ' 更新文章： 《' + title + '》 ' + desc;
	}
	else{
		weibo_content = '分享 ' + blog + ' 的网站';
		if(desc != ''){
			weibo_content += '，简介：' + desc;
		}
	}
	title = encodeURIComponent(title);
	weibo_content = encodeURIComponent(weibo_content);
	
	if (sns == "renren") {
		shareURL = 'http://share.xiaonei.com/share/buttonshare.do?link='+ url + '&title='+ title;
	} else if (sns == "douban") {
		shareURL = 'http://www.douban.com/recommend/?url='+ url + '&title='+ title;
	} else if (sns == "kaixin") {
		width = 1050;
		height = 600;
		shareURL = 'http://www.kaixin001.com/~repaste/repaste.php?&rurl='+ url + '&rtitle='+ title;
	} else if (sns == "sina") {
		shareURL = 'http://v.t.sina.com.cn/share/share.php?title='+ weibo_content + '&url='+ url;
	} else if (sns == "t163") {
		var source = '网易微博';
		shareURL = 'http://t.163.com/article/user/checkLogin.do?source='+ source 
					+'&info='+ weibo_content + ' ' + url + '&' + new Date().getTime();
	} else if (sns == "tsohu") {
		var width = 700;
		shareURL = 'http://t.sohu.com/third/post.jsp?content=utf-8&title='+ weibo_content + '&url='+ url;
	} else if (sns == "fanfou") {
		var d = encodeURIComponent(window.getSelection ? window.getSelection().toString()
					: document.getSelection ? document.getSelection()
											: document.selection.createRange().text);
		shareURL = 'http://fanfou.com/sharer?t='+ title + '&u='+ url + '&d=' + d;
	} else if (sns == "qqzone") {
		width = 1050;
		height = 600;
		shareURL = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='
				+ url + '&title='+ title;
	} else if (sns == "tqq") {
		shareURL = 'http://v.t.qq.com/share/share.php?title='+ weibo_content + '&url='+ url;
	} else if (sns == "baidu") {
		width = 1050;
		height = 600;
		shareURL = 'http://apps.hi.baidu.com/share/?url='+ url + '&title='+ title;
	} else if (sns == "gmark") {
		width = 800;
		height = 700;
		shareURL = 'http://www.google.com/bookmarks/mark?op=add&bkmk='+ url + '&title='+ title;
	} else if (sns == "gplus") {
		width = 600;
		height = 320;
		shareURL = 'https://plus.google.com/share?url='+ url + '&title='+ title;
	} else if (sns == "delicious") {
		width = 1050;
		height = 835;
		shareURL = 'http://del.icio.us/post?url='+ url + '&title='+ title;
	} else if (sns == "twitter") {
		width = 800;
		height = 515;
		shareURL = 'http://twitter.com/home?status='+ weibo_content + ' ' + url;
	} else if (sns == "facebook") {
		shareURL = 'http://www.facebook.com/sharer.php?u='+url+'&t='+title;
	} else if (sns == "linkedin") {
		shareURL = 'http://www.linkedin.com/shareArticle?url='+url+'&title='+title;
	}
	
	if(!document.all)
		window.open(shareURL, title, 'toolbar=0,resizable=1,scrollbars=yes,status=1,width=' 
				+ width + ',height=' + height);
	else
		window.open(shareURL);
}

function useTinyURL(sns){
	var list = new Array("twitter", "renren");
	for (i in list){
		if(list[i] == sns)
			return true;
	}
	return false;
}

if(window.addEventListener){
	window.addEventListener("load", WPSNS_init, false);
}
else if(window.attachEvent){
	window.attachEvent("onload", WPSNS_init, false);
}
