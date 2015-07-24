/**
 * Создание необходимых таблиц для фукционировани iMegaTeleport
 *
 * @package iMegaTeleport
 * @version 1.0
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
create table if not exists {$table_prefix}imegateleport (
  id bigint (20) unsigned not null auto_increment,
  object_id bigint(20)unsigned not null default'0',
  name varchar(50)default null,
  guid varchar(73)default null,
  primary key(id),unique key guid(guid,name),
  key name(name),
  key object_id(object_id)
)engine=innodb default charset=utf8;
