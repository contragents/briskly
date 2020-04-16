<?php
class GoodsCatalogExtractor {
	
	private static $logfile = false;
	
	public static function loadData() {
		$goods=[];
		$zp = zip_open('out/2.zip');
		
		while ($file = zip_read($zp)) {
			$xmlstr = zip_entry_read($file, zip_entry_filesize($file));
			$xml = new SimpleXMLElement($xmlstr);
			
			foreach( $xml->Каталог->Товары->Товар as $group) {
				
				if (!self::ean13Validate((string)$group->Штрихкод))
					self::writeLog((string)$group->Штрихкод . ' - Невалидный штрихкод. Ид товара: ' . (string)$group->Ид);
					//Просто записали в лог невалидный штрихкод и работаем дальше
				
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
	
	private static function ean13Validate($digits) {
		
		if (strlen($digits) != 13)
			return false;
		
		foreach (str_split($digits) as $digit)
			if (!is_numeric($digit))
				return false;
		
		//Простые проверки пройдены - вычисляем проверочный код
		
		// 1. Соберем сумму из четных чисел: 2, 4, 6, и т.д.
		$even_sum = $digits{1} + $digits{3} + $digits{5} + $digits{7} + $digits{9} + $digits{11};
		
		// 2. Умножим результат на 3.
		$even_sum_three = $even_sum * 3;
		
		// 3. Соберем сумму из нечетных чисел: 1, 3, 5, и т.д.
		$odd_sum = $digits{0} + $digits{2} + $digits{4} + $digits{6} + $digits{8} + $digits{10};
		
		// 4. Сложим результаты в пп 2 и 3.
		$total_sum = $even_sum_three + $odd_sum;
		
		// 5. Найдем число, которого не хватает числу из п4 до следующего десятка.
		$next_ten = (ceil($total_sum/10))*10;
		$check_digit = $next_ten - $total_sum;
		
		if ($digits{12} = $check_digit)
			return true;
		else 
			return false;
	}
	
	private static function writeLog($message) {
		if (!is_resource(self::$logfile))
			self::$logfile = fopen('logs/'.date('YmdHis'), 'a');
			//Открыли файл с именем, соответствующим данному времени
		
		fwrite(self::$logfile, $message . PHP_EOL);
	}
}
