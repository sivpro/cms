<?php
class first {

	public function __construct() {
		global $control;

		$this->printList($control->module_parent);

		if (isset($_POST['mode']) && $_POST['mode'] == 'lang') {
			return $this->changeLang();
		}
	}

	private function printList($cid) {
		global $control;


		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}

	function changeLang() {
		$lang = $_POST['lang'];

		$_SESSION['lang'] = $lang;
		die($_SESSION['lang']);
	}


}
?>