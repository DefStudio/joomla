var previewActive=false;function kPreviewHelper(c){var b=jQuery("#editor");if(Joomla.getOptions("com_kunena.suffixpreview")){var a="index.php?option=com_kunena&view=topic&layout=edit&format=raw";}else{var a=jQuery("#kpreview_url").val();}if(b.val()!==null){jQuery.ajax({type:"POST",url:a,async:true,dataType:"json",data:{body:b.val()}}).done(function(d){jQuery("#kbbcode-preview").html(d.preview);}).fail(function(){});}}jQuery(document).ready(function(b){var h=b(".qreply");var d=b("#editor");b("#tabs_kunena_editor a:first").tab("show");b("#tabs_kunena_editor a:last").click(function(m){m.preventDefault();var l=b("#kbbcode-preview");var k=b("#editor");l.css("display","block");k.hide();kPreviewHelper();l.attr("class","kbbcode-preview-bottom controls");var j=k.css("height");l.css("height",j);});b("#tabs_kunena_editor a:not(:last)").click(function(j){b("#kbbcode-preview").hide();d.css("display","inline-block");b("#markItUpeditor").css("display","inline-block");});b("#tabs_kunena_editor a:last").click(function(j){d.hide();b("#markItUpeditor").hide();});var f=localStorage.getItem("copyKunenaeditor");if(f){var g=b("#editor").next();g.empty();b("#editor").val(f);localStorage.removeItem("copyKunenaeditor");}b("#reset").onclick=function(){localStorage.removeItem("copyKunenaeditor");};if(b("#kemojis_allowed").val()==1){var i="";if(d.length>0&&h.length==0){i="#editor";}else{if(h.length>0){i=".qreply";}}if(b("#wysibb-body").length>0){i="#wysibb-body";}if(i!=undefined){b(i).atwho({at:":",displayTpl:"<li data-value='${key}'>${name} <img src='${url}' height='20' width='20' /></li>",insertTpl:"${name}",callbacks:{remoteFilter:function(j,k){if(j.length>0){b.ajax({url:b("#kurl_emojis").val(),data:{search:j}}).done(function(l){k(l.emojis);}).fail(function(){});}}}});}}if(i!==undefined){var a=b("#kurl_users").val();b(i).atwho({at:"@",data:a,limit:5});}if(b.fn.sisyphus!==undefined){b("#postform").sisyphus({locationBased:true,timeout:5});}b("#kshow_attach_form").click(function(){if(b("#kattach_form").is(":visible")){b("#kattach_form").hide();}else{b("#kattach_form").show();}});b("#form_submit_button").click(function(){b("#subject").attr("required","required");b("#editor").attr("required","required");localStorage.removeItem("copyKunenaeditor");});var c;b("#postcatid").change(function(){var k=b("select#postcatid option").filter(":selected").val();var l=b("#kurl_topicons_request").val();if(b("#kanynomous-check").length>0){var j=jQuery.parseJSON(Joomla.getOptions("com_kunena.arrayanynomousbox"));if(j[k]!==undefined){b("#kanynomous-check").show();b("#kanonymous").prop("checked",true);}else{b("#kanynomous-check").hide();b("#kanonymous").prop("checked",false);}}b.ajax({type:"POST",url:l,async:true,dataType:"json",data:{catid:k}}).done(function(o){b("#iconset_topic_list").remove();var n=b("<div>",{id:"iconset_topic_list"});b("#iconset_inject").append(n);b.each(o,function(r,s){if(s.type!=="system"){if(s.id===0){var p=b("<input>",{type:"radio",id:"radio"+s.id,name:"topic_emoticon",value:s.id}).prop("checked",true);}else{var p=b("<input>",{type:"radio",id:"radio"+s.id,name:"topic_emoticon",value:s.id});}var t=b("<span>",{"class":"kiconsel"}).append(p);if(Joomla.getOptions("com_kunena.kunena_topicicontype")==="B2"){var q=b("<label>",{"class":"radio inline","for":"radio"+s.id}).append(b("<span>",{"class":"icon icon-topic icon-"+s.b2,border:"0",al:""}));}else{if(Joomla.getOptions("com_kunena.kunena_topicicontype")==="fa"){var q=b("<label>",{"class":"radio inline","for":"radio"+s.id}).append(b("<i>",{"class":"fa glyphicon-topic fa-2x fa-"+s.fa,border:"0",al:""}));}else{var q=b("<label>",{"class":"radio inline","for":"radio"+s.id}).append(b("<img>",{src:s.path,border:"0",al:""}));}}t.append(q);b("#iconset_topic_list").append(t);}});}).fail(function(){});c=function m(){return b.ajax({type:"POST",url:b("#kurl_category_template_text").val(),async:true,dataType:"json",data:{catid:k}}).done(function(n){if(b("#editor").val().length>1){if(b("#editor").val().length>1){b("#modal_confirm_template_category").modal("show");}else{b("#editor").val(c);}}else{if(n.length>1){b("#modal_confirm_template_category").modal("show");}else{b("#editor").val(n);}}}).fail(function(){});}();});b("#modal_confirm_erase").click(function(){b("#modal_confirm_template_category").modal("hide");var j=b("#editor").next();j.empty();b("#editor").val(c.responseJSON);});b("#modal_confirm_erase_keep_old").click(function(){b("#modal_confirm_template_category").modal("hide");var k=d.val();var j=b("#editor").next();j.empty();b("#editor").val(c.responseJSON+" "+k);});if(b.fn.datepicker!==undefined){b("#datepoll-container .input-append.date").datepicker({orientation:"top auto"});}var e=Joomla.getOptions("com_kunena.kunena_quickreplymesid");b("#gotoeditor"+e).click(function(){localStorage.setItem("copyKunenaeditor",b(".test"+e).val());});});