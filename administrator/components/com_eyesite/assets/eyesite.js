function eyesite_check_for_update()
{
	var url = '?option=com_eyesite&task=ajax_status&format=raw&tmpl=component&lang=en';
	jQuery.ajax({
		url: url,
		type: 'get',
		success: function(responseText, status, xhr) {eyesite_handle_response(responseText);},
		});
}

function eyesite_handle_response(responseText)
{
	jQuery('#ajax_response').html(responseText);
	if (typeof window.eyesite_responseText === 'undefined')	// first time through
		{
		window.eyesite_scanning = false;
		window.eyesite_responseText = responseText;
		window.eyesite_responseCount = 0;
		setTimeout(function(){eyesite_check_for_update()},2000);	// check again in 2 seconds
		return;
		}
		
	var scanning = false;
	if (responseText.indexOf('eyesite_scanning') > -1)
		scanning = true;
		
	if (window.eyesite_scanning && !scanning)			    // if we were scanning and now we're not ..
		window.location.href = window.location.href;	    // .. re-load the page
	window.eyesite_scanning = scanning;

	if (window.eyesite_responseText != responseText)		// if the response has changed
		{
		window.eyesite_responseText = responseText;
		window.eyesite_responseCount = 0;					// reset the count, and ..
		setTimeout(function(){eyesite_check_for_update()},2000);	// check again in 2 seconds
		return;
		}
	window.eyesite_responseCount ++;						// if the response hasn't changed, increment the count
	if (window.eyesite_responseCount >= 60)					// if it hasn't changed for 60 refreshes (2 minutes)
		{
		setTimeout(function(){eyesite_check_for_update()},5000);	// check again in 5 seconds
		return;
		}
	setTimeout(function(){eyesite_check_for_update()},2000);		// count is < 60 - check again in 2 seconds
}