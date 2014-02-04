<?php
class e404 {

	public function __construct() {
		global $control;
		$this->printOne($control->cid);
	}

	private function printOne($bid) {
		global $control;
		$page->text = all::c_data_all($cid, "e404")->text;

		header("HTTP/1.0 404 Not Found");
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>