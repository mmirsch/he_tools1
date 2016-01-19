		$(document).ready(function(){
			if ($(".tx-hetools-pi1").length) {
				return;
			}			
			var fensterBreite = $(window).width();
			var fensterHoehe = $(window).height();
			var fontgroesse;
			var zoom = 4;
			if (fensterHoehe>(fensterBreite*9/16)) {
				fontgroesse = (fensterBreite*9/16/110);
			}	 else {
				fontgroesse = fensterHoehe/110;
			}
			fontgroesse = fontgroesse*zoom;
			$("html").css("font-size",fontgroesse + "px");
			$(".content").css("float","left");
			$(".content").css("padding","0");
			$(".content").css("margin","0");
			var contentBreite = $(".content").width();
			var contentHoehe = $(".content").height();
			var contentTop = Math.ceil((fensterHoehe-contentHoehe)/2);
			var contentLeft = Math.ceil((fensterBreite-contentBreite)/2);
			$(".content").css("margin-top",contentTop + "px");
			$(".content").css("margin-left",contentLeft + "px");
		});
