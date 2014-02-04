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
 * Тип данных многострочного текста (textarea).
 * Создавался для счетчиков статистики, отсюда и название
 */
class type_counter {
	public function input($name, $data, $comment = '', $ro) {

		if ( strpos($comment, "hidden:") !== false) {
			$style=" style='display: none;'";
		}
		else {
			$style="";
		}

		if ($ro) {
			$return = "<textarea  class=\"textarea\" name=\"".htmlspecialchars($name)."\" id=\"my$name\" $style readonly>".htmlspecialchars($data)."</textarea>";
		}
		else {
			$return = "<textarea  class=\"textarea\" name=\"".htmlspecialchars($name)."\" id=\"my$name\" $style>".htmlspecialchars($data)."</textarea>";
		}
		return $return;
	}

	public function save($name) {
		global ${"$name"};
		return stripslashes(${"$name"});
	}

}
?>