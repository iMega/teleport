# iMega Teleport 2

[![Build Status](https://travis-ci.org/iMega/teleport.svg?branch=master)](https://travis-ci.org/iMega/teleport)
[![Circle Build Status](https://circleci.com/gh/iMega/teleport.svg?style=shield)](https://circleci.com/gh/iMega/teleport)
[![Code Coverage](https://scrutinizer-ci.com/g/iMega/teleport/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/iMega/teleport/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/iMega/teleport/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/iMega/teleport/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5571ecfd393530001c000001/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5571ecfd393530001c000001)

Contributors: iMega

Donate link: http://teleport.imega.ru/donate

License: GPLv3

License URI: http://www.gnu.org/licenses/gpl-3.0.html

Import your products from your 1C to your eShop.

Взаимосвязь интернет-магазина и 1С.

### Description

##### In English.

Import your products from your 1C to your eShop.

Import data that contain title of goods, price, properties 

and characteristics of the goods, description and picture, 

the amount of goods available for sale.

Export orders and change status of orders.

### На русском.

iMegaTeleport обеспечивает взаимосвязь интернет-магазина и 1С 

через базовый модуль «обмен 1С и сайта», встроенного 

в конфигурациях 1С: Управление торговлей, Торговля и склад, 

Управление производственным предприятием, а также некоторых 

других продуктах 1С.

iMegaTeleport выгружает данные о товаре: название, цена, 

свойства и характеристики товара, описание и изображение, 

доступный остаток товара, а также структуру каталога 

товаров (группы номенклатуры).

Количество товаров, которое можно выгрузить ограничено 
возможностями сервера, на котором работает программа 1С.

Обрабатывает заказы покупателей, используя статусы: 
"В обработке", "Завершен", "Отменен".


Ознакомьтесь пожалуйста с [инструкцией](http://teleport.imega.ru/instructions).

# Dev

You needs docker and make.

Quick start `make build test start`

`make help`

