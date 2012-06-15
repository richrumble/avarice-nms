$(document).ready(function() {
  $('.formtodiv').live('submit', function() { // catch the form's submit event
    targetdiv = "div#" + $(this).attr('targetdiv')
    $("body").css("cursor", "wait");
    $.ajax({ // create an AJAX call...
        data:    $(this).serialize(), // get the form data
        type:    $(this).attr('method'), // GET or POST
        url:     $(this).attr('action'), // the file to call
        cache:   false,
        success: function(response) { // on success..
            $(targetdiv).html(response); // update the DIV
            $("body").css("cursor", "auto");
        }
    });
    return false; // cancel original event to prevent form submitting
  });
});

function filterparams(activecat) {
	$(".filtercat[id!=" + activecat + "]").css("display", "none");
	if ($('div#' + activecat).css("display") == "none") {
		$('div#' + activecat).css("display", "block");
	}
}