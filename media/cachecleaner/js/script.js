/**
 * Main JavaScript file
 *
 * @package         Cache Cleaner
 * @version         3.7.0
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var cachecleaner_delay = false;
(function($) {
	$(document).ready(function() {
		$('a.cachecleaner_link').each(function(i, el) {
			$(el).click(function() {
				cachecleaner_load();
				return false;
			});
		});

		jQuery('<span/>', {
			id: 'cachecleaner_msg',
			css: { 'opacity': 0 },
			click: function() { cachecleaner_show_end() }
		}).appendTo('body');

		cachecleaner_delay = false;
	});

	var cachecleaner_load = function() {
		var d = new Date();
		var url = cachecleaner_base + '/index.php?cleancache=1&break=1&time=' + d.toISOString();

		cachecleaner_show_start();
		jQuery.ajax({
			type: 'get',
			url: url,
			success: function(data) {
				var classname = 'warning';
				if (data.length > 100) {
					$('#cachecleaner_msg').addClass('btn-danger').html(cachecleaner_msg_inactive);
					cachecleaner_show_end(4000);
				} else {
					if (data.charAt(0) == '+') {
						data = data.substring(1, data.length);
						$('#cachecleaner_msg').addClass('btn-success');
					} else {
						$('#cachecleaner_msg').addClass('btn-warning');
					}
					$('#cachecleaner_msg').html(data);
					cachecleaner_show_end(2000);
				}
			},
			error: function(data) {
				$('#cachecleaner_msg').addClass('btn-danger').html(cachecleaner_msg_failure);
				cachecleaner_show_end(2000);
			}
		});
	};

	var cachecleaner_show_start = function() {
		$('#cachecleaner_msg')
			.html('<img src="' + cachecleaner_root + '/media/cachecleaner/images/loading.gif" /> ' + cachecleaner_msg_clean)
			.removeClass('btn-success').removeClass('btn-warning').removeClass('btn-danger').addClass('visible');

		clearInterval(cachecleaner_delay);
		$('#cachecleaner_msg').fadeTo('fast', 0.8);
	};

	var cachecleaner_show_end = function(delay) {
		if (delay) {
			setTimeout(function() {
				cachecleaner_show_end();
			}, delay);
		} else {
			clearInterval(cachecleaner_delay);
			$('#cachecleaner_msg').fadeOut();
		}
	};
})(jQuery);
