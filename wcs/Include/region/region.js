level=0;
stemp=new Array();
sletemp = new Array();
region = new Array();
ssarr = new Array();

function getLD(table,id,fid,name,value,nfid){
region['table']= table;
region['id']= id;
region['fid']= fid;
region['name']= name;
region['value']= value;

 $.post(publicUrlInc+"/region/region.php",
     {table:table,
     id:id,
     fid:fid,
     name:name,
     value:value, 
     nfid:nfid,
     action:1
     },
       function(data){
            if(data==-1)alert("服务器忙，稍后再试！");  
         
            if(data!=''){
            stemp = data;
            ssarr[level] = data;
            newSelect(data);
            }
            else ;

        },'json')


}



    function regetLD(){
        value=$('#regionname').val(); 
		var arrvalue = value.split(",");     
		var m = "";
		for(var i = 0;i<arrvalue.length;i++){
		  m+="<select style='float:left;' class='selects control'><option>"+arrvalue[i]+"</option></select>";
		}
		$(".select_region").html(m+"<input style='float:left;margin-left:5px;' type='button' value='修改' class='submit' onclick='updateLD();'>");
      
    
    }

    function updateLD(){
		$(".select_region").html('');
        getLD(dbpre+'region','id','fid','name','id',1);
    }


function newSelect(arr){

var x = $(".select_region").html(); 
var b = "<select style='float:left;' class='selects control' _l='"+level+"' onchange='change(this)'><option>请选择</option>";
var e = "</select>";
var m = "";

    for(var i = 0;i<arr.length;i++){

        m+="<option value='"+arr[i]['value']+"'>"+arr[i]['name']+"</option>";
    }


y = x+b+m+e;

$(".select_region").html(y);    

level++;

}



function changeSelect(nfid,cLevel){

var arr = ssarr[level-1];

var tlevel = parseInt(level)-1;
if(cLevel<parseInt(level)-1) {
level = parseInt(cLevel)+1;
arr = ssarr[cLevel];
tlevel = cLevel;
}



var x ='';

for(var i=0;i<tlevel;i++){
    x+=sletemp[i];
}


var b = "<select style='float:left;' class='selects class0 control' _l='"+tlevel+"' onchange='change(this)'><option>请选择</option>";
var e = "</select>";
var m = "";

    for(var i = 0;i<arr.length;i++){

        if(nfid!=arr[i]['value'])
        m+="<option value='"+arr[i]['value']+"'>"+arr[i]['name']+"</option>";
        else
        {m+="<option value='"+arr[i]['value']+"' selected>"+arr[i]['name']+"</option>";
         region['sname'] = arr[i]['name'];

         if(region['sname']!=''){
			 if(level-1==0){
		      $("#regionname").val(region['sname']);
			  rn1=region['sname'];
			}
			if(level-1==1){			  
			  rn2=rn1+','+region['sname'];
			  $("#regionname").val(rn2);
			  
			}
			if(level-1==2){
			  rn3=rn2+','+region['sname'];
			  $("#regionname").val(rn3);
			  
			}
			 
		  }
         if(typeof(_regioncallback)!= "undefined"){
        	 _regioncallback.call();
         }
         }
    }


y = x+b+m+e;
$(".select_region").html(y);    
sletemp[tlevel] = b+m+e;
}



function change(obj){

    var nfid = obj.value;
    changeSelect(nfid,$(obj).attr('_l'));
	 
    getLD(region['table'],region['id'],region['fid'],region['name'],region['value'],nfid);

}