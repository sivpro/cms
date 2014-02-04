<?php
/**
 * This file is part of Elgrow CMS
 * Copyright 2012 Innokenty Sarayev <6319432@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$lang = array();

$lang['ru']='switch ($errorType) {
			case "maxlength": $string = "У поля `" . $fieldName . "` превышена максимальная длина - " . $value; break;
			case "minlength": $string = "Поле `" . $fieldName . "` - недостаточно символов"; break;
			case "email": $string = "Поле `" . $fieldName . "` заполнено неверно"; break;
			case "noempty": $string = "Поле `" . $fieldName . "` обязательно для заполнения"; break;
			case "nonumber": $string = "Поле `" . $fieldName . "` не должно содержать цифры"; break;
			case "numberonly": $string = "Поле `" . $fieldName . "` должно содержать только цифры"; break;
			case "captcha": $string = "В поле `" . $fieldName . "` введен неверный код"; break;
			case "login": $string = "Адрес E-mail в поле `" . $fieldName . "` уже зарегистрирован"; break;
			case "sameas": $string = "Пароли не совпадают"; break;
			default: $string = "Ошибка";
		};';
$lang['eng']='switch ($errorType) {
			case "maxlength": $string = "У поля `" . $fieldName . "` exceeded the maximum length - " . $value; break;
			case "minlength": $string = "Field `" . $fieldName . "` -  not enough characters"; break;
			case "email": $string = "Field `" . $fieldName . "` is filled with incorrect"; break;
			case "noempty": $string = "Field `" . $fieldName . "` is required "; break;
			case "nonumber": $string = "Field `" . $fieldName . "` must not contain the figures"; break;
			case "numberonly": $string = "Field `" . $fieldName . "` must contain only digits"; break;
			case "captcha": $string = "В Field `" . $fieldName . "` entered the wrong code"; break;
			default: $string = "error";
		};';
$lang['de']='switch ($errorType) {
			case "maxlength": $string = "У поля `" . $fieldName . "` ?berschreitung der maximalen L?nge - " . $value; break;
			case "minlength": $string = "Field `" . $fieldName . "` -  nicht gen?gend Zeichen"; break;
			case "email": $string = "Field `" . $fieldName . "` gef?llt mit falschen"; break;
			case "noempty": $string = "Field `" . $fieldName . "` ist erforderlich "; break;
			case "nonumber": $string = "Field `" . $fieldName . "` darf nicht die Zahlen"; break;
			case "numberonly": $string = "Field `" . $fieldName . "` darf nur Ziffern"; break;
			case "captcha": $string = "В Field `" . $fieldName . "` den falschen Code eingegeben"; break;
			default: $string = "fehler";
		};';

if(isset($_GET['js']) && $_GET['js']=='get')
{
?>
var config = new Array(), 
	ready = true;


jQuery(document).ready(function(){
	jQuery("form").live("submit", function() {
		fsubmit(jQuery(this).attr('name'));
		return false;
	});

});
function fsubmit(key) {
	jQuery("form[name='"+key+"'] *").removeClass("error");
	jQuery(".error-desc").text("");
	jQuery(".alert").hide();
	jQuery(".good-alert").show();

	eval('validate_showErrorMethod = validate_'+key+'_showErrorMethod;');
	if (validate_showErrorMethod.indexOf("#")!= -1) {
		jQuery(validate_showErrorMethod+" > *").remove("*");
	}

	var res = new Array(0);
	res = checkForm(key);
	for (element in res) {
		if (element.length > 0) {
			var tttt = 11;
			displayErrors(res, key); break;
		}
	}

	if (tttt != 11) {

		eval('validate_sendMethod = validate_'+key+'_sendMethod;');
		if (validate_sendMethod == 'ajax') {
			post(key);
		}
		else {
			jQuery("form[name='"+key+"']").submit();
		}
	}

}
function post(fName) {
	if (ready) {

		var b = jQuery("form[name='"+fName+"'] input, form[name='"+fName+"'] textarea, form[name='"+fName+"'] select");

		var newstr = "";

		b.each(function() {
			if (jQuery(this).attr("type") == "radio") {
				if (jQuery(this).attr("checked") == "checked") {
					radioname = jQuery(this).attr("name");
					radioval = jQuery(this).val();
					newstr += radioname + '=' + radioval + '&';

				}
			}
			else if (jQuery(this).attr("type") == "checkbox") {
				if (jQuery(this).attr("checked") == "checked") {
					radioname = jQuery(this).attr("name");
					radioval = jQuery(this).val();
					newstr += radioname + '=' + radioval + '&';

				}
			}
			else {

				if (jQuery(this).val() == "") {
					jQuery(this).val("");
				}

				newstr += this.name + '=' + jQuery(this).val() + '&';
			}

		});
		newstr += 'ajax=true';
		newstr += '&formName='+fName;


		jQuery.ajax({
		   type: "POST",
		   url: jQuery("form[id='"+fName+"']").attr('action'),
		   data: newstr,
		   success: function(msg){
			 if (msg.match('@s@')) {
				eval('validate_preloaderId = validate_'+fName+'_preloaderId;');
				jQuery(validate_preloaderId).hide();

				var text1 = msg.replace(/@s@/g,'');
				eval('validate_showErrorMethod = validate_'+fName+'_showErrorMethod;');
				eval('validate_lastaction = validate_'+fName+'_lastaction;');

				eval('var validate_callback = validate_'+fName+'_callback;');				
				eval('var validate_param = validate_'+fName+'_param;');
				
				
				if (validate_lastaction == 'callback') {
					if (typeof(window[validate_callback]) == "function") {						
						window[validate_callback](validate_param);
					}
				}
				if (validate_showErrorMethod.match("#")) {
					jQuery(validate_showErrorMethod).html(text1);
				}
				if (validate_showErrorMethod == "alert") {
					if(validate_lastaction =='hide' || validate_lastaction == 'hideSend') {
						alert(text1);
					}
				}
				if (validate_lastaction == 'hide') {
					jQuery("form[id='"+fName+"']").css('display','none');
				}
				if (validate_lastaction == 'hideSend') {
					eval ('validate_send = validate_'+fName+'_sendId;');
					jQuery(validate_send).hide();
				}
				if (validate_lastaction.indexOf("sp") > - 1) {
					window.location.href = '/'+validate_lastaction+'/';
				}
				 ready = false;
				 setTimeout('readyFalse()', 1000);
			 }
			 else {				
				eval('validate_capId = validate_'+fName+'_capId;');
				var src = jQuery("captcha", msg).text();
				if (src.length == 0) {
					src = msg.substr(14, 32);
				}

				src = "/libs/imgcode/"+src+".jpg";

				jQuery(validate_capId).attr("src", src);

				text1 = jQuery("errors", msg).text();

				if (text1.length == 0) {
					text1 = msg.substr(msg.indexOf("<errors>")+8, msg.indexOf("</errors>")-msg.indexOf("<errors>")-8);
				}

				eval('validate_preloaderId = validate_'+fName+'_preloaderId;');
				jQuery(validate_preloaderId).hide();

				eval('validate_showErrorMethod = validate_'+fName+'_showErrorMethod;');
				if (validate_showErrorMethod.indexOf("#")!= -1) {
					text1 = "<p>"+text1+"</p>";
					jQuery(text1).appendTo(validate_showErrorMethod);
				}
				else {
					alert(text1);
				}
			}
		  }
		 });
		 eval('validate_preloaderId = validate_'+fName+'_preloaderId;');
		 jQuery(validate_preloaderId).show();
	}

	else {
		eval('validate_showErrorMethod = validate_'+fName+'_showErrorMethod;');
		if (validate_showErrorMethod.indexOf("#")!= -1) {
			jQuery("<p>Слишком частое отправление запроса, подождите, пожалуйста</p>").appendTo(validate_showErrorMethod);
		}
		else {
			alert("Слишком частое отправление запроса, подождите, пожалуйста");
		}
	}

}

function readyFalse() {
	ready = true;
}



function displayErrors(array, formName) {
	eval('validate_highlight = validate_'+formName+'_highlight;');	
	eval('validate_showErrorMethod = validate_'+formName+'_showErrorMethod;');
	if (validate_highlight) {
		highlight(array, formName);
	}

	if (validate_showErrorMethod == "alert") {
		alertError(array);
	}
	if (validate_showErrorMethod.indexOf("#")!= -1) {
		showErrorInDiv(array, validate_showErrorMethod);
	}
}

function highlight(array, formName) {	
	for (field in array) {
		for (error in array[field]) {
			var errorCaption = jQuery("#"+field+"-error");
			if (errorCaption.text() == "") {
				var newError = getError(error, array[field]['caption'], error);
				errorCaption.text(newError);
			}
			//jQuery("form[name='"+formName+"'] *[name='"+field+"']").css('background','#e97451');
			jQuery("form[name='"+formName+"'] *[name='"+field+"']").addClass("error");
			jQuery("#validate-bad_"+field).show();
			jQuery("#validate-good_"+field).hide();
		}
	}
}
function alertError(array) {
	var string = "";
	out:
	for (field in array) {
		for (error in array[field]) {
			var newError = getError(error, array[field]['caption'], array[field][error]);
			string += newError+"\n";
			continue out;
		}
	}
	alert(string);
}

function showErrorInDiv(array, id) {
	var string = "";
	out:
	for (field in array) {
		for (error in array[field]) {
			var newError = getError(error, array[field]['caption'], array[field][error]);
			string += "<p>"+newError+"</p>";
			continue out;
		}
	}
	$(id).append(string);
}


function getError($errorType, $fieldName, $value) {
	var $string;
	switch ($errorType) {
			case "maxlength": $string = "У поля `" + $fieldName + "` превышена максимальная длина - " + $value; break;
			case "minlength": $string = "Поле `" + $fieldName + "` - недостаточно символов"; break;
			case "email": $string = "Поле `" + $fieldName + "` заполнено неверно"; break;
			case "noempty": $string = "Поле `" + $fieldName + "` обязательно для заполнения"; break;
			case "nonumber": $string = "Поле `" + $fieldName + "` не должно содержать цифры"; break;
			case "numberonly": $string = "Поле `" + $fieldName + "` должно содержать только цифры"; break;
			case "captcha": $string = "В поле `" + $fieldName + "` введен неверный код"; break;
			case "login": $string = "Адрес E-mail в поле `" + $fieldName + "` уже зарегистрирован"; break;
			case "sameas": $string = "Пароли не совпадают"; break;
			default: $string = "Ошибка"
	};	
	return $string;
}


//Проверка формы
//formName - имя формы
function checkForm(formName) {
	var res;
	var error = new Array(0);
	var newArr = config[formName];

	for(elem in newArr) {
		
		res = checkField(elem, newArr[elem], formName);
		for (what in res) {
			if (what.length > 0) {
				error[elem] = new Array();
				error[elem] = res;
				error[elem]['caption'] = newArr[elem]['caption'];
				break;
			}
			else {
				delete error[elem];
				break;
			}
		}
	}

	return error;
}

//Проверяет Field
//field - имя поля
//value - массив с ограничителями и их значения
//formName - имя формы
function checkField(field, value, formName) {	
	var error2 = new Array(0);
	var res;
	var newArr = value;
	for (var handler in newArr) {
		if (handler != 'caption') {
			functionName = "check_"+handler;
			res = eval(functionName+"('"+field+"','"+ newArr[handler]+"','"+ formName+"')");

			if (!res) {
				error2[handler] = newArr[handler];
			}
			else {
				delete error2[handler];
			}
		}
	}

	return error2;
}

/*Обработчики*/



	function check_maxlength(field, value, formName) {
		try{
				var string = jQuery("#"+field+"").val();
			}catch(e){
				var string = jQuery("#"+field+"").html();
			}
		if (string.length > parseInt(value)) {
			return false;
		}
		return true;
	}

	function check_captcha(field, value, formName) {
		return true;
	}


	/*Min-lenght validation*/
	function check_minlength(field, value, formName) {
		try{
				var string = jQuery("#"+field+"").val();
			}catch(e){
				var string = jQuery("#"+field+"").html();
			}
		if (string.length < parseInt(value)) {
			return false;
		}
		return true;
	}

	/*Only numbers validation*/
	function check_numberonly(field, value, formName) {
		try{
			var string = jQuery("#"+field+"").val();
		}catch(e){
			var string = jQuery("#"+field+"").html();
		}

		var reg = new RegExp("[^0-9]+", 'i');
		var result = reg.test(string);
		if (result)  {
			return false;
		}
		return true;
	}


	/*No numbers validation*/
	function check_nonumber(field, value, formName) {
		try{
			var string = jQuery("#"+field+"").val();
		}catch(e){
			var string = jQuery("#"+field+"").html();
		}

		var reg = new RegExp("[0-9]", 'i');
		var result = reg.test(string);

		if (result)  {
			return false;
		}
		return true;
	}

	/*Only numbers and symbols validation*/
	function check_numberandsimbols(field, value, formName) {
		try{
			var string = jQuery("#"+field+"").val();
		}catch(e){
			var string = jQuery("#"+field+"").html();
		}

		var reg = new RegExp("[^0-9-_+@\s]", 'i');
		var result = reg.test(string);
		if (!result)  {
			return false;
		}
		return true;
	}

	/*E-mail validation*/
	function check_email(field, value, formName) {
		try{
			var string = jQuery("#"+field+"").val();
		}catch(e){
			var string = jQuery("#"+field+"").html();
		}

		var reg = new RegExp("[0-9a-z_]+@[0-9a-z_^.]+\\.[a-z]{2,3}", 'i');
		var result = reg.test(string);
		if (!result)  {
			return false;
		}
		return true;
	}
	/*No empty validation*/
	function check_noempty(field, value, formName) {		
		try{
			var string = jQuery("#"+field+"").val();
		}catch(e){
			var string = jQuery("#"+field+"").html();
		}		

		if (string.length < 1) {
			return false;
		}
		if (string === value && value > 1) {
			return false;
		}

		
		return true;
	}
	/*checkbox validation*/
	function check_checkbox(field, value, formName)
	{
		var string = jQuery("#"+field+"").attr('checked');
		if (!string)
		{
			return false;
		}
		return true;
	}
	/*sameAs (password reply) validation*/
	function check_sameas(field, value, formName) {
		try{
			var string = jQuery("#"+field+"").val();
		}catch(e){
			var string = jQuery("#"+field+"").html();
		}
		try {
			var string2 = jQuery("#"+value+"").val();
		}catch(e) {
			var string2 = jQuery("#"+value+"").html();
		}
		if (string != string2) {
			return false;
		}
		return true;
	}
	<?php
die;
}
/*Класс для проверки форм - работает как с включенным js, так и без него
**Чтобы воспользоваться нужно:
** 1). Создать конфиг для Fieldй такого вот вида:
		$this->config = array(
			$formName => array(
				'fio' => array(
					'caption' => 'Имя',
					'noempty' => true,
					'nonumber'  => true,
					'maxlength' => 5
				),
				'phone' => array(
					'caption' => 'Контактный телефон',
					'noempty' => true,
					'numberonly' => true,
					'maxlength' => 100
				),
				'eemail' => array(
					'caption' => 'Ваш E-mail',
					'noempty' => true,
					'email' => true,
					'maxlength' => 100
				),
				'text' => array(
					'caption' => 'Текст сообщения',
					'noempty' => true,
					'maxlength' => 200
				)
			)
		);
** caption - обязательное поле, остальные - обработчики (все перечислены внизу класса)

** 2). Создать новый экземпляр класса и передать в конструктор конфиг $this->validator = new formvalidator($this->config);
** 3). Установить необходимые значения переменных, например $this->validator->showErrorMethod = "#showErrors";  //div для показа ошибок
*/

class captcha {
	public static $imagePath = "/libs/imgcode/";
	public static $fontPath = "/libs/fonts/";
	public function __construct() {

	}
	public static function getCaptcha($salt) {
		self::$imagePath = DOC_ROOT."/libs/imgcode/";
		self::$fontPath = DOC_ROOT."/libs/fonts/";

		$x_img = 120; $y_img = 29; //ширина и высота
		$numsymb_img=5; //кол-во символов
		$bgcolor_img = 0x1f4452; //задний фон		
		$font_size_from = 14;
		$font_size_dis = 14;
		$fonts = array('a3');
		$im = @ImageCreateTrueColor ($x_img, $y_img)or die ("Ошибка инициализации библиотеки GD");
		ImageFilledRectangle ($im, 0, 0, $x_img - 1, $y_img - 1, $bgcolor_img);
		$tx = 7 + rand(0, 5);
		$s = '';
		for ($i = 0; $i < $numsymb_img; $i++) {			
			mt_srand(time());
			$n = self::getRandomSign();			
			$nx = $tx;			
			$ny = 15; // координаты цифры
			//$ny = 25 + rand(0, 35); // координаты цифры
			$f = rand(5, 10); // размер цифры
			$font = $fonts[rand(0, count($fonts) - 1)] . '.ttf';
			$size = rand($font_size_from,$font_size_dis);
			 $angle = -30 + mt_rand(0, 60);
			//$angle = 10;
			//$color = rand(0, 180) * 65536 + rand(0, 180) * 256 + rand(0, 180);
			$color = 0xFFFFFF;
			$dx = self::imagettftext_cr($im, $size, $angle, $nx, $ny, $color, self::$fontPath.$font, $n );
			$s .= $n;
			$tx += 8 + $dx;
		}
		// Формирование пикселей
	/*	$n = rand(40,90);
		for ($i = 0; $i < $n; $i++) {
			$color = rand(100, 255) * 65536 + rand(100, 255) * 256 + rand(100, 255);
			$nx = rand(1, $x_img - 2);
			$ny = rand(1, $y_img - 2);
			imagesetpixel($im, $nx, $ny, $color);
			imagesetpixel($im, $nx - 1, $ny, $color);
			imagesetpixel($im, $nx + 1, $ny, $color);
			imagesetpixel($im, $nx, $ny - 1, $color);
			imagesetpixel($im, $nx, $ny + 1, $color);
		}*/

		$tmp_cifr_post = md5($salt.$s);
		ImageJpeg ($im, self::$imagePath.$tmp_cifr_post.".jpg", 99);
		@imagedestroy($im);
		foreach (glob(self::$imagePath."*.jpg") as $filename) {
			if(date("YmdHis", filemtime($filename))<date("YmdHis", mktime(date("H"), date("i")-5, date("s"), date("m"), date("d"), date("Y"))))
			unlink($filename);
		}		
		return $tmp_cifr_post;
	}    
	private function imagettftext_cr (&$im, $size, $angle, $x_img, $y_img, $color, $fontfile, $text) {
		$bbox = imagettfbbox($size, $angle, $fontfile, $text);
		$dx = ($bbox[2]-$bbox[0])/2.0 - ($bbox[2]-$bbox[4])/2.0; // deviation left-right
		$dy = ($bbox[3]-$bbox[1])/2.0 + ($bbox[7]-$bbox[1])/2.0; // deviation top-bottom
		$px = $x_img-$dx;
		$py = $y_img-$dy;
		imagettftext($im, $size, $angle, $px, $py, $color, $fontfile, $text);
		return $bbox[2] - $bbox[0];
	}
	private function getRandomSign() {
		mt_srand();
		$m = mt_rand(48,57);		
		return chr($m);
	}
}

class formvalidator {
	private $config;					//Конфиг Fieldй, загружается в конструкторе
	public $error = array();			//Массив ошибок
	public $post;						//Пост данные

	private $captcha;
	public $formName;					//Имя формы

	private $salt = "has";

	/*Метод возврата ошибок.
	**Возможные значения - alert(только при включенном js),
	** #idэлемента - ошибки будут показываться в этом элементе*/
	public $showErrorMethod = "alert";
	public $highlight = true;			//Подсветка ошибочных Fieldй
	public $sendMethod = "ajax";
	public $capId = "#captcha";			//Айдишник капчи
	public $preloaderId = "#preloader";		//Айдишник прелоадера



	public function __construct($config) {

		$this->config = $config;

		foreach ($this->config as $key => $value) {
			$this->formName = $key; break;
		}
	}

	public function checkFields($caller, $object) {
		$this->post = $_POST;
		foreach ($this->post as $key => $value) {
			$this->post[$key] = htmlspecialchars(trim($value));
		}

		foreach ($this->config[$this->formName] as $fieldName => $field) {
			$this->checkField($fieldName, $field, $field['caption']);
		}

		$far = $this->salt.$this->post['cap'];


		if (count($this->error) < 1)
		{
			//$caller->success();

			$this->success = true;
			if ($this->sendMethod == 'ajax') {
//				die(" ok ");
			}
			if ($this->sendMethod == "ajax") {
				print '@s@';
			}
			$object->success = true;
			return $object;
		}

		elseif (count($this->error) > 0) {
			if ($this->sendMethod == "ajax") {
				$captcha = $this->getCaptcha();
				header('Content-Type: text/html; charset=utf-8');
				$text = "<al><captcha>".$captcha."</captcha><errors>".$this->getErrors()."</errors></al>";
				die($text);
			}
			$post = $this->post;
			$post2 = array();
			$i = 0;
			foreach ($post as $key => $value) {
				$post2[$i] = new stdClass();
				$post2[$i]->name = $key;
				$post2[$i]->value = $value;
				$i++;
			}
			$object->post = $post2;
			$object->errors = $this->getErrors();
			$this->errors = $object->errors;

			return $object;
		}

	}

	public function getCaptcha() {
		$this->captcha = captcha::getCaptcha($this->salt);
		$_SESSION[$this->capId] = $this->captcha;
		return $this->captcha;
	}

	//Формирует JavaScript код, все переменные нужно устанавливать до вызова этой функции
	public function getJsArray() {
		$script = '<script>';
		$script .= 'var validate_'.$this->formName.'_showErrorMethod = "'.$this->showErrorMethod.'",';
		$script .= '	validate_'.$this->formName.'_sendId = "'.$this->sendId.'",';
		$script .= '	validate_'.$this->formName.'_lastaction = "'.$this->lastaction.'",';
		$script .= '	validate_'.$this->formName.'_highlight = '.$this->highlight.',';
		$script .= '	validate_'.$this->formName.'_sendMethod = "'.$this->sendMethod.'",';
		$script .= '	validate_'.$this->formName.'_capId = "'.$this->capId.'",';
		$script .= '	validate_'.$this->formName.'_preloaderId = "'.$this->preloaderId.'",';
		$script .= '	validate_'.$this->formName.'_callback = "'.$this->callback.'";';
		$script .= '	validate_'.$this->formName.'_param = "'.$this->param.'";';
		foreach ($this->config as $formName => $form) {
			$script .= 'config["'.$formName.'"] = new Array('.count($form).');';

			foreach ($form as $fieldName=>$field) {
				$script .= '
					config["'.$formName.'"]["'.$fieldName.'"] = new Array('.count($field).');
				';
				foreach ($field as $name => $value) {
					if($name=='login')continue;
					$script .= '
						config["'.$formName.'"]["'.$fieldName.'"]["'.$name.'"] = "'.$value.'";
					';
				}
			}
		}

		$script .= '
			</script>
		';
		return $script;
	}

	public function getErrors() {
		$errorStr = "";
		foreach ($this->error as $fieldName => $error) {

			$value = isset($error['value_'.$error[0]]) ? $error['value_'.$error[0]] : 0;
			$errorStr .= "".$this->getErrorText($error['caption'], $error[0], $value)."";
		}
		return $errorStr;
	}

	private function getErrorText($fieldName, $errorType, $value) {

		switch ($errorType) {
			case "maxlength": $string = "У поля `" . $fieldName . "` превышена максимальная длина - " . $value; break;
			case "minlength": $string = "Поле `" . $fieldName . "` - недостаточно символов"; break;
			case "email": $string = "Поле `" . $fieldName . "` заполнено неверно"; break;
			case "noempty": $string = "Поле `" . $fieldName . "` обязательно для заполнения"; break;
			case "nonumber": $string = "Поле `" . $fieldName . "` не должно содержать цифры"; break;
			case "numberonly": $string = "Поле `" . $fieldName . "` должно содержать только цифры"; break;
			case "login": $string = "- Пользователь с таким адресом E-mail уже имеется в нашей базе. Пожалуйста введите другое значение."; break;
			case "captcha": $string = "В поле `" . $fieldName . "` введен неверный код"; break;
			case "sameas": $string = "Пароли не совпадают"; break;
			default: $string = "Ошибка";
		};
		return $string;
	}

	private function getFormName() {
		if (isset($this->post['formname'])) {
			$this->formName = $this->post['formname'];
			return;
		}
		foreach ($this->post as $key => $value) {
			foreach ($this->config as $formName => $b) {
				if ($formName == $key) {
					$this->formName = $formName;
					return;
				}
			}
		}
	}

	private function checkField($fieldName, $field, $caption) {
		foreach ($field as $crit => $value) {
			if ($crit != "caption") {
				$methodName = "check_".$crit;
				$this->$methodName($fieldName, $value, $caption);
			}

			if (is_array($this->error[$fieldName])) {
				$this->error[$fieldName]['caption'] = $caption;
			}
		}
	}









/*Обработчики*/




	/*Max-lenght validation*/
	private function check_maxlength($field, $value) {
		if (strlen($this->post[$field]) > $value) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'maxlength';
			$this->error[$field]['value_maxlength'] = $value;
		}
	}

	/*captcha validation*/
	private function check_captcha($field, $value) {
		if (md5($this->salt.$this->post[$field]) != $_SESSION[$this->capId]) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'captcha';
		}
	}

	/*Min-lenght validation*/
	private function check_minlength($field, $value) {
		if (strlen($this->post[$field]) < $value) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'minlength';
			$this->error[$field]['value_minlength'] = $value;

		}
	}

	/*Only numbers validation*/
	private function check_numberonly($field, $value) {
		$string = $this->post[$field];
		if (preg_match("#[^0-9+]#", $string)) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'numberonly';
		}
	}

	/*No numbers validation*/
	private function check_nonumber($field, $value) {
		$string = $this->post[$field];
		if (preg_match("#[0-9]#", $string)) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'nonumber';
		}
	}

	/*Only numbers and symbols validation*/
	private function check_numberandsimbols($field, $value) {
		$string = $this->post[$field];
		if (preg_match("#[^0-9-_+@\s]#", $string)) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'numberandsimbols';
		}
	}

	/*E-mail validation*/
	private function check_email($field, $value) {
		$string = $this->post[$field];
		if (!preg_match("#^[0-9a-z_]+@[0-9a-z_^.]+\\.[a-z]{2,3}$#i", $string)) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'email';
		}
	}

	/*No empty validation*/
	private function check_noempty($field, $value) {
		if (strlen($value) > 4) {
			if ($this->post[$field] == $value) {
				if (!is_array($this->error[$field])) {
					$this->error[$field] = array();
				}
				$this->error[$field][] = 'noempty';
			}
		}

		if (strlen($this->post[$field]) < 1) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'noempty';
		}
	}
	/*SQL validation*/
	private function check_login($field, $value) {
		$email = mysql_real_escape_string($this->post[$field]);
		$array = explode("_", $value);
		$table = $array[0];
		$field = $array[1];
	 	$r = sql::one_record('SELECT * FROM prname_b_'.$table.' WHERE `'.$field.'`="'.$email.'"');
		if ($r != null) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'login';
		}

	}

	/*sameAs (password reply) validation*/
	private function check_sameas($field, $value) {
		if ($this->post[$value] != $this->post[$field]) {
			if (!is_array($this->error[$field])) {
				$this->error[$field] = array();
			}
			$this->error[$field][] = 'sameas';
		}

	}



}

?>