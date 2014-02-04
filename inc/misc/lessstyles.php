<?php

class lessstyles {

	function Make($wrapper) {
		$less = new lessc;

		try {
			$less->checkedCompile(DOC_ROOT."/css/style.less", DOC_ROOT."/css/style.css");
		}
		catch (exception $e) {
			echo "fatal error: " . $e->getMessage();
		}

		return;
	}
}
?>