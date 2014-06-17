var rowheights = []; var nb_columns = 3;
if (document.URL.match('/?s=')) nb_columns = 4;
// FIX SIZE OF PIN_LIKE PIECES
function realign_columns() {
	// @todo 
	return;
	for (var i = 0; i < Math.floor($(".column_display:visible").length/nb_columns); i++) rowheights[i] = 0;
	$(".column_display:visible").each(function(idx) {
		var row = Math.floor(idx/nb_columns);
		rowheights[row] = Math.max(rowheights[row], $(this).height());
	});
	$(".column_display:visible").each(function(idx) {
		$(this).height(rowheights[Math.floor(idx/nb_columns)]);
		debugger;
	});
}

$(document).ready(function(){
	realign_columns();

	// CHANGE BACKGROUND PATTERN
	$(".change-bg-1").click(function() {
		$("body").css("background-image","url(http://www.medialab.sciences-po.fr/wp-content/themes/medialab_v4/img/bg.jpg)");
	});

	$(".change-bg-2").click(function() {
		$("body").css("background-image","url(http://www.medialab.sciences-po.fr/wp-content/themes/medialab_v4/img/bg-alt-3.jpg)");
	});

	// SORT OBJECTS BY PEOPLE/PROJECTS/TOOLS
	$(".facet").click(function() {
		if ($(this).hasClass("active_facet")) {
			$(".column_display").css("display","block");
			$(".active_facet").removeClass("active_facet");
			$(".active-p-enabled").removeClass("active-p-enabled");
		} else {
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(".active_facet").removeClass("active_facet");
			$(this).addClass("active_facet");
			// remove all elements
			$(".column_display").css("display","none");
			$("."+this.id).css("display","block");
		}
		realign_columns();
	});

	// SORT PEOPLE/PROJECTS BY ACTIVE/INACTIVE STATUS
	if($(".column_display").hasClass("0")){
		$(".0").css("display", "none");
	};
	$(".active-p").click(function active_p() {
		$(".active_facet").removeClass("active_facet");
		if ($(this).hasClass("active-p-enabled")){
			$(".column_display").css("display","block");
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(".actif").css("display", "block");
			$(".inactif").css("display", "block");
		} else {
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(this).addClass("active-p-enabled");
			$(".actif").css("display", "block");
			$(".inactif").css("display","none");
		}
		realign_columns();
	});
	$(".retired-p").click(function() {
		$(".active_facet").removeClass("active_facet");
		if ($(this).hasClass("active-p-enabled")){
			$(".column_display").css("display","block");
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(".actif").css("display","block");
			$(".inactif").css("display", "block");
		} else {
			$(".active-p-enabled").removeClass("active-p-enabled");
			$(this).addClass("active-p-enabled");
			$(".inactif").css("display", "block");
			$(".actif").css("display", "none");
		}
		realign_columns();
	});

	// HIDE SORTER IF EMPTY 
	if($(".related-tools-content").html() == ''){
		$(".related-tools").css("display", "none");
	}
	if($(".related-people-content").html() == ''){
		$(".related-people").css("display", "none");
	}
	if($(".related-projects-content").html() == ''){
		$(".related-projects").css("display", "none");
	}
	if($(".related-publications-content").html() == ''){
		$(".related-publications").css("display", "none");
	}
});
