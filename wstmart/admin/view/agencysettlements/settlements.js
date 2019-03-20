var grid;
function initGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/agencysettlements/pageAgencyQuery'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '<input type="checkbox" onclick="WST.checkChks(this,\'.chk_1\')"/>', width:30,name: 'orderNo',isSort: false,render: function (rowdata, rowindex, value){
            	return '<input type="checkbox" id="s_'+rowdata['agencyId']+'" class="chk_1" value="'+rowdata['agencyId']+'" dataval="'+rowdata['realName']+'"/>';
            }},
	        { display: '区域商姓名', name: 'realName',isSort: false},
	        { display: '联系电话', name: 'telephone',isSort: false},
	        { display: '待结算订单数', name: 'noSettledOrderNum',isSort: false},
	        { display: '待结算佣金', name: 'noSettledOrderFee',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '操作', name: 'op',isSort: false,render: function (rowdata, rowindex, value){
	            var h = "<a href='javascript:toView(" + rowdata['agencyId'] + ")'>订单列表</a>&nbsp;&nbsp;";
	            return h;
	        }}
        ]
    });
}
function toView(id){
   location.href=WST.U('admin/agencysettlements/toOrders','id='+id);
}
function initOrderGrid(id){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/agencysettlements/pageAgencyOrderQuery','id='+id),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单号', name: 'orderNo',isSort: false},
	        { display: '支付方式', name: 'payTypeName',isSort: false},
	        { display: '商品金额', name: 'goodsMoney',isSort: false},
	        { display: '运费', name: 'deliverMoney',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '订单总金额', name: 'totalMoney',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '实付金额', name: 'realTotalMoney',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '佣金', name: 'agencyFee',isSort: false,render: function (rowdata, rowindex, value){
	        	return '¥'+value;
	        }},
	        { display: '下单时间', name: 'createTime',isSort: false}
        ]
    });
}
function loadAgencyGrid(){
	var areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	grid.set('url',WST.U('admin/agencysettlements/pageAgencyQuery','realName='+$('#realName').val()+"&areaIdPath="+areaIdPath));
}
function loadOrderGrid(){
	var id = $('#id').val();
    grid.set('url',WST.U('admin/agencysettlements/pageAgencyOrderQuery','orderNo='+$('#orderNo').val()+"&payType="+$('#payType').val()+'&id='+id));
}
var generateNo = 0;
var agencys = [];
function generateSettle(){
	var agencyId = agencys[generateNo];
	var realName = $('#s_'+agencyId).attr('dataval');

	var load = WST.msg('正在生成【'+realName+'】结算单，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/agencysettlements/generateSettleByAgency'),{id:agencyId},function(data,textStatus){
		// console.log(data);return;
		layer.close(load);
		var json = WST.toAdminJson(data);
			if(json.status==1){
				if(generateNo<(agencys.length-1)){
					generateNo++;
		            generateSettle();
				}else{
                    WST.msg(json.msg);
                    loadAgencyGrid();
				}
		}else{
			WST.msg(json.msg);
			loadAgencyGrid();
		}
	});
}
function generateSettleByAgency(){
	var ids = WST.getChks('.chk_1');
	if(ids.length==0){
		WST.msg('请选择要结算的区域商!',{icon:2});
		return;
	}
	agencys = ids;
	WST.confirm({content:'您确定生成选中区域商的结算单吗？',yes:function(){
        generateNo = 0;
	    generateSettle();
	}});
}