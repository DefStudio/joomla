/***********************************************************************************************/
/* Main IceTheme template Scripts */
/***********************************************************************************************/

/* default joomla script */

function make_search(){

//		alert(1);
		var lang_ = jQuery('meta[itemprop=inLanguage]').attr('content');

		lang_mas = lang_.split('-');
		jQuery('#prod_lang').val(lang_mas[0]);

		jQuery.ajax({
			type: "GET",
			url: '/get_products.php',
			data: {
				'search' : jQuery('#prod_search').val(),
				'lang' : jQuery('#prod_lang').val(),
				'limit' : jQuery('#prod_limit').val(),
				'type' : jQuery('#prod_type').val()
			},
			beforeSend: function(){ },
			error: function(){ },
			success: function(response){
				if(response.results.length>0){

					var out_line = '';

					jQuery.each(response.results, function( index, value ) {
						out_line += '<a class="line_prod" href="'+value.tlink+'"><span class="line_img"><img src="'+value.image+'"></span><span class="line_text">'+value.name+'</span></a>';
					})

					jQuery('#search_results').html(out_line);
					jQuery('#search_results > div').css({'height':'auto'});
				}else{
					jQuery('#search_results').html('<span class="line_prod">No result</span>');
				}
			}
		});

}

(function($)
{
	$(document).ready(function()
	{

		if($(window).width() < 768){
			if($("div[itemprop='articleBody']").length){
				if($("div[itemprop='articleBody'] table:eq(0) td").length == "2" && $("div[itemprop='articleBody'] iframe").length){
					$("div[itemprop='articleBody'] table:eq(0) ").addClass("mobile-table-two-buttons ");
				}
			}
		}

		$('#prod_search').on('keyup',function(e){
			$('#prod_close').show();
			make_search();
		})

		$('#prod_close').on('click',function(e){
			$('#search_results').html('');
			$('#prod_search').val('');
			$('#prod_close').hide();
		})

		$('*[rel=tooltip]').tooltip()

		// Turn radios into btn-group
		$('.radio.btn-group label').addClass('btn');
		$(".btn-group label:not(.active)").click(function()
		{
			var label = $(this);
			var input = $('#' + label.attr('for'));

			if (!input.prop('checked')) {
				label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
				if (input.val() == '') {
					label.addClass('active btn-primary');
				} else if (input.val() == 0) {
					label.addClass('active btn-danger');
				} else {
					label.addClass('active btn-success');
				}
				input.prop('checked', true);
			}
		});
		$(".btn-group input[checked=checked]").each(function()
		{
			if ($(this).val() == '') {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
			} else if ($(this).val() == 0) {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
			} else {
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
			}
		});
	})
})(jQuery);


function sortGrid(colNum, type, item) {
  var tbody = item.getElementsByTagName('tbody')[0];

  // Составить массив из TR
  var rowsArray = [].slice.call(tbody.rows);

  // определить функцию сравнения, в зависимости от типа
  var compare;

  switch (type) {
	case 'number':
	  compare = function(rowA, rowB) {
		return rowA.cells[colNum].innerHTML + rowB.cells[colNum].innerHTML;
	  };
	  break;
	case 'string':
	  compare = function(rowA, rowB) {
		return rowA.cells[colNum].innerHTML < rowB.cells[colNum].innerHTML;
	  };
	  break;
  }

  // сортировать
  rowsArray.sort(compare);

  // Убрать tbody из большого DOM документа для лучшей производительности
  item.removeChild(tbody);

  // добавить результат в нужном порядке в TBODY
  // они автоматически будут убраны со старых мест и вставлены в правильном порядке
  for (var i = 0; i < rowsArray.length; i++) {
	tbody.appendChild(rowsArray[i]);
  }

  item.appendChild(tbody);

}



/* jQuery scripts for IceTheme template */
jQuery(document).ready(function() {

	/* initialize bootstrap tooltips */
	jQuery("[rel='tooltip']").tooltip();

	/* language module hover efffect for flags */
	jQuery(".mod-languages li").hover(function () {
		jQuery(".mod-languages li").css({opacity : .25});
	  },
	  function () {
		jQuery(".mod-languages li").css({ opacity : 1});
	  }
	);

	/* effect for the footer menu on hover */
	jQuery("#footer .footermenu ul.nav li a").hover(function () {
		jQuery("#footer .footermenu ul.nav li a").css({color : '#999'});
	  },
	  function () {
		jQuery("#footer .footermenu ul.nav li a").css({ color : '#555'});
	  }
	);

	/* social icons effect on hover */
	jQuery("#social_icons li a").hover(function () {
		jQuery("#social_icons li a").css({opacity : .15});
	  },
	  function () {
		jQuery("#social_icons li a").css({ opacity : .5});
	  }
	);

	/* add a class to icemegamenu module element */
	jQuery(".ice-megamenu-toggle a").attr("href", "#mainmenu")

	/* add some adjustments to joomla articles */
	jQuery(".createdby").prepend("<span class=\"icon-user\"></span>");
	jQuery(".category-name").prepend("<span class=\"icon-folder-close\"></span>");

	/* fade slideshow with white bg on menu hover */
	jQuery("#mainmenu").hover(function(){
		jQuery("#iceslideshow > div > div:first-child").addClass("icemegamenu-hover");
	},
	function(){
	   jQuery("#iceslideshow > div > div:first-child").removeClass("icemegamenu-hover");
	});

	jQuery('.sort_grid').tablesorter();

/*******************************
	jQuery('.sort_grid').each(function(i,item){

		jQuery(item).on('click',function(e){
		  if (e.target.tagName != 'TH') return;
			jQuery(item).find('th').css({'color':'black'});
			jQuery(e.target).css({'color':'green'});
		  // Если TH -- сортируем
		  sortGrid(e.target.cellIndex, e.target.getAttribute('data-type'), item);
		});

		jQuery(item).find('th').css({'color':'black'});

	});
*******************************/

});


// detect if screen is with touch or not (pure JS)
if (("ontouchstart" in document.documentElement)) {
	document.documentElement.className += "with-touch";
}else {
	document.documentElement.className += "no-touch";
}
