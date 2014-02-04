$(document).ready(function(){	

	$(".addressLink", $("#addressList")).click(function() {			
		$(".addressLink", $("#addressList")).removeClass("act");
		$(this).addClass("act");
		$("#addressBasket").val( $(this).html() );
		return false;
	});

	
	
	//count change
	var elems = $("input.good-count");

	$(elems).keydown(function(event){		
		if( (event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106) || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 ) {
			return true; 
		}
		else {	
			return false;
		}
	});

	var elems = $("input.good-count2");

	$(elems).keydown(function(event){		
		if( (event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106) || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 ) {
			return true; 
		}
		else {	
			return false;
		}
	});

	$(elems).keyup(function(event){
		if( (event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106) || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 ) {
			var id = this.id;			
			id = id.substr(12);			
			var t = 0;
			if (this.value == "" || this.value == 0) t = 1;
			else t = parseInt(this.value);	
			if(isNaN(t)) t = 1;			
			
			var leftSide = $("#good-price_"+id).val();
			leftSide = leftSide.replace(" ","");
			leftSide = leftSide.replace(" ","");
			
			var pr = parseFloat(leftSide) * t;			

			$("#good-total-price_"+id).val(pr);
			
			var sum = $("#pricetotal");
			var summ = 0;

			var r = 0;
			$(".good-total-price").each(function() {
				
				r = $(this).val();									
				r = r.replace(" ","");
				r = r.replace(" ","");
				
				summ += parseFloat(r);
			});

			summ2 = addCommas(summ.toString()) + " руб.";
			
			sum.text(summ2);
			
			/*discount*/
			var sum = $("#priceDiscount");					

			summD = (summ * (100 - parseInt( $("#discountValue").text() ))) / 100;
			summD = addCommas(summD.toString()) + " руб.";
			
			sum.text(summD);

			setCount(id, t);
		}
	});

	var elems = $("input.good-count3");

	$(elems).keydown(function(event){		
		if( (event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106) || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 ) {
			return true; 
		}
		else {	
			return false;
		}
	});

	$(elems).keyup(function(event){		
		if( (event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106) || event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode==undefined) {			
			var id = this.id;			
			id = id.substr(12);			
			var t = 0;
			if (this.value == "" || this.value == 0) t = 1;
			else t = parseInt(this.value);	
			if(isNaN(t)) t = 1;			
			
			var leftSide = $("#good-price_"+id).val();
			leftSide = leftSide.replace(" ","");
			leftSide = leftSide.replace(" ","");
			
			var pr = parseFloat(leftSide) * t;			

			$("#good-total-price_"+id).val(pr);
			
			var sum = $("#pricetotal");
			var summ = 0;

			var r = 0;
			$(".good-total-price").each(function() {
				var idd = $(this).attr("id").substr(17);		
				
				if ($("#repeatCh_"+idd).attr("checked") == true) {
					r = $(this).val();									
					r = r.replace(" ","");
					r = r.replace(" ","");
					
					summ += parseFloat(r);
				}
				
				
			});
			
			var summ2 = 0;
			summ2 = addCommas(summ.toString()) + " руб.";
			
			sum.text(summ2);
			
			/*discount*/
			var sum = $("#priceDiscount");					

			summD = (summ * (100 - parseInt( $("#discountValue").text() ))) / 100;
			summD = addCommas(summD.toString()) + " руб.";
			
			sum.text(summD);			
		}


	});

	$(".repeatCh").change(function() {
		var id = this.id;			
		id = id.substr(9);		
		$("#good-count3_"+id).keyup();
	});

	$("a.deleteItem").click(function() {
		$(".confirm").remove();
		var offset = $(this).offset();

		var left = offset.left;
		var top = offset.top;

		var id = $(this).attr("id");
		id = id.substr(11);		
			
		var div = $('<div class="confirm" style="top: '+top+'px; left: '+left+'px;"><p>Удалить?</p><p><a href="#" onclick="deleteItem('+id+'); return false;">Да</a> <a href="#" onclick="hideConfirm(); return false;">Нет</a></p></div>');

		$("body").append(div);
		return false;
	});

});

function hideConfirm() {
	$(".confirm").remove();
}


function addCommas(nStr) {

	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? ',' + x[1] : '';
	x2 = x2.substr(0, 3);
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ' ' + '$2');
	}
	return x1 + x2;
}


/*basket*/

function putItem(id, price) {
	
	var newId = id;	
	var count = $("#good-count_"+id).val();
	
	
	$.post(
	  '/basket',
	  {
		mode: "add",
		item_id: newId,
		count: count,
		price: price
	  },
	  function(data) {
		$("#basket-preloader").fadeOut("slow");	
		$("#block-basket").html(data);
		$("#basket-act").show();
	  }
	);
	$("#basket-preloader").show();
}

function allToBasket() {
	clearBasket();
}

function putItem2(array) {
			$.post(
			  '/basket',
			  {
				mode: "add",
				item_id: array[0]['item_id'],
				count: array[0]['count'],
				price: array[0]['price']
			  },
			  function(data) {
				if (array.length > 1) {
					array.shift();
					putItem2(array);
				}
				else {
					$("#block-basket").html(data);
					$("#basket-preloader").fadeOut("slow");	
					$("#basket-act").show();
				}
			  }
			);
}



function setCount(id, value) {	
	$.post(
		'/basket',
		{
			mode: "setcount",
			item_id: id,
			count: value
		},
		function(data) {
			$("#basket-preloader").fadeOut("slow");	
			$("#block-basket").html(data);	
		}
	);
	$("#basket-preloader").show();
}

function clearBasket() {
	$("#basket-preloader").show();
	$.post(
		'/basket',
		{
			mode: "clear"
		},
		function(data) {
			var i = 0;
			$(".good-count3").each(function() {
				var newId = $(this).attr("id").substr(12);
				if ($("#repeatCh_"+newId).attr("checked") == true) i ++;
			});
			var array = new Array(i);
			i = 0;
			$(".good-count3").each(function() {		
				var newId = $(this).attr("id").substr(12);
				if ($("#repeatCh_"+newId).attr("checked") == true) {
					array[i] = new Array(3);
					array[i]['count'] = $(this).val();
					array[i]['price'] = $("#good-price_"+newId).val();
					array[i]['item_id'] = newId;
					i++;
				}
			});
			putItem2(array);			
		}
	);	
}

function deleteItem(id) {	
	
	var newId = id;		
	
	$.post(
	  '/basket',
	  {
		mode: "delete",
		item_id: newId
	  },
	  function(data) {
			$("#basket-preloader").fadeOut("slow");
			$("#good-simple_"+id).remove();
			$("#good-desc_"+id).remove();
			$(".confirm").remove();
			
			var iss = false;
			$(".tr").each(function() {
				iss = true;
			});

			$("#block-basket").html(data);

			if (!iss) {
				$(".goods").html('<p>Вы еще ничего не заказали.</p><p>Выбрать товары можно в нашем <a href="/catalog">каталоге</a>.</p>');
				$(".reg").hide();
			}

			else {
				var sum = $("#pricetotal");
				var summ = 0;

				var r = 0;
				$(".good-total-price").each(function() {
					
					r = $(this).val();									
					r = r.replace(" ","");
					r = r.replace(" ","");
					
					summ += parseFloat(r);
				});

				summ2 = addCommas(summ.toString()) + " руб.";
				
				sum.text(summ2);


				/*discount*/
				var sum = $("#priceDiscount");					

				summD = (summ * (100 - parseInt( $("#discountValue").text() ))) / 100;
				summD = addCommas(summD.toString()) + " руб.";
				
				sum.text(summD);
			}
		}
	);
	$("#basket-preloader").show();
}

function showAddressForm() {
	$("#addressForm").show();
}

function addAddress() {
	if ($("#addressToAdd").val() != "") {
		var address = $("#addressToAdd").val();
		$.post(
			'/basket',
			{
				mode: 'address',
				address: address
			},
			function(data) {				
				$("#addressToAdd").val("");
				if (data != "no") {
					$("#addressList").append(data);
					$(".addressLink", $("#addressList")).click(function() {			
						$(".addressLink", $("#addressList")).removeClass("act");
						$(this).addClass("act");
						$("#addressBasket").val( $(this).html() );
						return false;
					});
				}
			}
		);
	}
}




