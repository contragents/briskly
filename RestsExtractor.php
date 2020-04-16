<?php
class RestsExtractor {
	
	public static function loadData(array $goods) {
		$rests = [];
		$zp = zip_open('out/5.zip');
		
		while ($file = zip_read($zp)) {
			$xmlstr = zip_entry_read($file, zip_entry_filesize($file));
			$xml = new SimpleXMLElement($xmlstr);

			foreach ($xml->ПакетПредложений->Предложения->Предложение as $group) {
				if (isset($goods[(string)$group->Ид])) {
					$rests[(string)$group->Ид] = [];
					$rests[(string)$group->Ид]['rest'] = 0;
					//Инициализировали остатки по продукту
					
					foreach ($group->Остатки->Остаток as $rest) {
						$rests[(string)$group->Ид]['rest'] += (int)$rest->Склад->Количество;
						$rests[(string)$group->Ид][(string)$rest->Склад->Ид] = (int)$rest->Склад->Количество;
					}
				}
			}
		}
		return $rests;
	}
}
