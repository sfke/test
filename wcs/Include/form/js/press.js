function AddAnchor(){
	var action=document.getElementById('imgtemp').value;
	$.layer({
		type : 2,
		maxmin : true,
		shadeClose : true,
		title : '编辑锚点',
		shade : [0.1 , '#fff'],
		offset : ['20px',''],
		area : ['600px', ($(window).height()-50)+'px'],
		iframe : {src: action }
	})

	
}
