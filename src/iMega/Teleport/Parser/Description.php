<?php
/**
 * Copyright (C) 2014 iMega ltd Dmitry Gavriloff (email: info@imega.ru),
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace iMega\Teleport\Parser;

/**
 * Class Description
 */
final class Description
{
    const CONTAINS_ONLY_THE_CHANGES = 'СодержитТолькоИзменения',
        DOCUMENT        = 'Документ',
        PACKAGEOFFERS   = 'ПакетПредложений',
        PRICETYPES      = 'ТипыЦен',
        PRICETYPE       = 'ТипЦены',
        CURRENCY        = 'Валюта',
        OFFERS          = 'Предложения',
        OFFER           = 'Предложение',
        BARCODE         = 'Штрихкод',
        BASEUNIT        = 'БазоваяЕдиница',
        PRODUCTFEATURES = 'ХарактеристикиТовара',
        PRODUCTFEATURE  = 'ХарактеристикаТовара',
        PRICES          = 'Цены',
        PRICE           = 'Цена',
        REPRESENTATION  = 'Представление',
        PRICETYPEID     = 'ИдТипаЦены',
        PRICEBYUNIT     = 'ЦенаЗаЕдиницу',
        UNIT            = 'Единица',
        RATIO           = 'Коэффициент',
        AMOUNT          = 'Количество',

        KEY = 'Код',
        FULLNAME = 'НаименованиеПолное',
        INTERNATIONALABBREVIATION = 'МеждународноеСокращение',
        ARTICLE = 'Артикул',

        CLASSI = 'Классификатор',
        GROUPS = 'Группы',
        GROUP = 'Группа',

        CATALOG = 'Каталог',
        PRODUCTS = 'Товары',
        PRODUCT = 'Товар',

        ATTRIBUTEVALUES = 'ЗначенияРеквизитов',
        ATTRIBUTEVALUE = 'ЗначениеРеквизита',
        VALUE = 'Значение',

        ID = 'Ид',
        NUMBER = 'Номер',
        NAME = 'Наименование',
        DESC = 'Описание',
        IMAGE = 'Картинка',

        PROPERTIES = 'Свойства',
        PROPERY = 'Свойство',
        VALUETYPE = 'ТипЗначений',
        ATTRIBUTESVARIANTS = 'ВариантыЗначений',
        FORPRODUCT = 'ДляТоваров',
        VALUEID = 'ИдЗначения',
        DIC = 'Справочник',

        PROPERTYVALUES = 'ЗначенияСвойств',
        PROPERTYVALUE = 'ЗначенияСвойства',

        OPERATION = 'ХозОперация',
        COMMERCIAL_INFO = 'КоммерческаяИнформация',
        DATE_CREATE = 'ДатаФормирования',
        CONTRAGENTS = 'Контрагенты',
        CONTRAGENT = 'Контрагент',
        NAMEFULL = 'ПолноеНаименование',
        FIRSTNAME = 'Имя',
        LASTNAME = 'Фамилия',
        ADDRESS = 'АдресРегистрации',
        ADDRESS_TITLE = 'Представление',
        ADDRESS_FIELD = 'АдресноеПоле',
        TYPE = 'Тип',
        DATE = 'Дата',
        TIME = 'Время',
        GOODS = 'Товары',
        GOOD = 'Товар',
        SUM = 'Сумма',
        RATE = 'Курс',

        MARK_REMOVAL = 'ПометкаУдаления',
        HELD = 'Проведен',
        PAYMENT_DATE = 'Дата оплаты по 1С',
        DATE_OF_SHIPMENT = 'Дата отгрузки по 1С';
}
