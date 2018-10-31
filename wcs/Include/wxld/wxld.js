level=0;
stemp=new Array();
sletemp = new Array();
wxld = new Array();
ssarr = new Array();

function getLD(table,id,fid,name,value,nfid){
wxld['table']= table;
wxld['id']= id;
wxld['fid']= fid;
wxld['name']= name;
wxld['value']= value;
 $.post(publicUrlInc+"/wxld/wxld.php",
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



    function regetLD(table,id,fid,name,value,nfid){
        wxld['table']= table;
        wxld['id']= id;
        wxld['fid']= fid;
        wxld['name']= name;
        wxld['value']= value;
        wxld['nfid']= nfid;
            
         var zid = $("#wxld").val();
            if(zid){
                    $.post(publicUrlInc+"/wxld/wxld.php",
                    {table:table,
                      id:id,
                      fid:fid,
                      name:name,
                      value:value, 
                      zid:zid,
                      nfid:nfid,
                      action:2
                     },
                     function(data){
                            if(data==-1)alert("服务器忙，稍后再试！");
                            else {

                                


                                for(var o in data['select']){
                                    ssarr[level]=data['select'][o];
                                    renewSelect(data['select'][o],data['selected'][o]['value']);
                                    stemp = data['select'][o];
                                    wxld['sname'] = data['selected'][o]['name'];
                                    wxld['svalue'] = data['selected'][o]['value'];
                                }
       
                                
                                
                            } 
                     },'json')
    
    
                }
    
    
    
    
    
    }



function newSelect(arr){

var x = $(".select_ld").html(); 
var b = "<select style='float:left;' class='selects control' _l='"+level+"' onchange='change(this)'><option>请选择</option>";
var e = "</select>";
var m = "";

    for(var i = 0;i<arr.length;i++){

        m+="<option value='"+arr[i]['value']+"'>"+arr[i]['name']+"</option>";
    }


y = x+b+m+e;

$(".select_ld").html(y);    

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
         wxld['sname'] = arr[i]['name'];
         wxld['svalue'] = arr[i]['value'];
         if(wxld['svalue']!='')$("#wxld").val(wxld['svalue']);
         if(wxld['sname']!='')$("#wxldname").val(wxld['sname']);
         if(typeof(_wxldcallback)!= "undefined"){
        	 _wxldcallback.call();
         }
         }
    }


y = x+b+m+e;
$(".select_ld").html(y);    
sletemp[tlevel] = b+m+e;
}



function change(obj){

    var nfid = obj.value;
    changeSelect(nfid,$(obj).attr('_l'));
    getLD(wxld['table'],wxld['id'],wxld['fid'],wxld['name'],wxld['value'],nfid);

}







function renewSelect(arr,selected){

var x = $(".select_ld").html(); 
var b = "<select style='float:left;' class='selects class0' _l='"+level+"' onchange='change(this)'><option>请选择</option>";
var e = "</select>";
var m = "";

    for(var i = 0;i<arr.length;i++){
        if(arr[i]['value'] == selected) var str = "selected"; else var str = "";
        m+="<option value='"+arr[i]['value']+"' "+str+">"+arr[i]['name']+"</option>";
    }


y = x+b+m+e;

$(".select_ld").html(y); 
sletemp[level]=b+m+e;
level++;

}







/*
$(function(){

    //getLD('jl_wiki_category','wc_id','wc_pid','wc_name','wc_id',0);
    getLD('jl_price_type','id','reid','typename','id',0);
    
})
*/