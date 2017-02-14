<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.feedback.php 60166 2016-11-05 23:20:51Z lindonb $

function smarty_function_feedback($params, $smarty)
{
	$result = Feedback::get();

	TikiLib::lib('header')->add_js(
		'
$(document).ajaxComplete(function (e, jqxhr) {
	var feedback = jqxhr.getResponseHeader("X-Tiki-Feedback");
	if (feedback) {
		var fb = $($.parseHTML(feedback)).filter("#tikifeedback").html();
		var divtop = $("#tikifeedback").offset().top;
		var wintop = $(window).scrollTop();
		if (divtop < wintop) {
			$("#tikifeedback").offset({"top": wintop});
			$("#tikifeedback").addClass("ajaxmoved");
			$("#tikifeedback").css("z-index", 3000);
			$(window).on("scroll");
		}
		$("#tikifeedback").fadeIn(200, function() {
			$("#tikifeedback").html(fb);
		});
	}
	$("#tikifeedback .clear").on("click", function () {
		$("#tikifeedback").empty();
		return false;
	});
});
$(window).scroll(function(){
	if ($("#tikifeedback").hasClass("ajaxmoved")) {
		$("#tikifeedback").fadeOut();
		$("#tikifeedback").empty();
		$("#tikifeedback").fadeIn();
		var coltop = $("#col1").offset().top;
		$("#tikifeedback").offset({"top": coltop});
		$("#tikifeedback").removeClass("ajaxmoved");
	}
});
		'
	);

	if (is_array($result)) {
		$smarty->assign('tikifeedback', $result);
	}
	$ret = $smarty->fetch('feedback/default.tpl');
	return $ret;
}

