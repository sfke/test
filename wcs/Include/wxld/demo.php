<?php
$wxld_id = $_GET['wxld_id'];
?>
<html>
<head>
<script language="javascript" src="jquery.js"></script>
<script language="javascript" src="wxld.js"></script>
<style>
.select_ld{width:900px;}
</style>
</head>
<body>


<input type="hidden" name='cid' id="wxld"  value="<?php echo $wxld_id;?>"/>
<div class="select_ld"></div>

</body>
<script>
    //getLD('jl_wiki_category','wc_id','wc_pid','wc_name','wc_id',0);
    
    $(function(){
        if($("#wxld").val()=='')getLD('jl_wxld','id','fid','title','id',10);
        else  regetLD('jl_wxld','id','fid','title','id',10);
    })
    
    
</script>
</html>