function initEdit(){
	KindEditor.ready(function(K) {
		editor1 = K.create('textarea[name="shopContent"]', {
		  height:'350px',
		  width:'800px',
		  uploadJson : WST.conf.ROOT+'/home/shops/editorUpload',
		  allowFileManager : false,
		  allowImageUpload : true,
		  items:[
			          'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
			          'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
			          'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
			          'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
			          'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
			          'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|','image','multiimage','table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
			          'anchor', 'link', 'unlink', '|', 'about'
		  ],
		  afterBlur: function(){ this.sync(); }
		});
	});
}

function save(){
	$('#editform').isValid(function(v){
		if(v){
			var params = WST.getParams('.j-ipt');
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
			$.post(WST.U('home/shops/contentSave'),params,function(data,textStatus){
				// console.log(data);return;
		    	layer.close(loading);
		    	var json = WST.toJson(data);
		    	if(json.status=='1'){
		    		WST.msg(json.msg,{icon:1});
		    		location.reload();
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}