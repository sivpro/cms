var sizes = [],
	sizesCount = 0;

function iframeLoaded(id) {
	var iFrameID = document.getElementById(id);
	if(iFrameID) {
		iFrameID.height = "";
		iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
	}
}

$(document).ready(function() {
	$(".trtree").removeClass("click");
	$(document).click(function() {
		$(".trtree").removeClass("click");
	});

	// Переключение языков в админке
	$(".lang-change").click(function() {
		var t = $(this),
			lang = t.attr("data-id");

		$.post(
			"/",
			{
				mode : "lang",
				lang : lang
			},
			function(data) {
				window.location.href = window.location.href;
			}
		);
		return false;
	});

	//Сохранение миниатюры
	$("#imageSave").on("click", saveMini);

	//Удаление изображения
	$(".erase-button").on("click", deleteImage);

	// Перемещение блоков к другому родителю
	$("#moveToSelect").change(moveTo);



	//TinyMce
	tinymce.init({
		selector: ".elgrow_html",
		relative_urls: false,
		language : 'ru',
		fontsize_formats: "0.75em 0.875em 1em 1.125em 1.25em 1.5em 1.75em 2em",
		style_formats: [
			{
				title: 'Изображение слева',
				selector: 'img',
				styles: {
					'float' : 'left',
					'margin': '0 20px 20px 0'
				}
			},
			{
				title: 'Изображение справа',
				selector: 'img',
				styles: {
					'float' : 'right',
					'margin': '0 0 20px 20px'
				}
			}
		],
		image_advtab: true,
		plugins : 'advlist autolink link image lists charmap print code paste table textcolor visualblocks filemanager media',
		toolbar : 'undo redo | bold italic underline strikethrough subscript superscript | alignleft aligncenter alignright alignjustify | forecolor backcolor | formatselect fontsizeselect | bullist numlist | outdent indent | link unlink anchor | image media |removeformat'
	});

	//TinyMce readonly
	tinymce.init({
		selector: ".elgrow_html_readonly",
		readonly: 1,
		relative_urls: false,
		language : 'ru',
		fontsize_formats: "0.75em 0.875em 1em 1.125em 1.25em 1.5em 1.75em 2em",
		style_formats: [
			{
				title: 'Изображение слева',
				selector: 'img',
				styles: {
					'float' : 'left',
					'margin': '0 20px 20px 0'
				}
			},
			{
				title: 'Изображение справа',
				selector: 'img',
				styles: {
					'float' : 'right',
					'margin': '0 0 20px 20px'
				}
			}
		],
		image_advtab: true,
		plugins : 'advlist autolink link image lists charmap print code paste table textcolor visualblocks filemanager media',
		toolbar : 'undo redo | bold italic underline strikethrough subscript superscript | alignleft aligncenter alignright alignjustify | forecolor backcolor | formatselect fontsizeselect | bullist numlist | outdent indent | link unlink anchor | image media |removeformat'
	});



	//tabs in block template

	$(".tabber").on("click", "li", function() {
		var t = $(this),
			tab = t.attr("data-tab");

		curTab = tab;
		$(".tabber li").removeClass("active");
		t.addClass("active");
		$("#tab-content > *").hide();
		$("#addFieldsTable_"+tab).show();

		return false;
	});

	//tabs in block edit
	$(".block-tabber").on("click", "li", function() {
		var t = $(this),
			tab = t.attr("data-tab");

		$(".block-tabber li").removeClass("active");
		t.addClass("active");
		$(".block-tab").hide();
		$("#block-tab_"+tab).show();

		return false;
	});

	// search blocks
	$("#search-blocks-text").keyup(function() {
		var text = $(this).val(),
			regExp = new RegExp("[.]*"+text+"[.]*", "i"),
			table = $("#tree");

		console.log(regExp);

		if (!text) {
			$("#tree tr").show();
			return;
		}

		$("#tree tr:not(:first)").each(function() {
			var t = $(this),
				td = t.find("td"),
				hide = true;

			t.show();

			td.each(function() {
				var tt = $(this),
					val = tt.text(),
					result = regExp.test(val);

				if (result) {
					hide = false;
					return false;
				}
			});

			if (hide) {
				t.hide();
			}


		});
	});

	// maxlength
	$("[data-maxlength]").keyup(function() {
		var $t = $(this),
			$p = $t.prev(),
			$b = $p.find("b"),
			val = $t.val(),
			maxlength = $t.attr("data-maxlength"),
			length = val.length,
			left = maxlength - length;

		if (left < 0) {
			left = 0;
			$t.val($t.val().substr(0, maxlength));
		}

		$b.text(left);
	});
});

function addCat(a) {
	if ($(a).attr("rel") > 0) {
		var parent = $("#hide-parent").val();
		if (!parent) return false;
		window.location.href = '/manage/catedit/_aadd_parent'+parent+'/';
	}

}

function editCat(a) {
	if ($(a).attr("rel") > 0) {
		var parent = $("#hide-parent").val();
		if (!parent) return false;
		window.location.href = '/manage/catedit/_aedit_parent'+parent+'/';
	}
}

function showHideCat(a) {
	if ($(a).attr("rel") > 0) {
		var parent = $("#hide-parent").val();
		if (!parent) return false;
		window.location.href = '/manage/catedit/_ashowhide_parent'+parent+'/';
	}
}

function delCat(a) {
	if ($(a).attr("rel") > 0) {
		var parent = $("#hide-parent").val();
		if (!parent) return false;
		if (confirm('Вы действительно хотите удалить эту страницу и все дочерние?')) {
			window.location.href = '/manage/catedit/_adel_parent'+parent+'/';
		}
		else {
			$(".trtree").removeClass("click");
		}
	}
}

function blockList(a) {
	if ($(a).attr("rel") > 0) {
		var parent = $("#hide-parent").val(),
			page = $("#hide-page").val();

		if (!parent) return false;

		window.location.href = '/manage/blockedit/_alist_parent'+parent+'/';
	}
}

function moveCat(a) {
	if ($(a).attr("rel") > 0) {
		var parent = $("#hide-parent").val();
		$("#tr"+parent+"jscontext").remove();

		beginMove(parent, 'move');

	}
}

function copyCat(a) {
	if ($(a).attr("rel") > 0) {
		var parent = $("#hide-parent").val();
		$("#tr"+parent+"jscontext").remove();

		beginMove(parent, 'copy');

	}
}

function beginMove(parent, mode) {
	var add = $('<img style="position: absolute;" class="moveIco" src="/admin/decor/standart/img/icons/ico-add.png"/>');
	var up = $('<img style="position: absolute;" class="moveIco" src="/admin/decor/standart/img/icons/ico-up.png"/>');
	var down = $('<img style="position: absolute;" class="moveIco" src="/admin/decor/standart/img/icons/ico-down.png"/>');
	$("body").append(add);
	$("body").append(up);
	$("body").append(down);
	$(".moveIco").hide();


		$(".trtree").bind("mousemove", function(e) {
			$(document).bind("keyup", function(e) {
				if (e.keyCode == 27) {
					$(".trtree").unbind("mousemove");
					add.remove();
					up.remove();
					down.remove();
				}
			});
			var curId = $(e.currentTarget).attr("id").substr(2);
			if (parent != curId) {
				var height = $(curId).height();

				var offset = $("#tree").offset();
				var currentMouse = parseInt(e.pageY)  - parseInt(offset.top);
				var currentOffset = e.currentTarget.offsetTop;
				var img, top, newParent, after, before;


				if (currentMouse - currentOffset <= 5 && curId != 1) {
					img = up;
					top = currentOffset + offset.top;

					parentNew = 0;
					after = 0;
					before = curId;
				}
				if (currentMouse - currentOffset >= 15 && curId != 1) {
					img = down;
					top = currentOffset + 4 + offset.top;

					parentNew = 0;
					after = curId;
					before = 0;
				}
				if (currentMouse - currentOffset < 15 && currentMouse - currentOffset > 5) {
					img = add;
					top = e.pageY - 7;

					parentNew = curId;
					after = 0;
					before = 0;
				}

				$(e.currentTarget).bind("click", function() {
					window.location.href = '/manage/catedit/_a'+mode+'_parent'+parent+'_newparent'+parentNew+'_after'+after+'_before'+before+'/';
				});

				$(".moveIco").hide();
				img.show();
				img.css("top", top+"px");
				img.css("left", e.pageX - 25);
			}
		});


}


function editBlock() {
	var parent = $("#hide-parent").val(),
		blockid =  $("#hide-blockid").val(),
		btemplate =  $("#hide-template").val(),
		page = $("#hide-page").val();

	if (!parent) return false;
	window.location.href = '/manage/blockedit/_aedit_id'+blockid+'_template'+btemplate+'_parent'+parent+'_page'+page+'/';
}

function editItemBlock() {
	var parent = $("#hide-parent").val();
	var blockid =  $("#hide-blockid").val();
	var btemplate =  $("#hide-template").val();
	var blockparent =  $("#hide-blockparent").val();
	if (!parent) return false;
	window.location.href = '/manage/blockedit/_aitemedit_id'+blockid+'_template'+btemplate+'_parent'+parent+'_blockparent'+blockparent+'/';
}

function showHideBlock() {
	var parent = $("#hide-parent").val();
	var blockid =  $("#hide-blockid").val();
	var btemplate =  $("#hide-template").val();
	window.location.href = '/manage/blockedit/_ashowhide_id'+blockid+'_template'+btemplate+'_parent'+parent+'/';
}

function delBlock(a) {
	var parent = $("#hide-parent").val();
	var blockid =  $("#hide-blockid").val();
	var btemplate =  $("#hide-template").val();

	if (confirm('Вы действительно хотите удалить блок?')) {
		window.location.href = '/manage/blockedit/_adel_id'+blockid+'_template'+btemplate+'_parent'+parent+'/';
	}
	else {
		$(".trtree").removeClass("click");
	}
}

function delItemBlock(a) {
	var parent = $("#hide-parent").val(),
		blockid =  $("#hide-blockid").val(),
		btemplate =  $("#hide-template").val(),
		blockparent =  $("#hide-blockparent").val();

	if (confirm('Вы действительно хотите удалить блок?')) {
		window.location.href = '/manage/blockedit/_aitemdel_id'+blockid+'_template'+btemplate+'_parent'+parent+'_blockparent'+blockparent+'/';
	}
	else {
		$(".trtree").removeClass("click");
	}
}

var groupDel = function () {
	var str = "",
		parent = $("#hide-parent").val(),
		btemplate =  $("#hide-template").val(),
		prop;

	$(".group-checkbox").each(function() {
		prop = $(this).prop('checked');
		if (prop === true) {
			str += $(this).attr("data-id")+";";
		}
	});

	if (str !== "" && confirm('Вы действительно хотите удалить выбранные блоки?')) {
		window.location.href = '/manage/blockedit/_agroupdel_parent'+parent+'_template'+btemplate+'_ids'+str+'/';
	}
};

var groupHide = function() {
	var str = "",
		parent = $("#hide-parent").val(),
		btemplate =  $("#hide-template").val(),
		prop;

	$(".group-checkbox").each(function() {
		prop = $(this).prop('checked');
		if (prop === true) {
			str += $(this).attr("data-id")+";";
		}
	});

	if (str !== "" && confirm('Вы действительно хотите скрыть выбранные блоки?')) {
		window.location.href = '/manage/blockedit/_agrouphide_parent'+parent+'_template'+btemplate+'_ids'+str+'/';
	}
};

var groupShow = function() {
	var str = "",
		parent = $("#hide-parent").val(),
		btemplate =  $("#hide-template").val(),
		prop;

	$(".group-checkbox").each(function() {
		prop = $(this).prop('checked');
		if (prop === true) {
			str += $(this).attr("data-id")+";";
		}
	});

	if (str !== "" && confirm('Вы действительно хотите показать выбранные блоки?')) {
		window.location.href = '/manage/blockedit/_agroupshow_parent'+parent+'_template'+btemplate+'_ids'+str+'/';
	}
};

var moveTo = function() {
	var str = "",
		t = $(this),
		value = t.val(),
		newParent = t.find("option:selected").attr("data-id"),
		parent = $("#hide-parent").val(),
		btemplate =  $("#hide-template").val(),
		attr;

	if (value == 0) {
		return false;
	}


	$(".group-checkbox").each(function() {
		attr = $(this).attr('checked');
		if (typeof attr !== 'undefined' && attr !== false) {
			str += $(this).attr("data-id")+";";
		}
	});

	if (str !== "" && confirm('Вы действительно хотите переместить выбранные блоки?')) {
		window.location.href = '/manage/blockedit/_amoveto_parent'+parent+'_new'+newParent+'_template'+btemplate+'_ids'+str+'/';
	}
};

function moveBlock() {
	var blockid = $("#hide-blockid").val();
	$("#block_"+blockid+"jscontext").remove();
	$("#block_"+blockid).addClass("inmove");
	beginMoveBlock(blockid, 'move');
}

function copyBlock() {
	var parent = $("#hide-parent").val();
	var blockid =  $("#hide-blockid").val();
	var btemplate =  $("#hide-template").val();

	if (confirm('Вы действительно хотите создать копию блока?')) {
		window.location.href = '/manage/blockedit/_acopy_id'+blockid+'_template'+btemplate+'_parent'+parent+'/';
	}
	else {
		$(".trtree").removeClass("click");
	}
}

function beginMoveBlock(blockid, mode) {
	var up = $('<img style="position: absolute;" class="moveIco" src="/admin/decor/standart/img/icons/ico-up.png"/>');
	var down = $('<img style="position: absolute;" class="moveIco" src="/admin/decor/standart/img/icons/ico-down.png"/>');

	var parent = $("#hide-parent").val();
	var btemplate =  $("#hide-template").val();

	$("body").append(up);
	$("body").append(down);
	$(".moveIco").hide();


		$(".trtree").bind("mousemove", function(e) {
			$(document).bind("keyup", function(e) {
				if (e.keyCode == 27) {
					$(".trtree").unbind("mousemove");
					$("#block_"+blockid).removeClass("inmove");
					up.remove();
					down.remove();
				}
			});
			var curId = $(e.currentTarget).attr("id").substr(6);
			if (blockid != curId) {
				var height = $(e.currentTarget).height();


				var offset = $("#tree").offset();
				var currentMouse = parseInt(e.pageY)  - parseInt(offset.top);
				var currentOffset = e.currentTarget.offsetTop;
				var img, top, newParent, after, before;


				if (currentMouse - currentOffset <= 10) {
					img = up;
					top = currentOffset + offset.top;

					after = 0;
					before = curId;
				}
				if (currentMouse - currentOffset >= 11) {
					img = down;
					top = currentOffset + 8 + offset.top;

					after = curId;
					before = 0;
				}

				$(e.currentTarget).bind("click", function() {
					window.location.href = '/manage/blockedit/_a'+mode+'_parent'+parent+'_after'+after+'_before'+before+'_template'+btemplate+'_id'+blockid+'/';
				});

				$(".moveIco").hide();
				img.show();
				img.css("top", top+"px");
				img.css("left", e.pageX - 25);
			}
		});


}

//Сохранение миниатюры
function saveMini() {
	var x1 = $("#x1").val(),
		y1 = $("#y1").val(),
		x2 = $("#x2").val(),
		y2 = $("#y2").val(),
		w = $("#w").val(),
		h = $("#h").val(),
		realW = $("#realW").val(),
		realH = $("#realH").val(),
		fileName = $("#fileName").val(),
		fieldName = $("#fieldName").val(),
		subSizes = sizes[fieldName];


	$.post(
		"/admin/uploadimage.php",
		{
			mode : "crop",
			x1 : x1,
			x2 : x2,
			y1 : y1,
			y2 : y2,
			w : w,
			h : h,
			realW : realW,
			realH : realH,
			fileName : fileName,
			resize: sizesCount + 1

		},
		function(data) {
			//Увеличиваем счетчик ресайза
			sizesCount ++;

			//Закрываем кроппер
			$.fancybox.close();

			//Если в ресайзах еще есть элементы - ресайзим по ним дальше
			if (typeof(subSizes[sizesCount]) != "undefined") {
				successImageLoad(null, fileName, fieldName);
			}
			//Если нет - то сбрасываем счетчик ресайзов
			else {
				sizesCount = 0;
			}
		}
	);
}

function successImageLoad(file, response, fieldName) {
	if (response != 'no') {

		var api,
			subSizes = sizes[fieldName];

		//Кадрирование
		$("#image-cont").html("<img src='/files/temp/"+response+"' alt='' id='crop-target'>");

		$.fancybox({href : '#form-image', fitToView: false, modal: true, autoResize: false, autoSize: false, width: 1200, height: 1000, autoWidth: false, autoHeight: false, margin: 0});

		$("#realW").val(subSizes[sizesCount][0]);
		$("#realH").val(subSizes[sizesCount][1]);
		$("#fileName").val(response);
		$("#fieldName").val(fieldName);

		function writeCoords(c) {
			$('#x1').val(c.x);
			$('#y1').val(c.y);
			$('#x2').val(c.x2);
			$('#y2').val(c.y2);
			$('#w').val(c.w);
			$('#h').val(c.h);
		};



		  $('#crop-target').Jcrop({
			bgOpacity: 0.5,
			bgColor: 'white',
			setSelect : [0,0,subSizes[sizesCount][0],subSizes[sizesCount][1]],
			addClass: 'jcrop-dark',
			onChange:   writeCoords,
			onSelect:   writeCoords,
			aspectRatio: subSizes[sizesCount]["aspectRatio"]
		  },function(){
			api = this;
			api.setOptions({ bgFade: true });
			api.ui.selection.addClass('jcrop-selection');
		  });



	}
	else {
		alert('Неподходящий формат файла');
	}
}

function deleteImage() {
	if (!confirm("Удалить файл?")) {
		return false;
	}
	var t = $(this),
		fileName = t.attr("data-delete-name"),
		sName = t.attr("data-delete-sname"),
		fieldName = t.attr("data-field-name"),
		val = $('#'+fieldName+'imageload').val();

	$.post(
		"/admin/uploadimage.php",
		{
			mode : "delete",
			fileName : fileName

		},
		function(data) {
			$("#"+sName).hide();
			val = val.replace(fileName, '');
			val = val.replace(';;', ';');
			$('#'+fieldName+'imageload').val(val);
		}
	);

	return false;
}