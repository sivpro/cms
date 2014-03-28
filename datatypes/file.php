<?php
/**
 * This file is part of Elgrow CMS
 * Copyright 2012 Innokenty Sarayev <6319432@gmail.com>
 *
 * Elgrow CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Elgrow CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Тип данных для загрузки файлов.
 * Параметры в комментарии:
 * "noext:" неразрешенные разрешения файлов через запятую
 * "onlyext:" ограничение на загрузку только файлов перечисленных типов через запятую
 * "selfname:" - использовать имя загружаемого файла без генерации имени
 * "resize:" - ресайзы изображений. например <resize:100x500> сделает пропрциональный ресайз
 * чтобы вписаться в прямоугольник 100x500 px. Чтобы сделать несколько ресайзов нужно писать их через
 * запятую. Например <resize:100x500,300x600,400x200xcrop>
 */
class type_file {
	public function input($name, $data, $comment = '') {
		global $config;

		if (strpos($comment, "resize") > -1) {
			$aspectRatio = str_replace("resize:", "", $comment);
			$aspectRatio = explode("x", $aspectRatio);
			$aspectRatio = $aspectRatio[0] ."/". $aspectRatio[1];
		}
		else {
			$aspectRatio = 0;
		}
		$s = "<input name=\"".htmlspecialchars($name)."\" type=\"file\" value=\"\">";
		$s .= "<input name=\"".htmlspecialchars($name)."_wasuploaded\" type=\"hidden\" value=\"".bin2hex($data)."\">";
		$s .= "<input name=\"".htmlspecialchars($name)."_comment\" type=\"hidden\" value=\"".htmlspecialchars($comment)."\">";
		if ($data != '') {
			$n = strpos($data, ' ');
			$fn = substr($data, 0, $n);
			if ($fn == '')   {
				$fn = trim($data);
			}
			$s .= "<br><a href=\"".$config['server_url']."files/0/$fn\" target=\"_blank\">просмотр..</a>";
			$s .= "&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"".htmlspecialchars($name)."_remove\" value=1> удалить";
		}

		/*Если файл - изображение и оно уже загружено, при условии атрибута realcrop - выводим кроппер*/
		$fileInfo = pathinfo(DOC_ROOT."/files/0/".$data);
		$fileInfo['extension'] = strtolower($fileInfo['extension']);

		if (($fileInfo['extension'] == 'jpg' || $fileInfo['extension'] == 'jpeg' || $fileInfo['extension'] == 'png') && strpos($comment, "realcrop") > -1) {
			$s .= "<div class=\"cropper\" style=\"margin-top: 20px;\">
				<img src=\"/files/3/".$data."\" id=\"crop-target\" alt=\"\" style=\"margin-top: 20px; display: none;\"/ >
				<input type=\"hidden\" id=\"x1\" name=\"x1\" />
				<input type=\"hidden\" id=\"y1\" name=\"y1\" />
				<input type=\"hidden\" id=\"x2\" name=\"x2\" />
				<input type=\"hidden\" id=\"y2\" name=\"y2\" />
				<input type=\"hidden\" id=\"w\" name=\"w\" />
				<input type=\"hidden\" id=\"h\" name=\"h\" />
				</div>";

			$s .= "<a href=\"#\" style=\"margin-top: 20px;\" class=\"btn btn-inverse\" id=\"cropDataButton\">Кадрировать</a>";
			$s .= "<a href=\"#\" style=\"margin-top: 20px; display: none;\" class=\"btn btn-warning\" id=\"cropDataCancel\">Отменить</a>";

			$s .= "<script>
				var api;

				function initCrop() {
				  $('#crop-target').Jcrop({
					bgOpacity: 0.5,
					bgColor: 'white',
					addClass: 'jcrop-dark',
					onChange:   writeCoords,
					onSelect:   writeCoords,
					aspectRatio: ".$aspectRatio."
				  },function(){
					api = this;
					api.setOptions({ bgFade: true });
					api.ui.selection.addClass('jcrop-selection');
				  });
				}

				 function writeCoords(c) {
					$('#x1').val(c.x);
					$('#y1').val(c.y);
					$('#x2').val(c.x2);
					$('#y2').val(c.y2);
					$('#w').val(c.w);
					$('#h').val(c.h);
				};


				$('#cropDataButton').live('click', function() {
					$('#crop-target').show();
					initCrop();
					$('#cropDataButton').hide();
					$('#cropDataCancel').show();
					return false;

				});

				$('#cropDataCancel').live('click', function() {
					api.destroy();
					$('#crop-target').hide();

					$('#cropDataButton').show();
					$('#cropDataCancel').hide();
					return false;

				});

			</script>

			";

		}
		return $s;
	}

	public function save($name) {
		global ${"$name"};
		global ${"$name"."_wasuploaded"};
		global ${"$name"."_comment"};
		global ${"$name"."_name"};
		global ${"$name"."_type"};
		global ${"$name"."_size"};
		global ${"$name"."_remove"};
		global $error;

		$uploaded = false;
		${"$name"} = $_FILES[$name]['tmp_name'];
		${"$name"."_type"} = $_FILES[$name]['type'];
		${"$name"."_size"} = $_FILES[$name]['size'];
		$comment = $_POST[$name.'_comment'];
		${"$name"."_wasuploaded"} = $_POST[$name.'_wasuploaded'];
		${"$name"."_remove"} = $_POST[$name.'_remove'];

		$x1 = $_POST['x1'];
		$y1 = $_POST['y1'];

		$x2 = $_POST['x2'];
		$y2 = $_POST['y2'];

		$w = $_POST['w'];
		$h = $_POST['h'];

		if (is_uploaded_file(${"$name"})) {

			$uploaded = true;
			$fn = $_FILES[$name]['name'];
			$n = strrpos($fn, ".");
			$ext = substr($fn, $n);


			if (($n = strpos($comment, 'noext:')) !== false) {
				if (($n2 = strpos($comment, ' ', $n)) == false) {$n2 = strlen($comment);}
				$n += 6;
				$val = substr($comment, $n, $n2 - $n);
				$mimes = explode(",", $val);
				if (in_array(strtolower(substr($ext, 1)), $mimes)) {$uploaded = false; $error = "Недопустимый тип файла ".substr($ext, 1).". Пожалуйста, не используйте файлы типа $val";};
			}

			if (($n = strpos($comment, 'onlymime:')) !== false) {
				if (($n2 = strpos($comment, ' ', $n)) == false) {$n2 = strlen($comment);}
				$n += 9;
				$val = substr($comment, $n, $n2 - $n);
				$mimes = explode(",", $val);
				if (!in_array(${"$name"."_type"}, $mimes)) {$uploaded = false; $error = "Недопустимый тип файла ".${"$name"."_type"}.". Пожалуйста, используйте файлы типа $val";}
			}

			if (($n = strpos($comment, 'onlyext:')) !== false) {
				if (($n2 = strpos($comment, ' ', $n)) == false) {$n2 = strlen($comment);}
				$n += 8;
				$val = substr($comment, $n, $n2 - $n);

				$mimes = explode(",", $val);
				if (!in_array(strtolower(substr($ext, 1)), $mimes)) {$uploaded = false; $error = "Недопустимый тип файла ".substr($ext, 1).". Пожалуйста, используйте файлы типа $val";}

			}



			if (($n = strpos($comment, 'selfname:')) !== false) {

				if ($uploaded) {
					$fn = str_replace(" ", "_", $fn);

					copy(${"$name"}, DOC_ROOT."/files/0/$fn");


					if (($n = strpos($comment, 'resize:')) !== false) {
						$fn1 = explode('resize:',$comment);
						$fs = explode(',',$fn1[1]);

						for ($if=0;$if<count($fs);$if++) {
							resize_image($fn, $fs[$if], $if+1);
						}
					}

					$data = $fn;
				}
			}

			else {
				if ($uploaded) {
					$fn = (rand(0, 255) + 256 * rand(0, 255) + 65536 * rand(0, 255)) . time() . $ext;
					copy(${"$name"}, DOC_ROOT."/files/0/$fn");


					if (($n = strpos($comment, 'resize:')) !== false) {
						$fn1 = explode('resize:',$comment);
						$fs = explode(',',$fn1[1]);

						for ($if=0;$if<count($fs);$if++) {
							resize_image($fn, $fs[$if], $if+1);
						}

					}

					$data = $fn;
				}
			}



		}
		if (!$uploaded) {
			/*Кадрирование*/
			if (isset($_POST['x1'])) {

				$data = pack("H*", stripslashes(${"$name"."_wasuploaded"}));



				$fn = $data;

				if (($n = strpos($comment, 'resize:')) !== false) {
					$fn1 = explode('resize:',$comment);
					$fs = explode(',',$fn1[1]);

					for ($if=0;$if<count($fs);$if++) {


						$mimes = explode("x", $fs[$if]);

						if ($mimes[3] == 'realcrop') {

							$cropData['x1'] = $x1;
							$cropData['y1'] = $y1;
							$cropData['x2'] = $x2;
							$cropData['y2'] = $y2;
							$cropData['w'] = $w;
							$cropData['h'] = $h;
							resize_image($fn, $fs[$if], $if+1, $cropData);
						}
						else {
							resize_image($fn, $fs[$if], $if+1);
						}
					}

				}

			}
			if (${"$name"."_wasuploaded"} != '') {
				if (${"$name"."_remove"} != '') {
					$data = '';
				}
				else {
					$data = pack("H*", stripslashes(${"$name"."_wasuploaded"}));
				}
			}
			else $data = '';
		}

		return $data;
	}

	public function get($data, $comment, $ro) {
		return "";
	}

}
?>