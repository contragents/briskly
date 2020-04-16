<?php
class GoodsCatalogExtractor {
	
	public static function loadData() {
		$goods=[];
		$zp = zip_open('out/2.zip');
		
		while ($file = zip_read($zp)) {
			$xmlstr = zip_entry_read($file, zip_entry_filesize($file));
			$xml = new SimpleXMLElement($xmlstr);
			
			foreach( $xml->Каталог->Товары->Товар as $group) {
				if (!isset($goods[(string)$group->Ид]))
				$goods[(string)$group->Ид] = [
					'id' => (string)$group->Ид,
					'barcodes' => [(string)$group->Штрихкод],
					'category_id' => (string)$group->Группы->Ид,
					'description' => (string)$group->Описание,
					'name' => (string)$group->Наименование,
					'price' => '?',
					'unit_id' => (string)$group->БазоваяЕдиница,
				];
				else
					$goods[(string)$group->Ид]['barcodes'][] = (string)$group->Штрихкод;
				//Добавили Штрихкод к существующему товару
			}
		}
		return $goods;
	}
}
