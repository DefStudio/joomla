(function(){var e=function(e){var t=this,n=e;e.require().script("ui/effect").done(function(){var e=function(){(function(e,t){e.effects.effect.highlight=function(t,n){var r=e(this),i=["backgroundImage","backgroundColor","opacity"],s=e.effects.setMode(r,t.mode||"show"),o={backgroundColor:r.css("backgroundColor")};s==="hide"&&(o.opacity=0),e.effects.save(r,i),r.show().css({backgroundImage:"none",backgroundColor:t.color||"#ffff99"}).animate(o,{queue:!1,duration:t.duration,easing:t.easing,complete:function(){s==="hide"&&r.hide(),e.effects.restore(r,i),n()}})}})(n)};e(),t.resolveWith(e)})};dispatch("ui/effect-highlight").containing(e).to("Foundry/2.1 Modules")})();