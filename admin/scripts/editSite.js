function editCont(id) {				
	$("#editbl_"+id).aloha();

	$("#saveLink_"+id).show();
	$("#editLink_"+id).hide();	
}

function saveCont(type, datatype, template, field, id) {
	if (datatype == 'html') {
		var value = $("#editbl_"+id).html();
	}
	if (datatype == 'text') {
		var value = $("#editbl_"+id).text();
	}	
	
				
	$.post(
		"/admin/editcont.php",
		{
			type : type,			//Тип: блок или папка
			datatype : datatype,	//Тип: блок или папка
			template : template,	//Таблица
			field : field,			//Поле в таблице
			id : id,				//Айди записи
			value : value			//Значение поля	
		},
		function(data) {
			
			if (data == "ok") {							
				alert("Изменения сохранены");
				window.location.href = window.location.href;											
			}
			else {
				alert(data);
			}
		}
	);
}

$(document).ready(function() {
	$(".editLink").each(function() {
		$("body").prepend($(this));
	});
	$(".saveLink").each(function() {
		$("body").prepend($(this));
	});
	$(".editLink").each(function() {					
		var id = $(this).attr("id").substr(9);
		var offset = $("#editbl_"+id).offset();
		var width = $("#editbl_"+id).width();					
		$(this).css('top' , offset.top - 22 + 'px');
		$(this).css('left' ,width + offset.left - 129 + 'px');
		$("#saveLink_"+id).css('top' , offset.top - 22 + 'px');
		$("#saveLink_"+id).css('left' , width + offset.left - 129 + 'px');
	});
});

	
	