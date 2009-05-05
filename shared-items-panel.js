function check_url ( that, $ )
{
	var math_share_id = /(http|https):\/\/(www.)?google.com\/reader\/shared\/([0-9]+)\/?/i;
	var share_url = $(that).val ( );
	var matches = share_url.match(math_share_id);
	if (  matches != null && matches[3].length == 20 )
	{
		$("#adv_paypal_url").css({backgroundColor:'lightgreen'});
		$("#adv_share_id").val( matches[3] );
		$("#filled_share_id").text ( matches[3] ).css({color:'green'});
	}
	else
	{
		$("#adv_paypal_url").css({backgroundColor: '#CD5C5C'});
		$("#filled_share_id").text ( 'Incorrect shared items URL?' ).css({color: 'red'});
	}
}
jQuery(document).ready ( function ( $ )
{
	
	$("#adv_paypal_url")
		.change ( function ( ) { check_url ( this, $ ); } )
		.keyup ( function ( ) { check_url ( this, $ ); } )
	;
});
