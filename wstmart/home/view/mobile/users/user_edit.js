function closeClassify(){
	layer.closeAll();
}

function userName(){
	var html = $("#changeUserName").html();
	var pageii = layer.open({
	  	type: 1
	  	,content: html
	  	,anim: 'up'
	  	,style: 'background-color:#f5f5f5;position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;'
	});
}
function changeUserName(){
	var userName = $(".userName:eq(1)").val();	
	$.post(WST.U('home/users/changeUserName'), {userName:userName}, function(data){
		if(data.status == 1){
			layer.closeAll();
			$("#userName").html(userName);
			$(".userName:eq(0)").attr('value',userName);
			layer.open({style:'width:60%;font-size:25px;',content:'修改成功',time:1});
		}else{
			layer.open({style:'width:60%;font-size:25px;',content:'修改失败',time:1});
		}
	});
}

function trueName(){
	var html = $("#changeTrueName").html();
	var pageii = layer.open({
	  	type: 1
	  	,content: html
	  	,anim: 'up'
	  	,style: 'background-color:#f5f5f5;position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;'
	});
}
function changeTrueName(){
	var trueName = $(".trueName:eq(1)").val();
	$.post(WST.U('home/users/changeTrueName'), {trueName:trueName}, function(data){
		if(data.status == 1){
			layer.closeAll();
			$("#trueName").html(trueName);
			$(".trueName:eq(0)").attr('value',trueName);
			layer.open({style:'width:60%;font-size:25px;',content:'修改成功',time:1});
		}else{
			layer.open({style:'width:60%;font-size:25px;',content:'修改失败',time:1});
		}
	});
}

function userSex(){
	var html = $("#changeUserSex").html();
	// $("#changeUserSex").empty();
	var pageii = layer.open({
	  	type: 1
	  	,content: html
	  	,anim: 'up'
	  	,style: 'background-color:#f5f5f5;position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;'
	});
}
function changeUserSex(){
	var userSex = $("input[name='userSex']:checked").val();
	$.post(WST.U('home/users/changeUserSex'), {userSex:userSex}, function(data){
		if(data.status == 1){
			layer.closeAll();
			layer.open({style:'width:60%;font-size:25px;',content:'修改成功',time:1});
			setTimeout(function(){
				location.reload();
			}, 800);
		}else{
			layer.open({style:'width:60%;font-size:25px;',content:'修改失败',time:1});
		}
	});
}

function brithday(brithday){
	$.post(WST.U('home/users/getBrithday'), {brithday:brithday}, function(data){
		var pageii = layer.open({
		  	type: 1
		  	,content: data
		  	,anim: 'up'
		  	,style: 'background-color:#f5f5f5;position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;'
		});
		var calendar = new LCalendar();
		calendar.init({
			'trigger': '#brithday', //标签id
			'type': 'date', //date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择,
			'minDate': (new Date().getFullYear()-100) + '-' + 1 + '-' + 1, //最小日期
			'maxDate': (new Date().getFullYear()+0) + '-' + 12 + '-' + 31 //最大日期
		});
	});	
}
function changeBrithday(){
	var brithday = $("#brithday").val();
	$.post(WST.U('home/users/changeBrithday'), {brithday:brithday}, function(data){
		if(data.status == 1){
			layer.closeAll();
			layer.open({style:'width:60%;font-size:25px;',content:'修改成功',time:1});
			setTimeout(function(){
				location.reload();
			}, 800);
		}else{
			layer.open({style:'width:60%;font-size:25px;',content:'修改失败',time:1});
		}
	});
}

function userQQ(){
	var html = $("#changeUserQQ").html();
	var pageii = layer.open({
	  	type: 1
	  	,content: html
	  	,anim: 'up'
	  	,style: 'background-color:#f5f5f5;position:fixed; left:0; top:0; width:100%; height:100%; border: none; -webkit-animation-duration: .5s; animation-duration: .5s;'
	});
}
function changeUserQQ(){
	var userQQ = $(".userQQ:eq(1)").val();
	$.post(WST.U('home/users/changeUserQQ'), {userQQ:userQQ}, function(data){
		if(data.status == 1){
			layer.closeAll();
			$("#userQQ").html(userQQ);
			$(".userQQ:eq(0)").attr('value',userQQ);
			layer.open({style:'width:60%;font-size:25px;',content:'修改成功',time:1});
		}else{
			layer.open({style:'width:60%;font-size:25px;',content:'修改失败',time:1});
		}
	});
}

function userPhoto(){
	location.href = WST.U('home/users/getChangePhoto');
}