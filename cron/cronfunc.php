<?php

function subscribe($limit) {
	global $config;
	global $control;
	$list = & new Listing('mailsend', 'blocks', 'all');
	$list->limit = 1;
	$list->getList();
	$list->getItem();
	$page->item = $list->item;
	if (count($page->item) > 0) {
		$start = $page->item[0]->num > 0 ? $page->item[0]->num : 0;
		$stop = $start + $limit;
		$arr = explode(';', substr($page->item[0]->mailresr, 1, -1));
		if (count($arr) > 0) {
			foreach ($arr as $soptov) {
				$cri .= " `parent`='$soptov' or";
			}
		}
		$cri = substr($cri, 0, -2);

		$num = sql::query("select * from prname_b_mailers where $cri limit $start , $stop");
		// Обновляем кол-во отправленных писем
		all::update_block($page->item[0]->id, 'mailsend', "$stop", 'num');
		// =========================================================================================================================================================
		$imgpatch = "images/";
		$mail = new PHPMailer();
		$body = str_replace("/images/", "images/", sprintt($page, 'templates/temps/mail.html'));
		$body = eregi_replace("[\]", '', $body);
		$mail->FromName = iconv('utf-8', 'cp1251', $config[site_name]);
		$mail->Subject = $page->item[0]->title;
		preg_match_all("/src=\"(.*)\"/Uis", $body, $out);
		for ($i = 0; $i < count($out[1]); $i++) {
			$file_name = str_replace($imgpatch, '', $out[1][$i]);
			$id = md5($file_name);
			if ($imgs[$id] !== $id) {
				$arrimg[$i][file_patch] = $out[1][$i];
				$arrimg[$i][file_name] = str_replace($imgpatch, '', $out[1][$i]);
				$arrimg[$i][file_type] = substr($arrimg[$i][file_name], -3);
				$arrimg[$i][file_id] = md5($arrimg[$i][file_name]);
				$body = str_replace($out[1][$i], 'cid:'.$arrimg[$i][file_id], $body);
				$imgs[$id] = $id;
			}
		}
		$mail->MsgHTML(iconv('utf-8', 'cp1251', $body));
		for ($i = 0; $i < count($arrimg); $i++)
			$mail->AddEmbeddedImage($arrimg[$i][file_patch], $arrimg[$i][file_id], $arrimg[$i][file_name], "base64", "image/".$arrimg[$i][file_type]);

		// =========================================================================================================================================================
		// Прикрепление файлов
		for ($i = 0; $i < count($page->item); $i++) {
			$list_file = & new Listing('mailfiles', 'items', $page->item[$i]->id);
			$list_file->getList();
			$list_file->getItem();
			for ($ii = 0; $ii < count($list_file->item); $ii++)
				$mail->AddAttachment("files/0/".$list_file->item[$ii]->file, $list_file->item[$ii]->file);
		}
		if (sql::num_rows($num) > 0) {
			while ($arr = sql::fetch_object($num)) {
				$mail->AddAddress($arr->mail, iconv('utf-8', 'cp1251', $arr->fio));
				$mail->Send();
				$mail->ClearAddresses();
			}
		}
		else
			all::update_block($page->item[0]->id, 'mailsend', "0", 'visible');
	}
}

?>