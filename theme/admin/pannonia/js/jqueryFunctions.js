function ajaxDialog( url , data )
{
	var postData = '';
	if( data )
	{
        var arr = {};
        parse_str(data, arr);
        $.each(arr,function(key,value)
        {
            postData += key+'='+encodeURIComponent(value)+'&';
        });
	}
    $.ajax(
	{
      url: url,
      type: 'POST',
	  data : postData,
      success: function(data) 
	  {
              jQuery.facebox(data);
        //$("<div></div>").html(data).dialog({modal: true,width:'auto'}).fancybox({href: '#fancybox'});
		$(".colorpicker").colorpicker({
			format: 'hex'
		});
		$(".pluginData").submit(function()
		{
			var formId   = $( this ).attr("id").split("_")[1];
			var formData = $( this ).serialize();
			$( "#postionDataBox_" + formId ).val( $.base64.encode( formData ) );
			$(".ui-dialog-content").dialog("close");
			return false;
		});
      },
	  error : function()
	  {
		$("<div></div>").html('خطا در برقراری ارتباط با سرور').dialog({modal: true}).dialog('open');
	  }
    });
}
function appendAjaxDialogLinks()
{
	jQuery(".ajaxDialog").unbind("click").click(function()
	{
		if( $( this ).attr( 'rel' ) && $( "#" + $( this ).attr( 'rel' ) ) )
		{
			ajaxDialog( $( this ).attr( 'href' ), 'data=' + $( "#" + $( this ).attr( 'rel' ) ).val() );
		}
		else
		{
			ajaxDialog( $( this ).attr( 'href' ) );
		}
		return false;
	});
}
jQuery( document ).ready( function() 
{
	appendAjaxDialogLinks();
} );

/* Base64 */
"use strict";jQuery.base64=(function($){var _PADCHAR="=",_ALPHA="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",_VERSION="1.0";function _getbyte64(s,i){var idx=_ALPHA.indexOf(s.charAt(i));if(idx===-1){throw"Cannot decode base64"}return idx}function _decode(s){var pads=0,i,b10,imax=s.length,x=[];s=String(s);if(imax===0){return s}if(imax%4!==0){throw"Cannot decode base64"}if(s.charAt(imax-1)===_PADCHAR){pads=1;if(s.charAt(imax-2)===_PADCHAR){pads=2}imax-=4}for(i=0;i<imax;i+=4){b10=(_getbyte64(s,i)<<18)|(_getbyte64(s,i+1)<<12)|(_getbyte64(s,i+2)<<6)|_getbyte64(s,i+3);x.push(String.fromCharCode(b10>>16,(b10>>8)&255,b10&255))}switch(pads){case 1:b10=(_getbyte64(s,i)<<18)|(_getbyte64(s,i+1)<<12)|(_getbyte64(s,i+2)<<6);x.push(String.fromCharCode(b10>>16,(b10>>8)&255));break;case 2:b10=(_getbyte64(s,i)<<18)|(_getbyte64(s,i+1)<<12);x.push(String.fromCharCode(b10>>16));break}return x.join("")}function _getbyte(s,i){var x=s.charCodeAt(i);if(x>255){throw"INVALID_CHARACTER_ERR: DOM Exception 5"}return x}function _encode(s){if(arguments.length!==1){throw"SyntaxError: exactly one argument required"}s=String(s);var i,b10,x=[],imax=s.length-s.length%3;if(s.length===0){return s}for(i=0;i<imax;i+=3){b10=(_getbyte(s,i)<<16)|(_getbyte(s,i+1)<<8)|_getbyte(s,i+2);x.push(_ALPHA.charAt(b10>>18));x.push(_ALPHA.charAt((b10>>12)&63));x.push(_ALPHA.charAt((b10>>6)&63));x.push(_ALPHA.charAt(b10&63))}switch(s.length-imax){case 1:b10=_getbyte(s,i)<<16;x.push(_ALPHA.charAt(b10>>18)+_ALPHA.charAt((b10>>12)&63)+_PADCHAR+_PADCHAR);break;case 2:b10=(_getbyte(s,i)<<16)|(_getbyte(s,i+1)<<8);x.push(_ALPHA.charAt(b10>>18)+_ALPHA.charAt((b10>>12)&63)+_ALPHA.charAt((b10>>6)&63)+_PADCHAR);break}return x.join("")}return{decode:_decode,encode:_encode,VERSION:_VERSION}}(jQuery));

/* parse Str */
function parse_str(e,t){var n=String(e).replace(/^&/,"").replace(/&$/,"").split("&"),r=n.length,i,s,o,u,a,f,l,c,h,p,d,v,m,g,y,b=function(e){return decodeURIComponent(e.replace(/\+/g,"%20"))};if(!t){t=this.window}for(i=0;i<r;i++){p=n[i].split("=");d=b(p[0]);v=p.length<2?"":b(p[1]);while(d.charAt(0)===" "){d=d.slice(1)}if(d.indexOf("\0")>-1){d=d.slice(0,d.indexOf("\0"))}if(d&&d.charAt(0)!=="["){g=[];m=0;for(s=0;s<d.length;s++){if(d.charAt(s)==="["&&!m){m=s+1}else if(d.charAt(s)==="]"){if(m){if(!g.length){g.push(d.slice(0,m-1))}g.push(d.substr(m,s-m));m=0;if(d.charAt(s+1)!=="["){break}}}}if(!g.length){g=[d]}for(s=0;s<g[0].length;s++){h=g[0].charAt(s);if(h===" "||h==="."||h==="["){g[0]=g[0].substr(0,s)+"_"+g[0].substr(s+1)}if(h==="["){break}}f=t;for(s=0,y=g.length;s<y;s++){d=g[s].replace(/^['"]/,"").replace(/['"]$/,"");l=s!==g.length-1;a=f;if(d!==""&&d!==" "||s===0){if(f[d]===c){f[d]={}}f=f[d]}else{o=-1;for(u in f){if(f.hasOwnProperty(u)){if(+u>o&&u.match(/^\d+$/g)){o=+u}}}d=o+1}}a[d]=v}}}

/*text effect ehsan */
jQuery(function($) {
    var text, chars, $el, i, output;

    // Iterate over all class occurences
    $('.textToHalfStyle').each(function(idx, el) {
        $el = $(el);
        text = $el.text();
        chars = text.split('');

        // Set the screen-reader text
        $el.html('<span style="position: absolute !important;clip: rect(1px 1px 1px 1px);clip: rect(1px, 1px, 1px, 1px);">' + text + '</span>');

        // Reset output for appending
        output = '';

        // Iterate over all chars in the text
        for (i = 0; i < chars.length; i++) {
            // Create a styled element for each character and append to container
            output += '<span aria-hidden="true" class="halfStyle" data-content="' + chars[i] + '">' + chars[i] + '</span>';
        }

        // Write to DOM only once
        $el.append(output);
    });
});