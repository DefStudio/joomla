(function(){var e=function(e){var t=this,n=function(){(function(t,n){"$:nomunge";var r;e.throttle=r=function(t,r,i,s){function a(){function l(){u=+(new Date),i.apply(e,f)}function c(){o=n}var e=this,a=+(new Date)-u,f=arguments;s&&!o&&l(),o&&clearTimeout(o),s===n&&a>t?l():r!==!0&&(o=setTimeout(s?c:l,s===n?t-a:t))}var o,u=0;return typeof r!="boolean"&&(s=i,i=r,r=n),e.guid&&(a.guid=i.guid=i.guid||e.guid++),a},e.debounce=function(e,t,i){return i===n?r(e,t,!1):r(e,i,t!==!1)}})(window)};n(),t.resolveWith(n)};dispatch("throttle-debounce").containing(e).to("Foundry/2.1 Modules")})();