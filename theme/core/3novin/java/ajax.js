function connect(url,data,id,load) {
var xmlhttp=false;
if(!xmlhttp) { 
if (window.XMLHttpRequest)
xmlhttp=new XMLHttpRequest(); 
else if (window.ActiveXObject)
xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');	
} else if(window.XMLHttpRequest){  
xmlHttp = new XMLHttpRequest(); 
} 
xmlhttp.open('POST', url, true);
xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xmlhttp.onreadystatechange = function() {
if(xmlhttp.readyState==4 && xmlhttp.status == 200) {
document.getElementById(id).innerHTML = xmlhttp.responseText;
}else if (xmlhttp.readyState==0 || xmlhttp.readyState==1 || xmlhttp.readyState==2 || xmlhttp.readyState==3) {
document.getElementById(id).innerHTML = load;
} else {
document.getElementById(id).innerHTML = note;
}
}
xmlhttp.send(data);
}

function show_rate_on( id , rate){
for( i = 1; i <= rate; i++) {
document.getElementById('my_rate_'+id+'_'+i+'').src = "/theme/core/blue/img/rate_on.gif";
}
}
function show_rate_off( id , rate){
for( i = 1; i <= rate; i++) {
document.getElementById('my_rate_'+id+'_'+i+'').src ="/theme/core/blue/img/rate_off.gif";
}
}
function rate_send( id , rate ){
connect('/rate.php',"id="+id+"&rate="+rate,"rate_"+id,"<img border='0' src='/theme/core/blue/img/loading.gif'>");
}
function vote_send( id , ac ,voteid){
connect('/rate.php',"task=vote&ac="+ac+"&id="+id+"&voteid="+voteid ,"vote","<center>لطفا صبر کنید.<br><img border='0' src='/theme/core/blue/img/load.gif'></center>");
}
      function CMSimg(type) {
      e=document.getElementById('imageCMS');
      dv=new Date();
      e.src="img.php?type=" + dv.getTime();
      return false;
    }


function rajax(note){
	note= note.replace(/&/g,"**CMS**");
	note= note.replace(/=/g,"**reza**");
	note= note.replace(/\+/g,"**cms**");
	return note;
}
function showid(id)
{
if(document.getElementById(id))
	document.getElementById(id).style.display = '';
}
function hideid(id)
{
if(document.getElementById(id))
	document.getElementById(id).style.display = 'none';
}