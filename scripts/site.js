$(document).ready(function() {
	var $container;

	//RESIZE FIRST SCREEN
	ELGROW.resizeFirstBlock();
	$(window).resize(ELGROW.resizeFirstBlock);

	$('#video-bg').bind('progress', function() {
		var $video = $(this),
			bar = $("#video-progress"),
			percent = this.buffered.end(0) / this.duration,
			calcWidth = percent * 100;
		bar.width(calcWidth+"%");
		if (calcWidth > 99) {
			$(".progress-bar").hide();
		}
	});

	$(".big-select").chosen({
		no_results_text: "Ничего не найдено"
	});
	$(".small-select").chosen({
		no_results_text: "Ничего не найдено"
	});

	$('.styled-input').iCheck({
		checkboxClass: 'icheckbox_flat',
		radioClass: 'iradio_flat'
	});

	$('.styled-input-dark').iCheck({
		checkboxClass: 'icheckbox_flat-grey',
		radioClass: 'iradio_flat-grey'
	});

	$("#priceTour").ionRangeSlider({
		type: "double",
		postfix: " р"
	});

	$(".range-slider").ionRangeSlider({
		type: "double"
	});


	if ($("#fixed-nav").attr("id") != "undefined") {
		$("#fixed-nav").scrollToFixed({
			minWidth: 1024
		});
	}

	// MENU COLLAPSER
	$(".menu-button").click(ELGROW.menuCollapse);

	// POPUPS
	$(".popup").on("click", function() {
		var $t = $(this),
			href = $t.attr("href"),
			$w,
			effectIn = "flipInX",
			effectOut = "flipOutX";



		$.fancybox({
			autoWidth: true,
			autoHeight: true,
			fitToView	: true,
			autoSize	: true,
			closeClick	: false,
			openEffect	: 'none',
			closeEffect	: 'none',
			closeBtn : false,
			wrapCSS : 'form',
			padding : 0,
			openSpeed: 0,
			closeSpeed: 0,
			href: href
		});

		$w = $(".fancybox-wrap");

		$w.addClass("animated " + effectIn);
		$w.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
			$w.removeClass("animated " + effectIn + " " + effectOut);
		});

		return false;
	});

	// POPUP CLOSE
	$(".popup-close").click(function() {
		$.fancybox.close();
		return false;
	});

	// Drop-down menu click
	$(".dropping").click(ELGROW.dropdown);

	// AUTH POPUP
	$("#authButton").on("click", function() {
		var $t = $(this),
			href = $t.attr("href"),
			$w = $t.next(),
			vis = $w.is(":visible"),
			effectIn = "flipInX",
			effectOut = "flipOutX",
			windowWidth = $(window).width();

		if (vis) {
			$w.removeClass("fancy popup-block");
			$w.addClass("animated " + effectOut);
			$w.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
				$w.removeClass("animated " + effectIn + " " + effectOut).hide();
			});
		}
		else {
			if (windowWidth > 1160) {
				$w.toggle().addClass("animated " + effectIn);
				$w.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
					$w.removeClass("animated " + effectIn + " " + effectOut);
				});
			}
			else {
				$w.addClass("fancy popup-block");
				$.fancybox({
					autoWidth: true,
					autoHeight: true,
					fitToView	: true,
					autoSize	: true,
					closeClick	: false,
					openEffect	: 'none',
					closeEffect	: 'none',
					closeBtn : false,
					wrapCSS : 'form',
					padding : 0,
					openSpeed: 0,
					closeSpeed: 0,
					href: href
				});

				$w = $(".fancybox-wrap");

				$w.addClass("animated " + effectIn);
				$w.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
					$w.removeClass("animated " + effectIn + " " + effectOut);
				});
			}
		}

		return false;
	});

	// AUTH POPUP CLOSE
	$(".auth-close").on("click", function() {
		var $t = $(this),
			$w = $("#auth-popup"),
			effectIn = "flipInX",
			effectOut = "flipOutX";

		$w.removeClass("fancy popup-block");
		$w.addClass("animated " + effectOut);
		$w.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
			$w.removeClass("animated " + effectIn + " " + effectOut).hide();
		});

		return false;
	});

	// SLIDER
	$("#team-slider").slick({
		autoplay: false,
		autoplaySpeed: 5000,
		arrows: true,
		dots: false,
		slide: ".slide",
		slidesToShow: 5,
		slidesToScroll: 1,
		draggable: false,
		responsive: [
			{
				breakpoint: 1200,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 900,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 700,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
	});


	$("#team-slider-inner").slick({
		autoplay: false,
		autoplaySpeed: 5000,
		arrows: true,
		dots: false,
		slide: ".slide",
		slidesToShow: 5,
		slidesToScroll: 1,
		draggable: false,
		responsive: [
			{
				breakpoint: 1300,
				settings: {
					slidesToShow: 4,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 1100,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 800,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1
				}
			},
			{
				breakpoint: 520,
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}
		]
	});

	// Local scroll
	$("#nav-buttons").localScroll();
	$("#scroll").localScroll();

	// Map-route
	$("#map-route").click(function() {
		var $t = $(this),
			href = $t.attr("href");
		$.fancybox({
			autoWidth: true,
			autoHeight: true,
			fitToView	: true,
			autoSize	: true,
			closeClick	: false,
			openEffect	: 'none',
			closeEffect	: 'none',
			closeBtn : false,
			wrapCSS : 'form',
			padding : 0,
			openSpeed: 0,
			closeSpeed: 0,
			href: href,
			type: "iframe",
			iframe: {
				preload: true,
				scrolling : 'no'
			}
		});

		return false;
	});

	// User types
	$(".usertype").iCheck({
		checkboxClass: 'icheckbox_flat',
		radioClass: 'iradio_flat'
	})
	.on("ifClicked", function() {
		var $t = $(this),
			val = $t.val();

		$("#typeReg").val(val);

		$(".type-text").slideUp(300, function() {
			$("#type-text_"+val).slideDown(300);
		});
	});

	// Agree user terms
	$("#agreeReg").on("ifChanged", function() {
		var $t = $(this),
			button = $("#sendReg"),
			checked = $t.prop("checked");

		if (checked) {
			button.removeClass("disabled").prop("disabled", false);
		}
		else {
			button.addClass("disabled").prop("disabled", true);
		}
	});

	// Paswords
	$("input[type=password]").sauron();

	// Auth
	$("#authSubmit").click(ELGROW.auth);

	// Logout
	$("#logoutButton").click(ELGROW.logout);

	// City chooser
	$("#city-chooser").change(function() {
		var $t = $(this),
			$name = $("#city-name"),
			$selected = $t.find("option:selected"),
			val = $selected.val(),
			text = $selected.text();

		$name.text(text);

		$(".dealers-box")
			.removeOverscroll()
			.hide();


		$("#dealers-box_"+val)
			.show()
			.overscroll({
				direction:"vertical",
				hoverThumbs: true,
				thumbColor: "#729944"
			});
	});

	// Add to compare
	$(".compare-add").click(ELGROW.compareAdd);

	// Delere from compare
	$(".compare-delete").click(ELGROW.compareDelete);

	// Ajax load items
	$(".page-next").click(ELGROW.loadMore);

	// Byu tour
	$("#open-buy-tour").click(function() {
		$("#buy-tour").slideDown();
		$(".hidden-select").chosen({
			no_results_text: "Ничего не найдено",
			width: "200px"
		});
		return false;
	});

	// Add adult
	$("#add-adult").click(function() {
		var $adult = $("#tourist-adult_0"),
			$clone = $adult.clone(),
			$container = $("#adult-container"),
			realNumber = touristAdult++ + 1,
			price = $("#priceoneBuy").val() * touristAdult;

		// Изменяем цену тура
		$("#priceBuy").val(price);
		$("#pricevisBuy").text(price);

		// Разблокируем кнопку заказа
		$("#sendBuy").prop("disabled", false);

		// Изменяем data-id удаления
		$clone.find(".delete-tourist").attr("data-id", touristAdult);

		//Добавляем, ставим новый ID и показываем
		$clone.appendTo($container).attr("id", "tourist-adult_"+touristAdult).show();

		// Присваиваем required
		$clone.find(".required").prop("required", true);

		// Задаем радио новое имя
		$clone.find(".radio-styled").attr("name", "genderBuy_"+touristAdult)

		// Стайлим чекбоксы
		$clone.find(".styled-input-dark-after")
			.iCheck({
				checkboxClass: 'icheckbox_flat-grey',
				radioClass: 'iradio_flat-grey'
			})
			.on("ifToggled", function(e) {
				var $t = $(this);
				if ($t.prop("checked") === true) {
					$t.parent().parent().next().val("1");
				}
				else {
					$t.parent().parent().next().val("0");
				}
			});

		// Стайлим радио
		$clone.find(".styled-radio-dark-after")
			.iCheck({
				checkboxClass: 'icheckbox_flat-grey',
				radioClass: 'iradio_flat-grey'
			})
			.on("ifClicked", function(e) {
				var $t = $(this),
					val = $t.val();

				$t.parents(".radio-input").find(".gender").val(val);

			});

		// Изменяем номер туриста
		$clone.find("p.name").text("Турист "+realNumber);

		return false;
	});

	// Add child
	$("#add-child").click(function() {
		var $adult = $("#tourist-child_0"),
			$clone = $adult.clone(),
			$container = $("#child-container"),
			realNumber = touristChild++ + 1;

		// Изменяем data-id удаления
		$clone.find(".delete-tourist").attr("data-id", touristChild);

		//Добавляем, ставим новый ID и показываем
		$clone.appendTo($container).attr("id", "tourist-child_"+touristChild).show();

		// Присваиваем required
		$clone.find(".required").prop("required", true);

		// Задаем радио новое имя
		$clone.find(".radio-styled").attr("name", "genderChildBuy_"+touristChild)

		// Стайлим радио
		$clone.find(".styled-radio-dark-after")
			.iCheck({
				checkboxClass: 'icheckbox_flat-grey',
				radioClass: 'iradio_flat-grey'
			})
			.on("ifClicked", function(e) {
				var $t = $(this),
					val = $t.val();

				$t.parents(".radio-input").find(".gender").val(val);

			});

		// Изменяем номер туриста
		$clone.find("p.name").text("Ребенок "+realNumber);

		return false;
	});

	// Delete adult
	$("body").on("click", ".delete-adult", function() {
		var $t = $(this),
			id = $t.attr("data-id"),
			price;

		if (!id) return false;

		$("#tourist-adult_" + id).remove();
		touristAdult --;

		// Изменяем цену тура
		price = $("#priceoneBuy").val() * touristAdult;
		$("#priceBuy").val(price);
		$("#pricevisBuy").text(price);

		return false;
	});

	// Delete child
	$("body").on("click", ".delete-child", function() {
		var $t = $(this),
			id = $t.attr("data-id");

		if (!id) return false;

		$("#tourist-child_" + id).remove();
		touristChild --;

		return false;
	});

	// Table highlight
	var table = $('#compare-table');

	if (table.attr("id") != "undefined") {
		table.wholly();

		table.on('wholly.mouseenter', 'td, th', function () {
			$(this).addClass('highlight');
		});

		table.on('wholly.mouseleave', 'td, th', function () {
			$(this).removeClass('highlight');
		});
	}

	// Equip
	ELGROW.equip();
	$(window).resize(ELGROW.equip);

});

var ELGROW = {
	loadMore: function() {
		var $t = $(this),
			url = $t.attr("href"),
			itemSelector = $t.attr("data-item"),
			containerSelector = $t.attr("data-container"),
			$container = $(containerSelector);

		$.get(
			url,
			function(data) {
				var $items = $(containerSelector + " " + itemSelector, $(data)),
					$next = $(".page-next", $(data));

				// Ставим новую ссылку на кнопку загрузки элементов
				if (typeof $next.attr("data-item") !== "undefined") {
					$t.attr("href", $next.attr("href"));
				}
				else {
					$t.hide();
				}
				$container.append($items);
				if (containerSelector === "#info-list") {
					$container.masonry( 'appended', $items);
				}
			}
		);
		return false;
	},

	compareAdd: function() {
		var $t = $(this),
			id = $t.attr("data-id"),
			type = $t.attr("data-type"),
			$tourA = "<span onclick='document.location.href=\"/compare/\"; return false;'>Добавлено к сравнению</span>",
			$excA = "<span onclick='document.location.href=\"/compare/_aexc/\"; return false;'>Добавлено к сравнению</span>"

		if (typeof id === "undefined") return false;
		if (typeof type === "undefined") return false;

		$.post(
			"/compare/",
			{
				id: id,
				type: type,
				mode: "add"
			},
			function(data) {
				if (type == "t") {
					$t.replaceWith($tourA);
				}
				else {
					$t.replaceWith($excA);
				}
			}
		);

		return false;
	},

	compareDelete: function() {
		var $t = $(this),
			id = $t.attr("data-id"),
			type = $t.attr("data-type"),
			selector = $(".compareItem_"+id);

		$.post(
			'/compare/',
			{
				id: id,
				type: type,
				mode: "delete"
			},
			function(data) {
				selector.hide(300, function() {

				});
			}
		);

		return false;
	},

	equip: function() {
		$(".item", $("#equip-page")).each(function() {
			var $t = $(this),
				$text = $t.find(".text-wrapper"),
				height = $text.outerHeight(),
				$image = $t.find(".image");

			$image.height(height);
		});
	},

	resizeFirstBlock: function() {
		var windowHeight = $(window).outerHeight(),
			width = $(window).width(),
			$wrapper = $(".first-screen-wrapper"),
			wrapperHeight = $wrapper.outerHeight(),
			$block1 = $("#first-block"),
			block1Height = $block1.height(),
			$header = $(".header"),
			headerHeight = $header.outerHeight(),
			$vC = $(".video-controls"),
			$video = $("#video-bg"),
			paddingValue,
			x = 1280,
			y = 720,
			ratio = x / y,
			newWidth,
			newHeight,
			gap;


		$block1.css("paddingTop", 0);
		$block1.css("paddingBottom", 0);
		$wrapper.height("auto");

		if (windowHeight >= block1Height + headerHeight) {
			$wrapper.height(windowHeight - headerHeight);
			paddingValue = (windowHeight - headerHeight - block1Height) / 2;
			$block1.css("paddingTop", paddingValue);
			$block1.css("paddingBottom", paddingValue);
		}

		wrapperRatio = $wrapper.width() / $wrapper.outerHeight();
		$video.css({
			top: 0,
			left: 0
		});

		if (wrapperRatio >= ratio) {
			newWidth = $wrapper.width();
			newHeight = newWidth / x * y;

			gap = (newHeight - $wrapper.outerHeight()) / 2;

			$video.width(newWidth).height(newHeight).css("top", "-"+gap+"px");
		}
		if (wrapperRatio < ratio) {
			newHeight = $wrapper.outerHeight();
			newWidth = newHeight / y * x;

			gap = (newWidth - $wrapper.width()) / 2;

			$video.width(newWidth).height(newHeight).css("left", "-"+gap+"px");
		}
		$video.addClass("loaded");
	},

	isVisible: function(id) {

		var elem = $(id),
			offsetTop = elem.offset().top,
			scrollTop = $(window).scrollTop();

		if (scrollTop > offsetTop-50) {
			return true;
		}
		return false;
	},

	dropdown: function() {
		var t = $(this),
			active = t.hasClass("selected"),
			menu = t.next();

		$(".drop-menu").hide();
		$(".menulink").removeClass("selected");

		if (active) {
			t.removeClass("selected");
			menu.hide();
		}
		else {
			t.addClass("selected");
			menu.show();
			$("body").click(function(e) {
				if ($(e.target).attr("class") != "dropmenu") {
					$(".menulink").removeClass("selected");
					menu.hide();
				}
			});
		}

		return false;
	},

	menuCollapse: function() {
		var menu = $(".flexnav"),
			controller = $(".menu-button"),
			visibility = menu.is(":visible");

		if (visibility) {
			menu.removeAttr("style");
		}
		else {
			menu.slideDown();
		}
	},

	logout: function() {
		$.post(
			"/wt/",
			{
				mode: "logout"
			},
			function(data) {
				document.location.href = '/';
			}
		);
		return false;
	},

	auth: function() {
		var login = $("#loginAuth").val(),
			password = $("#passwordAuth").val();

		if (login == "" || password == "") {
			$("#showErrorsAuth").slideDown();
			return false;
		}

		$("#showErrorsAuth").hide();

		$.post(
			"/wt/",
			{
				login: login,
				password: password,
				mode: "login"
			},
			function(data) {
				if (data == "ok") {
					window.location.href = window.location.href.split('#')[0];
				}
				else {
					$("#showErrorsAuth").slideDown().html(data);
				}
			}
		);
		return false;
	}

};

var successSend = function(param) {
	var id = "#successSend"+param,
		$w,
		effectIn = "flipInX",
		effectOut = "flipOutX";

	if (param == "Os" || param == "Reg" || param == "Buy") {
		$("#send"+param).hide();
	}

	$.fancybox.close();
	$.fancybox(id,
		{
			autoWidth: true,
			autoHeight: true,
			fitToView	: true,
			autoSize	: true,
			closeClick	: false,
			openEffect	: 'none',
			closeEffect	: 'none',
			closeBtn : false,
			wrapCSS : 'form',
			padding : 0,
			openSpeed: 0,
			closeSpeed: 0
		}
	);

	$w = $(".fancybox-wrap");

	$w.addClass("animated " + effectIn);
	$w.one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
		$w.removeClass("animated " + effectIn + " " + effectOut);
	});

	window.location.hash = param+"?form_send";
};



