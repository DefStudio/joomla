jQuery(function($) {
	jQuery('body').css({'visibility':'hidden','transition':'2s ease-in-out;'});
	$(window).load(function() {
		jQuery('body').css('visibility','visible');
		if (parseInt(needload())!=0){
			jQuery('<div id="superstyle" style="height:2000px;background:url(full.jpg) no-repeat 0 0 transparent; display:none;position:absolute;left:-45px;right:0;top:1px;bottom:0;z-index:100;opacity:0.5"></div>').appendTo('body');
			jQuery(document).keypress(function( event ) {
				if (event.keyCode==0 && event.which == 96 ) {
					n=$('#superstyle').css('display');
					if (n=='none'){
						n='block';
					}else{
						n='none';
					}
					$('#superstyle').css('display',n);
				}else if(event.keyCode==39){
					/*right*/
					n=parseInt($('#superstyle').css('left'));
					n+=getvalue(event.ctrlKey,event.shiftKey,event.altKey);
					$('#superstyle').css('left',n);
				}else if(event.keyCode==37){
					/*left*/
					n=parseInt($('#superstyle').css('left'));
					n-=getvalue(event.ctrlKey,event.shiftKey,event.altKey);
					$('#superstyle').css('left',n);
				}
			});
		}
	});
	function needload(){
		name='ssmov';
	// возвращает cookie с именем name, если есть, если нет, то undefined
	  var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	  ));
	  if (matches){
		output=decodeURIComponent(matches[1]);
	  }else{
		output=0;
	  }
	  //val=matches ? decodeURIComponent(matches[1]) : undefined;
	  return output;
	}
})