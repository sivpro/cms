<?php

class lessstyles {

	function Make($wrapper) {
		$less = new lessc;
		$less->setFormatter("compressed");

		try {
			$less->checkedCompile(DOC_ROOT."/css/style.less", DOC_ROOT."/css/style.css");
		}
		catch (exception $e) {
			echo "fatal error on style.less: " . $e->getMessage();
		}

		try {
			$less->checkedCompile(DOC_ROOT."/css/response.less", DOC_ROOT."/css/response.css");
		}
		catch (exception $e) {
			echo "fatal error on response.less: " . $e->getMessage();
		}

		return;
	}
}
?>