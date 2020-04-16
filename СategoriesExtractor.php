<?php
class СategoriesExtractor {
	
	public static function loadData() {
		$categories=[];
		$zp = zip_open('out/1.zip');
		
		while ($file = zip_read($zp)) {
			$xmlstr = zip_entry_read($file, zip_entry_filesize($file));
			$xml = new SimpleXMLElement($xmlstr);
			
			foreach( $xml->Классификатор->Группы->Группа as $group) {
				$categories[] = ['id' => (string)$group->Ид, 'name' => (string)$group->Наименование];
				
				if (isset($group->Группы))
					foreach( $group->Группы->Группа as $subgroup) 
						$categories[] = ['id' => (string)$subgroup->Ид, 'name' => (string)$subgroup->Наименование];
				//Проверяем подкатегории и создаем, если есть
			}
		}
		
		return $categories;
	}
}
