<?php
  class head{

	function Make($wrapper)	{
		global $control;

		$page->title = $control->titleSeo;
		$page->description = $control->descriptionSeo;
		$page->keywords = $control->keywordsSeo;


		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>

