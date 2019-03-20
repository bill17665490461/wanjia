function shopList(p){
  $('#list').html('<img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...');
  var params = {};
  params = WST.getParams('.s-query');
  params.key = $.trim($('#key').val());
  params.page = p;
  $.post(WST.U('home/shops/agencyShops'),params,function(data,textStatus){
      var json = WST.toJson(data);
      if(!json.data.Rows){
      	$('#list').html('');
      }
      if(json.status==1){
          var gettpl = document.getElementById('tblist').innerHTML;
          laytpl(gettpl).render(json.data.Rows, function(html){
            $('#list').html(html);
            $('.j-lazyImg').lazyload({ effect: "fadeIn",failurelimit : 10,threshold: 200,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});
          });
          if(json.data.TotalPage>1){
            laypage({
               cont: 'pager', 
               pages:json.data.TotalPage, 
               curr: json.data.CurrentPage,
               skin: '#e23e3d',
               groups: 3,
               jump: function(e, first){
                    if(!first){
                      userAppraise(e.curr);
                    }
                  } 
            });
          }else{
            $('#pager').empty();
          }
        }  
  });
}