<?php
class PricesExtractor {
	
	public static function enrichData(array &$goods) {
		$zp = zip_open('out/4.zip');
		
		while ($file = zip_read($zp)) {
			$xmlstr = zip_entry_read($file,zip_entry_filesize($file));
			$xml = new SimpleXMLElement($xmlstr);
			
			foreach( $xml->ПакетПредложений->Предложения->Предложение as $group) {
				if (isset($goods[(string)$group->Ид]))
					$goods[(string)$group->Ид]['price'] = (string)$group->Цены->Цена->ЦенаЗаЕдиницу;
			}
		}
	}
}
