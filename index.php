<?php
/*
Создаем целевой JSON-файл на основании .zip-файлов из категории out
1.zip - Категории товаров + Склады 
2.zip - Каталог товаров
3.zip - Не используем, нет полезной инфо
4.zip - Цены на товары
5.zip - Остатки - работаем только с Ид складов по заданию
*/

require_once('autoload.php');
//Используемые классы для простоты расположены в одноименных файлах в текущей папке
 
$target_json = [];
//Целевой JSON-массив

$target_json['categories'] = СategoriesExtractor::loadData();
//Загрузили категории

$goods_catalog = GoodsCatalogExtractor::loadData();
//Загрузили каталог товаров

PricesExtractor::enrichData($goods_catalog);
//обогатили данные по товарам

$rests = RestsExtractor::loadData($goods_catalog);
//Создали массив остатков (всего по Ид товара + по каждому складу по Ид товара)

$target_json['items'] = array_values($goods_catalog);
//Убрали ключи-Ид товаров, чтобы точно соответствовать целевому файлу в задании

$target_json['tables'] = [];
//Не ясно, зачем tables в целевом файле задания и где брать инфу

$target_json['rests'] = $rests;
//Добавили остатки

print json_encode($target_json, JSON_UNESCAPED_UNICODE);