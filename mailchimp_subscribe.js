/** Example of using mailchimp_subscribe.php utilizing jQuery **/

$(document).ready(function() {
	$('.subscribe').submit(function() {
		storeAddress(this);
		return false;
	});
});

function storeAddress(form) {
	// update user interface
	$(form).children('.message').html('Adding email address...');

	// Prepare query string and send AJAX request
	$.ajax({
		url: '/subscribe.php',
		data: 'email=' + escape($(form).children("[name='email']").val()),
		success: function(output) {
			$(form).children('.message').html(output.message);
			ga('send','event','Email Specials','Click','Subscribe');
		}
	});
}
