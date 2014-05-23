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
 * Тип данных для вывода списка с единственным выбором (select)
 * В доп. информации указывается следующая информация:
 * type:table_field:visible:parent:table или перечисление необходимых значений через точку с запятой
 *
 * type - b (блок) или c (каталог)
 * table_field - поле таблицы
 * visible - vis (чтобы выбрать только видимые) hide (только невидимые) all (все)
 * parent - id родителя, может быть указано all
 * table - название шаблона блока или каталога
 *
 * Например, для выборки имен только видимых зарегистрированных юзеров, которые хранятся в каталоге с id 363
 * b:name:vis:363:user
 *
 * Или для выборки всех подразделов каталога (допустим id у каталога 340)
 * c:name:all:340:catgroup
 */
class type_select {

	public function input($name, $data, $comment = '', $ro) {
		if ( strpos($comment, "hidden:") !== false) {
			$style=" style='display: none;'";
		}
		else {
			$style="";
		}
		if ($ro) {
			$attr = " readonly disabled";
		}
		else {
			$attr = "";
		}

		$s = "<select name=\"".htmlspecialchars($name)."\" $style $attr class='select-styled' data-placeholder='Выберите значение'>";

		if ( strpos($comment, "c:") !== false || strpos($comment, "b:") !== false ) {
			$d = explode(":", $comment);

			$type = $d[0];
			$field = $d[1];
			$visible = $d[2] == 'vis' ? ' and `visible`=1' : ($d[2] == 'hide' ? ' and `visible`=0' : '');
			$parent = $d[3];
			$table = $d[4];

			$parent = ($parent == 'all' ? '`parent`>1' : "`parent`='$parent'");

			if ($type == 'b') {
				$query = "SELECT id, $field FROM prname_".$type."_".$table." WHERE $parent $visible ORDER BY sort";
			}
			if ($type == 'c') {
				$query = "SELECT id, name FROM prname_categories WHERE $parent $visible AND template='$table' ORDER BY sort";
			}

			$result = sql::query($query);


			if (sql::num_rows($result) > 0) {
				while ($arr = sql::fetch_assoc($result)) {
					$s .= "<option value=\"".$arr['id']."\" ".(($data == $arr['id']) ? ' selected ' : '').">".htmlspecialchars($arr[$field])."</option>";
				}
			}

		}

		else {
			$val = splstr($comment, ";");


			$d = splstr($data, ";");
			for ($i = 0; $i < count($val); $i++) {
				$s .= "<option value=\"".htmlspecialchars($val[$i + 1])."\" ".(in_array(htmlspecialchars($val[$i + 1]), $d) ? ' selected ' : '')."$attr>".htmlspecialchars($val[$i + 1])."</option>";
			}
		}
		$s .= "</select>";
		return $s;
	}

	public function save($name) {
		global ${"$name"};
		return ${"$name"};
	}

	public function get($data, $comment, $ro) {
		if ($data == "") {
			return "";
		}
		if ( strpos($comment, "c:") !== false || strpos($comment, "b:") !== false ) {
			$d = explode(":", $comment);

			$type = $d[0];
			$field = $d[1];
			$visible = $d[2] == 'vis' ? ' and `visible`=1' : ($d[2] == 'hide' ? ' and `visible`=0' : '');
			$parent = $d[3];
			$table = $d[4];

			$parent = ($parent == 'all' ? '`parent`>1' : "`parent`='$parent'");

			if ($type == 'b') {
				$query = "SELECT id, $field FROM prname_".$type."_".$table." WHERE id=$data";

			}
			if ($type == 'c') {
				$query = "SELECT id, name FROM prname_categories WHERE id=$data AND template='$table'";
			}

			$result = sql::fetch_assoc(sql::query($query));
			$s = $result[$field];
		}

		else {
			$s = $data;
		}

		return $s;
	}

}

?>