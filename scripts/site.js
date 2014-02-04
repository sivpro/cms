var num,
	num2 = 0,
	num3 = 0;
$(document).ready(function() {
	num = 0;

	$(".js-left").click(function() {

		if (num-1 > -1) {
			$(".js-slider").eq(num-1).fadeIn(700);
			$(".js-slider").addClass("active");
			$(".js-slider").eq(num).fadeOut(700);
			num--;
		} else {
			$(".js-slider").eq($(".js-slider").size()-1).fadeIn(700);
			$(".js-slider").removeClass("active");
			$(".js-slider").eq(num).fadeOut(700);
			num = $(".js-slider").size()-1;
		}

		clearTimeout(timeoutId);
		dtimeout();
	});


	$(".js-right").click(function() {

		if (num+1 < $(".js-slider").size()) {
			$(".js-slider").eq(num+1).fadeIn(700);
			$(".js-slider").addClass("active");
			$(".js-slider").eq(num).fadeOut(700);
			num++;
		} else {
			$(".js-slider").eq(0).fadeIn(700);
			$(".js-slider").removeClass("active");
			$(".js-slider").eq(num).fadeOut(700);
			num = 0;
		}

		clearTimeout(timeoutId);
		dtimeout();
	});

	dtimeout();

	num = 0;

	$('.box').each(function() {
		var t = $(this),
			box = t.attr("data-box"),
			id = t.attr("data-id"),
			active;

		if (id < 1) {
			$('#circle-holder_'+box).append('<div class="circle circle-a" data-box="'+box+'" data-id="'+id+'"></div>');
		}
		else {
			$('#circle-holder_'+box).append('<div class="circle" data-box="'+box+'" data-id="'+id+'"></div>');
		}
	});


	$('.box-holder .circle').eq(0).addClass('circle-a');
	$('.circle').each(function() { $(this).click(circles); });

	$('.lol').mCustomScrollbar({
		horizontalScroll:true,
		autoDraggerLength:false
	});

	$(".menu-block").hover(function() {
		$(".menu-block-hover", this).animate({ marginTop: 0 }, 150);
		$(".block-cont", this).animate({ marginTop: -134 }, 150);
	}, function() {
		$(".menu-block-hover", this).animate({ marginTop: 134 }, 150);
		$(".block-cont", this).animate({ marginTop: 0 }, 150);
	});

});


function nextSlide() {
	if (num-1 > -1) {
		$(".js-slider").eq(num-1).fadeIn(700);
		$(".js-slider").addclass("active");
		$(".js-slider").eq(num).fadeOut(700);
		num--;
	} else {
		$(".js-slider").eq($(".js-slider").size()-1).fadeIn(700);
		$(".js-slider").removeClass("active");
		$(".js-slider").eq(num).fadeOut(700);
		num = $(".js-slider").size()-1;
	}

	dtimeout();

}

function dtimeout() {
	timeoutId = setTimeout(nextSlide, 5000);
	return timeoutId;
}

function circles() {
	var t = $(this),
		box = t.attr("data-box"),
		animTime = 300,
		newSlide = t.attr("data-id"),
		boxHolder = $("#box-num-holder_"+box);
		num = boxHolder.val();



	if (newSlide > num) {
		$('#box_'+box+'_'+newSlide).css("left", "100%");
		$('#box_'+box+'_'+num).animate({ left: "-100%" }, animTime);
		$('#box_'+box+'_'+newSlide).animate({ left: 0 }, animTime);
		boxHolder.val(newSlide);
	}

	if (newSlide < num) {
		$('#box_'+box+'_'+newSlide).css("left", "-100%");
		$('#box_'+box+'_'+num).animate({ left: "100%" }, animTime);
		$('#box_'+box+'_'+newSlide).animate({ left: 0 }, animTime);
		boxHolder.val(newSlide);
	}
	$('#circle-holder_'+box+' .circle').removeClass('circle-a');

	$(this).addClass('circle-a');
}

ymaps.ready(function () {
	var address,
		myGeocoder,
		myMap;
	address ="Екатеринбург, ул.  Декабристов, 14";

	myGeocoder = ymaps.geocode(address);
	myGeocoder.then(
		function (res) {
			var coords = res.geoObjects.get(0).geometry.getCoordinates();

			myMap = new ymaps.Map("ymap", {
				center: coords,
				zoom: 14
			});

			myMap.controls.add('typeSelector');
			myMap.controls.add('zoomControl');


			myGeoObject = new ymaps.GeoObject({
				geometry: {
					type: "Point",
					coordinates: coords
			   }
			});

			myMap.geoObjects.add(myGeoObject);
		}
	);
});
$(function() {
	$("header ul li").hover(function() {
		$(this).toggleClass("show");
	});
	
	$(".prod-nav > ul > li").click(function() {
		$("ul.second-level",this).show();
	});
});