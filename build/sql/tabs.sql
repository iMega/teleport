/**
 * Создание рабочих таблиц
 *
 * @package iMegaTeleport
 * @version 1.1
 *
 * Copyright 2013 iMega ltd (email: info@imega.ru)
 *
 * This program is free software you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
drop table if exists {$table_prefix}imegaBuffer, {$table_prefix}imegaAttrs,
  {$table_prefix}imega_groups,{$table_prefix}imega_misc,
  {$table_prefix}imega_offers,{$table_prefix}imega_prop,
  {$table_prefix}imega_offers_features,
  {$table_prefix}imega_offers_prices,{$table_prefix}imega_prod;

create table {$table_prefix}imega_groups(
  id int(11)not null auto_increment,
  guid varchar(36),
  parent varchar(36),
  title varchar(100),
  slug varchar(200),
  primary key(id),
  unique key guid(guid),
  key parent(parent)
)engine=innodb default charset=utf8;

create table {$table_prefix}imega_misc(
  id int(11)not null auto_increment,
  type varchar(45),
  guid varchar(73),
  label varchar(36),
  val varchar(200),
  labelslug varchar(200),
  countattr int(11),
  valslug varchar(200),
  _visible smallint(1)default'0',
  primary key(id),
  key prod_guid(guid),
  key guid(label),
  key type(type)
)engine=innodb default charset=utf8;

create table {$table_prefix}imega_offers(
  id int(11)not null auto_increment,
  guid varchar(73),
  prod_guid varchar(36),
  barcode varchar(45),
  title varchar(100),
  base_unit varchar(45),
  base_unit_key varchar(45),
  base_unit_title varchar(100),
  base_unit_int varchar(45),
  amount double not null default'0',
  posttype varchar(20),
  primary key(id),
  unique key guid_unique(guid),
  key guid(guid),
  key prod_guid(prod_guid)
)engine=innodb default charset=utf8;

create table {$table_prefix}imega_offers_features(
  id int(11)not null auto_increment,
  offer_guid varchar(73),
  prodguid varchar(36),
  variantguid varchar(36),
  title varchar(100),
  val varchar(100),
  titleslug varchar(200),
  valslug varchar(200),
  primary key(id),
  key offer_guid(offer_guid)
)engine=innodb default charset=utf8;

create table {$table_prefix}imega_offers_prices(
  id int(11)not null auto_increment,
  offer_guid varchar(73),
  title varchar(100),
  price double(10,2),
  currency varchar(45),
  unit varchar(45),
  ratio int(11),
  type_guid varchar(36),
  primary key(id),
  key offer_guid(offer_guid),
  key type_guid(type_guid)
)engine=innodb default charset=utf8;

create table {$table_prefix}imega_prod(
  id int(11)not null auto_increment,
  title varchar(100),
  descr text,
  excerpt text,
  guid varchar(73),
  slug varchar(200),
  catalog_guid varchar(36),
  action char(6) default'',
  article varchar(45),
  img varchar(200),
  img_prop varchar(200),
  primary key(id),
  unique key guid(guid),
  key cat_guid(catalog_guid)
)engine=innodb default charset=utf8;

create table {$table_prefix}imega_prop(
  id int(11)not null auto_increment,
  guid varchar(73),
  title varchar(100),
  slug varchar(200),
  val_type varchar(45),
  parent_guid varchar(36),
  primary key(id),
  unique key guid(guid),
  key parentguid(parent_guid)
)engine=innodb default charset=utf8;
