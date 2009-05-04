jQuery(document).ready ( function ( $ )
{
	var math_share_id = /(http|https):\/\/(www.)?google.com\/reader\/shared\/([0-9]+)\/?/i;
	$("#adv_paypal_url")
		.change ( function ( )
		{
			var share_url = $(this).val ( );
			var matches = share_url.match(math_share_id);
			if (  matches[3] != "NULL" )
			{
				$("#adv_share_id").val( matches[3] );
				$("#filled_share_id").text ( matches[3] );
			}
		})
	;
});