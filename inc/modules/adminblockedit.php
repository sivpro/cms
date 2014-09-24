<?php
class adminblockedit extends manage {

	function __construct() {
		$_SESSION['newId'] = null;
		global $control;



		parent::checkForUser();

		$this->menu = parent::getMenu();

		$this->page = $control->page;
		if ($control->oper == 'add') {
			if (isset($_POST['parent']))
				return $this->add();
			else return $this->printAdd();
		}
		if ($control->oper == 'itemadd') {
			if (isset($_POST['blockparent']))
				return $this->add('item');
			else return $this->printAdd('item');
		}

		if ($control->oper == 'edit') {
			if (isset($_POST['parent']))
				return $this->edit();
			else return $this->printEdit();
		}

		if ($control->oper == 'itemedit') {
			if (isset($_POST['blockparent']))
				return $this->edit('item');
			else return $this->printEdit('item');
		}


		if ($control->oper == 'showhide') {
			return $this->showHide();
		}

		if ($control->oper == 'grouphide') {
			return $this->groupHide();
		}

		if ($control->oper == 'groupshow') {
			return $this->groupShow();
		}

		if ($control->oper == 'del') {
			return $this->delete();
		}
		if ($control->oper == 'itemdel') {
			return $this->delete('item');
		}

		if ($control->oper == 'move') {
			return $this->move();
		}

		if ($control->oper == 'copy') {
			return $this->copy();
		}

		if ($control->oper == 'list') {
			return $this->printList();
		}

		if ($control->oper == 'itemlist') {
			return $this->printListItem();
		}

		if ($control->oper == 'groupdel') {
			return $this->groupDel();
		}

		if ($control->oper == 'moveto') {
			return $this->moveTo();
		}

		if (isset($_POST['mode']) && $_POST['mode'] == 'trigger') {
			return $this->trigger();
		}


	}

	//Вывод списка блоков
	function printList() {
		global $control;
		global $config;
		$limit = 40;

		$parent = all::getVar("parent");

		$page->status = $_SESSION['admin_status'];
		$page->admin_id = user_is('admin_id');

		$page->sitename = $control->settings->sitename;
		$page->theme = parent::$mainTheme;;
		if (user_is("super") == '1') $page->super = true;
		$page->parent = $parent;


		//Возможные блоки
		$blockTypes = sql::one_record("SELECT blocktypes FROM prname_ctemplates WHERE `key`=(SELECT template FROM prname_categories WHERE id=".$parent.")");

		$blockTypes = preg_split("/ /", $blockTypes, null, PREG_SPLIT_NO_EMPTY);
		$blockTypes = array_unique($blockTypes);


		//Если папка не имеет возможных блоков
		if (count($blockTypes) == 0 && !user_is("super")) {
			header("Location: /manage/");
			return;
		}

		if (user_is("super")) $treshold = 0;
		else $treshold = 1;

		//Если несколько возможных блоков - запихиваем в селект
		if (count($blockTypes) > $treshold) {
			foreach ($blockTypes as $val) {
				$info = sql::fetch_assoc(sql::query("SELECT * FROM prname_btemplates WHERE `key`='".$val."'"));
				$page->blocktypes[$info['id']]->name = $info['name'];
				$page->blocktypes[$info['id']]->key = $info['key'];
			}
		}

		if (user_is("super")) {
			$btemplates = sql::query("SELECT * FROM prname_btemplates");

			while ($btemplate = sql::fetch_assoc($btemplates)) {
				if (!isset($page->blocktypes[$btemplate['id']])) {
					$page->sblocktypes[$btemplate['id']]->name = $btemplate['name'];
					$page->sblocktypes[$btemplate['id']]->key = $btemplate['key'];
				}
			}

		}

		//Текущий шаблон - если передан параметром - значит он, если нет - первый из возможных, если и их нет и супрадмин - то первый из всех
		$currentTemplate = all::getVar("template");
		if (!$currentTemplate) $currentTemplate = reset($blockTypes);
		if (!$currentTemplate && user_is("super")) {
			foreach ($page->sblocktypes as $val) {
				$currentTemplate = $val->key;
				break;
			}
		}

		// Если есть текущий шаблон - выбираем возможные места переноса блоков
		if ($currentTemplate) {
			$sql = "SELECT tree.*, templ.blocktypes as block FROM prname_ctemplates templ, prname_tree tree WHERE templ.key=tree.template AND tree.id>10";

			$query = sql::query($sql);
			while ($res = sql::fetch_object($query)) {
				$res->levels = "";
				$i = $res->level;
				while($i > 1) {
					$res->levels .= "&nbsp;&nbsp;&nbsp;&nbsp;";
					$i--;
				}
				if (strpos($res->block, $currentTemplate." ") === 0 || strpos($res->block, " ".$currentTemplate." ") > -1) {
					$res->disabled = false;
				}
				else {
					$res->disabled = true;
				}

				if ($res->id == $parent) {
					$res->disabled = true;
				}
				$page->moveTo[] = $res;
			}
		}




		//Только если есть что выбирать
		if ($currentTemplate) {
			// Страница текущая
			if (all::getVar("page") != "") {
				$page->lpage = $lpage = all::getVar("page");
			}
			else {
				$page->lpage = $lpage = 0;
			}

			$start = 0 + $lpage * $limit;

			// Узнаем кол-во блоков текущего шаблона
			$totalcount = sql::one_record("SELECT count(id) FROM prname_b_".$currentTemplate." WHERE parent=".$parent);

			$tempUrl = $control->module_url;

			// Если блоков больше, чем влазит на страницу - делаем постраничку
			if ($totalcount > $limit) {
				$pagecount = ceil($totalcount / $limit);
				for ($i = 0; $i < $pagecount; $i ++) {
					$page->page[$i]->title = $i+1;
					$page->page[$i]->url = $tempUrl."_alist_parent".$parent."_page".$i."/";

					$page->page[$i]->active = $i == $lpage ? true : false;
				}
			}



			//Выборка блоков текущего шаблона
			$result = sql::query("SELECT * FROM prname_b_".$currentTemplate." WHERE parent=".$parent." ORDER by sort ASC LIMIT $start, $limit");

			$dataRel = sql::query("SELECT p2.* from prname_btemplates p1, prname_bdatarel p2 WHERE p1.key='".$currentTemplate."' AND p2.templid=p1.id AND p2.show=1 ORDER by p2.tab, p2.sort");

			$j = 0;
			while ($dr = sql::fetch_assoc($dataRel)) {
				$page->fields[$j] = (object)$dr;
				$j ++;
			}


			$j = 0;
			while ($r = sql::fetch_assoc($result)) {
				$page->item[$j]->id = $r['id'];

				$name = "";

				$jj = 0;
				foreach ($page->fields as $val1) {
					// генерируем вывод информации в таблицу
					$value = $r[$val1->key];
					$datatkey = $val1->datatkey;

					$class = "type_".$datatkey;
					$obj = new $class();
					$genValue = $obj->get($value, $val1->comment, $val1->readonly, $r['id'], $val1->key);

					$page->item[$j]->fields[$jj]->val = $genValue;

					$jj ++;
				}

				$page->item[$j]->visible = $r['visible'];

				$j ++;

			}
			$page->template = $currentTemplate;
			$page->templname = sql::one_record("SELECT name FROM prname_btemplates WHERE `key`='".$currentTemplate."'");
			$page->rights = $this->getRights($currentTemplate);
		}


		$page->menu = $this->menu;
		$page->name = sql::one_record("SELECT name FROM prname_tree WHERE id=".$parent);
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}

	//Вывод списка вложенных блоков
	function printListItem() {
		global $control;

		$parent = all::getVar("parent");
		$blockparent = all::getVar("id");
		$template = all::getVar("template");

		$page->status = $_SESSION['admin_status'];
		$page->admin_id = user_is('admin_id');

		$page->sitename = $control->settings->sitename;
		$page->theme = parent::$mainTheme;
		if (user_is("super") == '1') $page->super = true;
		$page->parent = $parent;


		if ($blockparent) {
			$result = sql::query("SELECT * FROM prname_b_".$template." WHERE blockparent=".$blockparent." ORDER by sort");

			$dataRel = sql::query("SELECT p2.* from prname_btemplates p1, prname_bdatarel p2 WHERE p1.key='".$template."' AND p2.templid=p1.id AND p2.show=1 ORDER by p2.sort");

			$j = 0;
			while ($dr = sql::fetch_assoc($dataRel)) {
				$dataKey[$j] = $dr['key'];
				$page->fields[$j]->name = $dr['name'];
				$j ++;
			}
			$j = 0;
			while ($r = sql::fetch_assoc($result)) {
				$page->item[$j]->id = $r['id'];

				$name = "";

				$jj = 0;
				foreach ($dataKey as $val1) {
					$page->item[$j]->fields[$jj]->val = mb_substr(strip_tags($r[$val1]), 0, 50);
					$jj ++;
				}

				$page->item[$j]->name = $name;
				$page->item[$j]->visible = $r['visible'];

				$j ++;

			}
			$page->template = $template;
			$page->blockparent = $blockparent;

			$page->rights = $this->getRights($template);

			$page->canAdd = $this->getRight("canadd", $template);
		}
		else {
			$page->no = true;
		}

		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/itemlist.html');
	}

	//Вывод формы добавления блока
	function printAdd($mode='') {
		global $control;


		$parent = all::getVar("parent");
		$template = all::getVar("template");
		$page->lpage = all::getVar("page");
		if (!$page->lpage) {
			$page->lpage = 0;
		}


		$page->status = $_SESSION['admin_status'];
		$page->admin_id = user_is('admin_id');

		$page->sitename = $control->settings->sitename;
		$page->theme = parent::$mainTheme;
		if (user_is("super") == '1') $page->super = true;
		$page->parent = $parent;
		$page->template = $template;

		if ($mode == 'item') {
			$page->blockparent = all::getVar("blockparent");
		}

		//Сео поля
		$seo = sql::one_record("SELECT seo FROM prname_btemplates WHERE `key`='".$template."'");
		if ($seo > 0) {
			$page->addFields[0]->name = "Адрес блока";
			$page->addFields[0]->key = "uurl";

			$page->addFields[1]->name = "Title";
			$page->addFields[1]->key = "utitle";

			$page->addFields[2]->name = "Description";
			$page->addFields[2]->key = "udescription";

			$page->addFields[3]->name = "Keywords";
			$page->addFields[3]->key = "ukeywords";
		}


		//Поля блока
		$templId = sql::one_record("SELECT id FROM prname_btemplates WHERE `key`='$template'");

		$sqlFields = sql::query("SELECT * FROM prname_bdatarel WHERE templid=$templId ORDER BY tab, sort, `key`");


		$i = 0;
		while ($field = sql::fetch_assoc($sqlFields)) {

			$class = "type_".$field['datatkey'];
			$obj = new $class();
			$genValue = $obj->input('data'.$i, '', $field['comment'], $field['readonly']);

			//Если тип данных - загрузка изображений - выносим в отдельную вкладку (так как нужна новая форма для dropzone)

			if ($field['datatkey'] == 'imageload') {
				$genValue2 = $obj->input2('data'.$i, '', $field['comment']);

				$page->imageload[$i]->value = $genValue;
				$page->imageload[$i]->number = $i*2000;
				$page->imageload[$i]->name = $field['name'];

				$page->tabs[0]->fields[$i]->name = $field['name'];
				$page->tabs[0]->fields[$i]->value = $genValue2;
				$page->tabs[0]->fields[$i]->key = $field['key'];
				$page->tabs[0]->fields[$i]->datatkey = $field['datatkey'];
				$page->tabs[0]->fields[$i]->index = $i;

			}

			else {
				$tab = $field['tab'];
				$page->tabs[$tab]->id = $tab;
				$page->tabs[$tab]->fields[$i]->name = $field['name'];
				$page->tabs[$tab]->fields[$i]->key = $field['key'];
				$page->tabs[$tab]->fields[$i]->value = $genValue;
				$page->tabs[$tab]->fields[$i]->datatkey = $field['datatkey'];
				$page->tabs[$tab]->fields[$i]->index = $i;
			}

			$i ++;
		}


		$page->add = true;

		if ($mode == '') {
			$page->menu = $this->menu;
			$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_add.html');
		}
		else {
			$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/itemedit.html');
		}
	}


	//Обработка POST на добавление блока
	function add($mode='') {
		global $control;
		$cache = new phpFastCache();
		$cache->cleanup();
		$parent = $_POST['parent'];
		$template = $_POST['template'];
		$lpage = $_POST['page'];



		if ($mode == 'item') {
			$blockparent = $_POST['blockparent'];
		}

		$sort = 1 + sql::one_record("SELECT MAX(sort) as msort FROM prname_b_".$template);

		//Сначала то, что знаем
		$query = "INSERT INTO prname_b_".$template." SET parent=".$parent.", visible=1, sort=".$sort;

		if ($mode == 'item') {
			//Сначала то, что знаем
			$query = "INSERT INTO prname_b_".$template." SET visible=1, sort=".$sort.", blockparent=".$blockparent;
		}

		sql::query($query);

		$_SESSION['newId'] = sql::insert_id();


		$query = "UPDATE prname_b_".$template." SET visible=1";


		//Дальше - поля блока
		for ($i=0; $i < count($_POST['dat']); $i++) {
			$class = "type_".$_POST['dkey'][$i];
			$obj = new $class();
			$genValue = mysql_real_escape_string($obj->save('data'.$i));

			$query .= " , `".addslashes($_POST['dat'][$i])."` = '".$genValue."' ";
		}





		//А потом SEO-поля
		if ($_POST['uurl'] != '') {
			$value = htmlspecialchars(trim($_POST['uurl']));
			$value = preg_replace("#[\"']#", "", $value);
			$url = sql::one_record("SELECT url FROM prname_tree WHERE id=".$parent);
			$value = $url.$value."/";
			$value = $this->matchesUrl($value);
			$query .= " , `uurl`='".$value."' ";
		}

		if ($_POST['utitle'] != '') {
			$value = htmlspecialchars(trim($_POST['utitle']));
			$query .= " , `utitle`='".$value."' ";
		}

		if ($_POST['udescription'] != '') {
			$value = htmlspecialchars(trim($_POST['udescription']));
			$query .= " , `udescription`='".$value."' ";
		}



		if ($_POST['ukeywords'] != '') {
			$value = htmlspecialchars(trim($_POST['ukeywords']));
			$query .= " , `ukeywords`='".$value."' ";
		}

		$query .= " WHERE id=".$_SESSION['newId'];


		sql::query($query);

		//Сохраняем в таблицу с ЧПУ
		if ($_POST['uurl'] != '') {
			$value = htmlspecialchars(trim($_POST['uurl']));

			$value = preg_replace("#[\"']#", "", $value);

			$url = sql::one_record("SELECT url FROM prname_tree WHERE id=".$parent);
			$value = $url.$value."/";

			$value = $this->matchesUrl($value);


			$lastId = sql::one_record("SELECT MAX(id) FROM prname_b_".$template);
			$realUrl = $url;

			$urlSql = "INSERT INTO prname_urls (`url`, `realurl`, `template`, `blockid`) VALUES ('".$value."', '".$realUrl."', '".$template."', ".$lastId.")";
			sql::query($urlSql);
		}



		if ($mode == '') {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."_page".$lpage."/");
		}
		else {
			header("Location: /manage/blockedit/_aitemlist_parent".$parent."_id".$blockparent."_template".$template."/");
		}

	}

	//Вывод формы редактирования блока
	function printEdit($mode='') {
		global $control;
		$parent = all::getVar("parent");
		$template = all::getVar("template");
		$blockid = all::getVar("id");

		$page->lpage = all::getVar("page");
		if (!$page->lpage) {
			$page->lpage = 0;
		}

		if ($mode == 'item') {
			$page->blockparent = all::getVar("blockparent");
		}




		$page->status = $_SESSION['admin_status'];
		$page->admin_id = user_is('admin_id');

		$page->sitename = $control->settings->sitename;
		$page->theme = parent::$mainTheme;
		if (user_is("super") == '1') $page->super = true;
		$page->parent = $parent;
		$page->template = $template;
		$page->blockid = $blockid;



		//Спец поля
		$seo = sql::one_record("SELECT seo FROM prname_btemplates WHERE `key`='".$template."'");
		if ($seo > 0) {

			$aF = sql::fetch_assoc(sql::query("SELECT uurl, utitle, udescription, ukeywords FROM prname_b_".$template." WHERE id=".$blockid));

			$i = 0;
			foreach ($aF as $key => $val) {
				switch ($key) {
					case "uurl" : $page->addFields[$i]->name = "Адрес блока"; break;
					case "utitle" : $page->addFields[$i]->name = "Title"; break;
					case "udescription" : $page->addFields[$i]->name = "Description"; break;
					case "ukeywords" : $page->addFields[$i]->name = "Keywords"; break;
				}

				$page->addFields[$i]->key = $key;

				$page->addFields[$i]->value = $val;
				if ($key == 'uurl' && $val != '') {
					$value = trim($val, "/");
					$value = substr($value, strrpos($value, "/")+1);
					$page->addFields[$i]->value = $value;
				}
				$i ++;
			}
		}


		//Поля блока
		$templId = sql::one_record("SELECT id FROM prname_btemplates WHERE `key`='".$template."'");

		$sqlFields = sql::query("SELECT * FROM prname_bdatarel WHERE templid=".$templId." ORDER BY tab, sort, `key`");

		$sqlData = sql::fetch_assoc(sql::query("SELECT * FROM prname_b_".$template." WHERE id=".$blockid));

		$i = 0;
		while ($field = sql::fetch_assoc($sqlFields)) {
			$value = $sqlData[$field['key']];

			$class = "type_".$field['datatkey'];
			$obj = new $class();
			$genValue = $obj->input('data'.$i, $value, $field['comment'], $field['readonly']);


			//Если тип данных - загрузка изображений - выносим в отдельную вкладку (так как нужна новая форма для dropzone)

			if ($field['datatkey'] == 'imageload') {
				$genValue2 = $obj->input2('data'.$i, $value, $field['comment']);

				$page->imageload[$i]->value = $genValue;
				$page->imageload[$i]->number = $i*2000;
				$page->imageload[$i]->name = $field['name'];

				$page->tabs[0]->fields[$i]->name = $field['name'];
				$page->tabs[0]->fields[$i]->value = $genValue2;
				$page->tabs[0]->fields[$i]->key = $field['key'];
				$page->tabs[0]->fields[$i]->datatkey = $field['datatkey'];
				$page->tabs[0]->fields[$i]->index = $i;

			}


			else {
				$tab = $field['tab'];
				$page->tabs[$tab]->id = $tab;
				$page->tabs[$tab]->fields[$i]->name = $field['name'];
				$page->tabs[$tab]->fields[$i]->key = $field['key'];
				$page->tabs[$tab]->fields[$i]->value = $genValue;
				$page->tabs[$tab]->fields[$i]->datatkey = $field['datatkey'];
				$page->tabs[$tab]->fields[$i]->index = $i;
			}

			$i ++;
		}



		if ($mode == '') {
			$page->menu = $this->menu;
			$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_add.html');
		}
		else {
			$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/itemedit.html');
		}

	}


	//Обработка POST на редактирование блока
	function edit($mode='') {

		global $control;
		$cache = new phpFastCache();
		$cache->cleanup();
		$parent = $_POST['parent'];
		$template = $_POST['template'];
		$blockid = $_POST['blockid'];
		$lpage = $_POST['page'];



		if ($mode == 'item') {
			$blockparent = $_POST['blockparent'];
		}

		//Сначала то, что знаем
		$query = "UPDATE prname_b_".$template." SET visible=visible";

		//Дальше - поля блока
		for ($i=0; $i < count($_POST['dat']); $i++) {
			$class = "type_".$_POST['dkey'][$i];
			$obj = new $class();
			$genValue = mysql_real_escape_string($obj->save('data'.$i));

			$query .= " , `".addslashes($_POST['dat'][$i])."` = '".$genValue."' ";
		}


		//А потом SEO-поля
		$seo = sql::one_record("SELECT seo FROM prname_btemplates WHERE `key`='".$template."'");

		if ($seo > 0) {
			$value = htmlspecialchars(trim($_POST['uurl']));
			$value = preg_replace("/[\"']+/", "", $value);

			//Если урл пустой пришел - удаляем запись из базы урлов
			if ($value == "") {
				$query .= " , `uurl`='' ";
				sql::query("DELETE FROM prname_urls WHERE template='".$template."' AND blockid=".$blockid);
			}
			//Если есть урл, то пишем его в базу урлов
			else {
				$url = $realUrl = sql::one_record("SELECT url FROM prname_tree WHERE id=".$parent);

				$value = $url.$value."/";
				$value = $this->matchesUrl($value, $blockid, $template);

				$query .= " , `uurl`='$value' ";

				$urlS = sql::one_record("SELECT id FROM prname_urls WHERE template='".$template."' AND blockid=".$blockid);
				//Если урл уже был - обновляем
				if ($urlS > 0) sql::query("UPDATE prname_urls SET url='".$value."' WHERE template='".$template."' AND blockid=".$blockid);
				//Если нет - вставляем новую запись
				else sql::query("INSERT INTO prname_urls (`url`, `realurl`, `template`, `blockid`) VALUES ('".$value."', '".$realUrl."', '".$template."', ".$blockid.")");
			}


			if ($_POST['utitle'] != '') {
				$value = htmlspecialchars(trim($_POST['utitle']));
				$query .= " , `utitle`='".$value."' ";
			}

			if ($_POST['udescription'] != '') {
				$value = htmlspecialchars(trim($_POST['udescription']));
				$query .= " , `udescription`='".$value."' ";
			}

			if ($_POST['ukeywords'] != '') {
				$value = htmlspecialchars(trim($_POST['ukeywords']));
				$query .= " , `ukeywords`='".$value."' ";
			}
		}

		$query .= " WHERE id=".$blockid;

		//$this->updateCache($blockid, $parent, $template);

		sql::query($query);

		if ($mode == '') {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."_page".$lpage."/");
		}
		else {
			header("Location: /manage/blockedit/_aitemlist_parent".$parent."_id".$blockparent."_template".$template."/");
		}

	}

	function delete($mode) {
		global $control;

		$cache = new phpFastCache();
		$cache->cleanup();

		$blockid = all::getVar("id");
		$template = all::getVar("template");
		$parent = all::getVar("parent");

		if ($mode == 'item') {
			$blockparent = all::getVar("blockparent");
		}

		//Определяем разрешение на удаление
		if (!$this->getRight("candel", $template)) {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
			return;
		}

		delete_block($blockid, $template);

		if ($mode == '') {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
		}
		else {
			header("Location: /manage/blockedit/_aitemlist_parent".$parent."_id".$blockparent."_template".$template."/");
		}
	}

	//Групповое удаление
	function groupDel() {
		global $control;

		$cache = new phpFastCache();
		$cache->cleanup();

		$ids = all::getVar("ids");
		$template = all::getVar("template");
		$parent = all::getVar("parent");

		//Определяем разрешение на удаление
		if (!$this->getRight("candel", $template)) {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
			return;
		}

		$ids = explode(";", $ids);

		foreach ($ids as $val) {
			if ($val !== "") {
				delete_block($val, $template);
			}
		}

		header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
	}

	function move() {
		global $control;

		$cache = new phpFastCache();
		$cache->cleanup();

		$blockid = all::getVar("id");
		$template = all::getVar("template");
		$parent = all::getVar("parent");
		$after = all::getVar("after");
		$before = all::getVar("before");


		//Определяем разрешение на перемещение
		if (!$this->getRight("canmove", $template)) {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
			return;
		}

		if ($after > 0) {

			$sort = sql::one_record("SELECT sort FROM prname_b_$template WHERE id=$after");
			sql::query("UPDATE prname_b_$template SET sort=sort+1 WHERE sort>$sort AND parent=$parent");
			sql::query("UPDATE prname_b_$template SET sort=".($sort+1)." WHERE id=$blockid");
		}

		if ($before > 0) {
			$sort = sql::one_record("SELECT sort FROM prname_b_$template WHERE id=$before");
			sql::query("UPDATE prname_b_$template SET sort=sort+1 WHERE sort>=$sort AND parent=$parent");
			sql::query("UPDATE prname_b_$template SET sort=".$sort." WHERE id=$blockid");
		}

		header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
	}

	function copy() {
		global $control;

		$cache = new phpFastCache();
		$cache->cleanup();

		$blockid = all::getVar("id");
		$template = all::getVar("template");
		$parent = all::getVar("parent");

		//Определяем разрешение на копирование
		if (!$this->getRight("cancopy", $template)) {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
			return;
		}

		$infoBlock = sql::fetch_assoc(sql::query("SELECT * FROM prname_b_".$template." WHERE id=".$blockid));
		unset($infoBlock['id']);

		$sort = 1 + sql::one_record("SELECT MAX(sort) FROM prname_b_".$template." WHERE parent=".$parent);
		$infoBlock['sort'] = $sort;
		if (isset($infoBlock['uurl']) && $infoBlock['uurl'] != "") {
			$infoBlock['uurl'] = $this->matchesUrl($infoBlock['uurl']);
		}
		$query = "INSERT INTO prname_b_".$template." SET ";

		$i = 0;
		foreach ($infoBlock as $key => $val) {
			if ($i != 0) $query .= ", ";
			$query .= "`".$key."` = '".$val."'";
			$i ++;
		}

		sql::query($query);

		$lastId = sql::one_record("SELECT MAX(id) FROM prname_b_".$template);

		if (isset($infoBlock['uurl']) && $infoBlock['uurl'] != "") {
			$url = $infoBlock['uurl'];
			sql::query("INSERT INTO prname_urls (`url`, `realurl`, `template`, `blockid`) SELECT '".$url."', `realurl`, `template`, ".$lastId." FROM prname_urls WHERE template='".$template."' AND blockid=".$blockid);

		}

		header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
	}

	function showHide() {
		global $control;

		$cache = new phpFastCache();
		$cache->cleanup();

		$parent = all::getVar("parent");
		$blockid = all::getVar("id");
		$template = all::getVar("template");

		//Определяем разрешение на скрытие/показ
		if (!$this->getRight("canhide", $template)) {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
			return;
		}

		sql::query("UPDATE prname_b_".$template." SET visible=1-visible WHERE id=".$blockid);

		header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
	}

	//Групповое скрытие
	function groupHide() {
		global $control;

		$cache = new phpFastCache();
		$cache->cleanup();

		$parent = all::getVar("parent");
		$ids = all::getVar("ids");
		$template = all::getVar("template");

		//Определяем разрешение на скрытие/показ
		if (!$this->getRight("canhide", $template)) {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
			return;
		}

		$ids = explode(";", $ids);

		foreach ($ids as $val) {
			if ($val !== "") {
				sql::query("UPDATE prname_b_".$template." SET visible=0 WHERE id=".$val);
			}
		}

		header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
	}

	//Групповое скрытие
	function groupShow() {
		global $control;

		$cache = new phpFastCache();
		$cache->cleanup();

		$parent = all::getVar("parent");
		$ids = all::getVar("ids");
		$template = all::getVar("template");

		//Определяем разрешение на скрытие/показ
		if (!$this->getRight("canhide", $template)) {
			header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
			return;
		}

		$ids = explode(";", $ids);

		foreach ($ids as $val) {
			if ($val !== "") {
				sql::query("UPDATE prname_b_".$template." SET visible=1 WHERE id=".$val);
			}
		}

		header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
	}


	// Перемещение блоков к другому родителю
	function moveTo() {
		global $control;
		$ids = all::getVar("ids");
		$template = all::getVar("template");
		$parent = all::getVar("parent");
		$newParent = all::getVar("new") + 1 - 1;

		$ids = explode(";", $ids);
		$sort = sql::one_record("SELECT MAX(sort) FROM prname_b_$template WHERE parent=$newParent");

		foreach ($ids as $val) {
			if ($val !== "") {
				$sort ++;
				sql::query("UPDATE prname_b_$template SET parent=$newParent, sort=$sort WHERE id=$val");
			}
		}

		header("Location: /manage/blockedit/_alist_parent".$parent."_template".$template."/");
	}

	function getRights($template) {
		if (user_is("super") != "1") {
			$rights = sql::fetch_assoc(sql::query("SELECT canadd, candel, canedit, canmove, cancopy, canhide FROM prname_btemplates WHERE `key`='".$template."'"));

			foreach ($rights as $key => $val) {
				$rights[0]->$key = $val;
			}
			return $rights;
		}
		else return 0;
	}

	function getRight($name, $template) {
		if (user_is("super")) return true;
		$right = sql::one_record("SELECT ".$name." FROM prname_btemplates WHERE `key`='".$template."'");
		if ($right > 0) return true;
		return false;
	}

	function matchesUrl($url, $id=null, $template=null) {
		if ($id != null) {
			$matches = sql::one_record("SELECT id FROM prname_urls WHERE url='".$url."' AND (blockid<>".$id." AND template='".$template."')");
		}
		else {
			$matches = sql::one_record("SELECT id FROM prname_urls WHERE url='".$url."'");
		}


		if ($matches != "") {
			$value = trim($url, "/");
			$value .= rand(0,9)."/";

			return $this->matchesUrl($value, $id, $template);
		}
		else {
			return $url;
		}
	}

	function updateCache($blockid, $parent, $template) {
		$data = all::b_data_all($blockid, $template);
		$list = new Listing($template, 'blocks', $parent, " id=$blockid AND ");
		$list->getList();
		$list->getItem();

		$item = $list->item[0];
		$url = str_replace(array('{base_url}', '<!--base_url//-->'), '', $item->url);

		$arr_string = preg_split('#/#', $url, null, PREG_SPLIT_NO_EMPTY);

		if (count($arr_string) > 0) {
			$cdir = 'templates/_templates/';

			foreach ($arr_string as $one_arr) {
				$d = opendir($cdir);
				while ( $entry = readdir($d) ){
					if (is_file($cdir.$entry) && $entry != '..' && $entry != '.') {

						unlink($cdir.$entry);
					}
				}

				$cdir .= $one_arr.'/';
			}

			$d = opendir($cdir);
				while ( $entry = readdir($d) ){
				if (is_file($cdir.$entry) && $entry != '..' && $entry != '.') {

					unlink($cdir.$entry);
				}
			}
		}
	}

	private function trigger() {
		global $control, $config;

		$id = $_POST['id'] + 1 - 1;
		$template = $_POST['template'];
		$field = $_POST['field'];
		$value = $_POST['value'];

		if ($value == "true") {
			$value = 1;
		}
		else {
			$value = 0;
		}
		$sql = "UPDATE prname_b_$template SET $field=$value WHERE id=$id";
		sql::query($sql);
		die();
	}
}
?>