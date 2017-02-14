// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_survey_questions.js 59837 2016-09-28 16:36:57Z jonnybradley $

$(document).ready(function() {

	var listDirty = false;

	var setupList = function() {
		$(".surveyquestions tbody").sortable({

			opacity:.6,

			stop:function (event, ui) {
				if ($(".save_list:visible").length === 0) {
					$(".save_list").show("fast").parent().show("fast");
					listDirty = true;
				}
			}

		}).disableSelection();
	};

	$(window).on("beforeunload", function() {
		if (listDirty) {
			return tr("You have unsaved changes to your survey, are you sure you want to leave the page without saving?");
		}
	});

	setupList();

	$(".save_list").click(function(){

		var $ids = $(this).parent().find(".surveyquestions td.id");
		$(".surveyquestions").tikiModal(tr("Saving..."));

		var data = $ids.map(function () {
			return $(this).text();
		}).get().join();

		listDirty = false;
		$("input[name=questionIds]", "#reorderForm").val(data);
		$("#reorderForm").submit();

		return false;
	});

});

