/**
 * Логика для Woocommerce
 *
 * @package iMegaTeleport
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
set autocommit=1;

insert ignore {$table_prefix}options (option_name, option_value)
  value ('imegateleport_query', 0);
/*
 * Базовый путь
 */
set @baseurl = '{$baseurl}';
/*
 * Запрос 100 */
update {$table_prefix}options set option_value = 100
  where option_name = 'imegateleport_query';

/*
 * Путь для картинок
 */
set @imgpath = '{$imgpath}';

/*
 * Запрос 110 */
update {$table_prefix}options set option_value = 110
  where option_name = 'imegateleport_query';

/*
 * Увиличить переменную сервера
 */
set session group_concat_max_len = 1000000;

/*
 * Запрос 120 */
update {$table_prefix}options set option_value = 120
  where option_name = 'imegateleport_query';

/*
 * Создать простой и вариативный вид товара
 */
insert ignore {$table_prefix}terms (name, slug)
  values ('simple', 'simple'), ('variable', 'variable');

/*
 * Запрос 130 */
update {$table_prefix}options set option_value = 130
  where option_name = 'imegateleport_query';


insert ignore {$table_prefix}term_taxonomy (term_id, taxonomy, description)
  select term_id, 'product_type', ''
  from {$table_prefix}terms
    where slug = 'simple'or slug = 'variable';

/*
 * Запрос 140 */
update {$table_prefix}options set option_value = 140
  where option_name = 'imegateleport_query';

set sql_big_selects=1;

/*
 * Уникальный номер записи таксономии простого вида товара 
 */
set @prodSimple = (
  select term_taxonomy_id
    from {$table_prefix}term_taxonomy tt
    left join {$table_prefix}terms t on t.term_id = tt.term_id
    where slug = 'simple'
    limit 1);

/*
 * Запрос 150
 */
update {$table_prefix}options set option_value = 150
  where option_name = 'imegateleport_query';

/*
 * Уникальный номер записи таксономии простого вида товара 
 */
set @prodVariable = (
  select term_taxonomy_id
    from {$table_prefix}term_taxonomy tt
    left join {$table_prefix}terms t on t.term_id = tt.term_id
    where slug = 'variable'
    limit 1);
/*
 * Запрос 160
 */
update {$table_prefix}options set option_value = 160
  where option_name = 'imegateleport_query';


/*
 * Прогресс 55
 */
update {$table_prefix}options set option_value = 55
  where option_name = 'imegateleport_progress';

/*
 * Запрос 170
 */
update {$table_prefix}options set option_value = 170
  where option_name = 'imegateleport_query';

/*
 * Удалить статистику
 */
delete from {$table_prefix}options
  where option_name like 'imegateleport_stat%';
/*
 * Запрос 180
 */
update {$table_prefix}options set option_value = 180
  where option_name = 'imegateleport_query';

/*
 * Отметить дату выгрузки для статистики
 */
insert {$table_prefix}options (option_name, option_value)
  value ('imegateleport_stat_date', UNIX_TIMESTAMP (CURRENT_TIMESTAMP));
/*
 * Запрос 190
 */
update {$table_prefix}options set option_value = 190
  where option_name = 'imegateleport_query';

/*
 * Убираемся, если предыдущий запрос был закончен крахом
 */
drop table if exists {$table_prefix}imegaBuffer, {$table_prefix}imegaAttrs,
  {$table_prefix}compareGroups, {$table_prefix}compareGroupsUpdate,
  {$table_prefix}compareGroupsUnique, {$table_prefix}groupsUpdate;
/*
 * Запрос 200
 */
update {$table_prefix}options set option_value = 200
  where option_name = 'imegateleport_query';

/*
 * Создать временную таблицу, для объединения данных о вставке
 */
create table {$table_prefix}compareGroups
  select pt.id, pt.guid, pt.title, pt.slug, pt.parent
  from {$table_prefix}imega_groups pt
  left join {$table_prefix}imegateleport p on p.guid = pt.guid
  where p.guid is null;
/*
 * Запрос 210
 */
update {$table_prefix}options set option_value = 210
  where option_name = 'imegateleport_query';

/*
 * Статистика количество новых групп
 */
insert {$table_prefix}options (option_name, option_value)
  select 'imegateleport_stat_groups', count(id)
  from {$table_prefix}compareGroups;
/*
 * Запрос 220
 */
update {$table_prefix}options set option_value = 220
  where option_name = 'imegateleport_query';

/*
 * Заполняем временную таблицу, для объединения данных об обновлении
 */
create table {$table_prefix}compareGroupsUpdate
  select pt.id, pt.guid, p.title, p.slug, p.parent
  from {$table_prefix}imegateleport pt
  join {$table_prefix}imega_groups p on p.guid = pt.guid group by guid;
/*
 * Запрос 230
 */
update {$table_prefix}options set option_value = 230
  where option_name = 'imegateleport_query';

/*
 * Статистика количество групп для замены
 */
insert {$table_prefix}options (option_name, option_value)
  select 'imegateleport_stat_groups_replace', count(id)
  from {$table_prefix}compareGroupsUpdate;
/*
 * Запрос 240
 */
update {$table_prefix}options set option_value = 240
  where option_name = 'imegateleport_query';

/*
 * Создаем и заполняем таблицу новыми УНикальными слагами
 */
create table {$table_prefix}compareGroupsUnique
  select u.id, u.guid, u.title, u.slug, u.parent
  from (
    select cg.id, cg.guid, cg.title, cg.slug, cg.parent
      from {$table_prefix}compareGroups cg
      left join {$table_prefix}terms t on t.slug = cg.slug
      where t.slug is null
    ) u
  group by u.slug;
/*
 * Запрос 250
 */
update {$table_prefix}options set option_value = 250
  where option_name = 'imegateleport_query';

/*
 * Список новых УНикальных слагов
 */
insert {$table_prefix}terms (name, slug)
  select title, slug from {$table_prefix}compareGroupsUnique;
/*
 * Запрос 260
 */
update {$table_prefix}options set option_value = 260
  where option_name = 'imegateleport_query';

/*
 * Список новых УНикальных слагов в imegateleport
 */
insert {$table_prefix}imegateleport (object_id, name, guid)
  select t.term_id, 'terms', cgu.guid
  from {$table_prefix}compareGroupsUnique cgu
  left join {$table_prefix}terms t on t.slug = cgu.slug;
/*
 * Запрос 270
 */
update {$table_prefix}options set option_value = 270
  where option_name = 'imegateleport_query';

/*
 * Уникальный номер последней записи в terms
 */
set @maxID = (select CAST( ifnull( max(term_id), 1) as char)
              from {$table_prefix}terms);
/*
 * Запрос 280
 */
update {$table_prefix}options set option_value = 280
  where option_name = 'imegateleport_query';

/*
 * Список новых НЕуникальных слагов
 */
insert {$table_prefix}terms (name, slug)
  select cg.title, concat(cg.slug, (cg.id+@maxID)) slug
  from {$table_prefix}compareGroups cg
  left join {$table_prefix}compareGroupsUnique cu on cu.guid = cg.guid
  where cu.guid is null;
/*
 * Запрос 290
 */
update {$table_prefix}options set option_value = 290
  where option_name = 'imegateleport_query';

/*
 * Список новых НЕуникальных слагов в imegateleport
 */
insert {$table_prefix}imegateleport (object_id, name, guid)
  select t.term_id, 'terms', cg.guid
  from {$table_prefix}compareGroups cg
  left join {$table_prefix}compareGroupsUnique cu on cu.guid = cg.guid
  left join {$table_prefix}terms t on t.slug = concat(cg.slug, (cg.id+@maxID))
  where cu.guid is null;
/*
 * Запрос 300
 */
update {$table_prefix}options set option_value = 300
  where option_name = 'imegateleport_query';

/*
 * Записать связи между term и term_taxonomy
 */
insert ignore {$table_prefix}term_taxonomy (term_id, taxonomy, description, parent)
  select t.term_id, 'product_cat', '', ifnull(parent.term_id, 0) parent
    from {$table_prefix}compareGroups cg
    left join {$table_prefix}compareGroupsUnique cu on cu.guid = cg.guid
    left join {$table_prefix}terms t on cg.slug = t.slug
    left join {$table_prefix}compareGroups cuparent on cg.parent = cuparent.guid
    left join {$table_prefix}terms parent on cuparent.slug = parent.slug
    where cu.guid is not null
  union
  select t.term_id, 'product_cat', '', ifnull(parent.term_id, 0) parent
    from {$table_prefix}compareGroups cg
    left join {$table_prefix}compareGroupsUnique cu on cu.guid = cg.guid
    left join {$table_prefix}terms t on concat(cg.slug, (cg.id+@maxID)) = t.slug
    left join {$table_prefix}compareGroups cuparent on cg.parent = cuparent.guid
    left join {$table_prefix}terms parent on cuparent.slug = parent.slug
    where cu.guid is null;
/*
 * Запрос 310
 */
update {$table_prefix}options set option_value = 310
  where option_name = 'imegateleport_query';

/*
 * Список новых слагов записываем в imegateleport
 */
insert {$table_prefix}imegateleport (object_id, name, guid)
  select tt.term_taxonomy_id, 'term_taxonomy', cg.guid
    from {$table_prefix}compareGroups cg
    left join {$table_prefix}compareGroupsUnique cu on cu.guid = cg.guid
    left join {$table_prefix}terms t on cg.slug = t.slug
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id
    where cu.guid is not null
  union
  select tt.term_taxonomy_id, 'term_taxonomy', cg.guid
    from {$table_prefix}compareGroups cg
    left join {$table_prefix}compareGroupsUnique cu on cu.guid = cg.guid
    left join {$table_prefix}terms t on concat(cg.slug, (cg.id+@maxID)) = t.slug
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id
    where cu.guid is null;
/*
 * Запрос 320
 */
update {$table_prefix}options set option_value = 320
  where option_name = 'imegateleport_query';

/*
 * Создаем таблицу и собираем изменения для обновления данных
 */
create table {$table_prefix}groupsUpdate
  select tt.term_id, cgu.title, nullif(cgu.slug, t.slug) slug, tt.term_taxonomy_id, tt.taxonomy, parent_tt.term_id parent
    from {$table_prefix}compareGroupsUpdate cgu
    left join {$table_prefix}imegateleport tp on tp.guid = cgu.guid
    left join {$table_prefix}term_taxonomy tt on tt.term_taxonomy_id = tp.object_id
    left join {$table_prefix}terms t on t.term_id = tt.term_id and t.slug = cgu.slug
    left join {$table_prefix}imegateleport parent_tp on parent_tp.guid = cgu.parent
    left join {$table_prefix}term_taxonomy parent_tt on parent_tt.term_taxonomy_id = parent_tp.object_id and parent_tt.term_id<> tt.parent
    where t.slug is null or parent_tt.term_taxonomy_id is not null;
/*
 * Запрос 330
 */
update {$table_prefix}options set option_value = 330
  where option_name = 'imegateleport_query';

/*
 * Записываем изменения terms
 */
replace {$table_prefix}terms (term_id, name, slug)
  select term_id, title, slug from {$table_prefix}groupsUpdate
  where parent is null;
/*
 * Запрос 340
 */
update {$table_prefix}options set option_value = 340
  where option_name = 'imegateleport_query';

/*
 * Записываем изменения Родителей групп
 * ver 2
 * - replace {$table_prefix}term_taxonomy (term_taxonomy_id, term_id, taxonomy, parent)
 * + replace {$table_prefix}term_taxonomy (term_taxonomy_id, term_id, taxonomy, parent, description)
 * - select term_taxonomy_id, term_id, taxonomy, parent
 * + select term_taxonomy_id, term_id, taxonomy, parent, ''
 */
replace {$table_prefix}term_taxonomy (term_taxonomy_id, term_id, taxonomy, parent, description)
  select term_taxonomy_id, term_id, taxonomy, parent, ''
  from {$table_prefix}groupsUpdate
  where slug is null;
/*
 * Запрос 350
 */
update {$table_prefix}options set option_value = 350
  where option_name = 'imegateleport_query';

/*
 * Убираемся за собой
 */
drop table if exists {$table_prefix}compareGroups,
  {$table_prefix}compareGroupsUpdate, {$table_prefix}compareGroupsUnique,
  {$table_prefix}groupsUpdate;
/*
 * Запрос 360
 */
update {$table_prefix}options set option_value = 360
  where option_name = 'imegateleport_query';

/*
 * ПОСТИМ ТОВАР
 */
/*
 * Прогресс 65
 */
update {$table_prefix}options set option_value = 65
  where option_name = 'imegateleport_progress';
/*
 * Запрос 370
 */
update {$table_prefix}options set option_value = 370
  where option_name = 'imegateleport_query';

/*
 * Убираемся, если предыдущий запрос был закончен крахом
 */
drop table if exists {$table_prefix}compareGoods, {$table_prefix}compareGoodsUnique;
/*
 * Запрос 380
 */
update {$table_prefix}options set option_value = 380
  where option_name = 'imegateleport_query';

/*
 * Создать и заполнить таблицу  для объединения данных о вставке и обновлении
 */
create table {$table_prefix}compareGoods
  select c.id, c.guid, c.post_title, c.post_content, c.post_excerpt, c.post_name, c.catalogGuid, c.img, c.img_prop, c.action, m.label
    from (
      select pr.id, pr.guid, title post_title, descr post_content, excerpt post_excerpt, pr.slug post_name, catalog_guid catalogGuid, pr.img, pr.img_prop, 'insert'action
        from {$table_prefix}imega_prod pr
        left join {$table_prefix}imegateleport p on p.guid = pr.guid
        where p.guid is null
      union
      select pr.id, pr.guid, title post_title, descr post_content, excerpt post_excerpt, pr.slug post_name, catalog_guid catalogGuid, pr.img, pr.img_prop, 'update'action
        from {$table_prefix}imegateleport p
        join {$table_prefix}imega_prod pr on p.guid = pr.guid
        where p.name = 'posts'
    ) c
    left join {$table_prefix}imega_misc m on m.guid = c.guid
    where m.type = 'group';
/*
 * Запрос 390
 */
update {$table_prefix}options set option_value = 390
  where option_name = 'imegateleport_query';

/*
 * Статистика количество нового товара
 */
insert {$table_prefix}options (option_name, option_value)
  select 'imegateleport_stat_goods', count(id)
  from {$table_prefix}compareGoods
  where action = 'insert';
/*
 * Запрос 400
 */
update {$table_prefix}options set option_value = 400
  where option_name = 'imegateleport_query';

/*
 * Статистика количество измененного товара
 */
insert {$table_prefix}options (option_name, option_value)
  select 'imegateleport_stat_goods_replace', count(id)
  from {$table_prefix}compareGoods
  where action = 'update';
/*
 * Запрос 410
 */
update {$table_prefix}options set option_value = 410
  where option_name = 'imegateleport_query';

/*
 * Создать и заполнить таблицу новыми УНикальными слагами товаров
 */
create table {$table_prefix}compareGoodsUnique
  select cg.id, cg.guid, cg.post_title, cg.post_content, cg.post_excerpt, cg.post_name, cg.catalogGuid, cg.img, cg.img_prop, cg.action, cg.label
    from (
      select * from {$table_prefix}compareGoods group by post_name
    ) cg
    left join {$table_prefix}posts p on p.post_name = cg.post_name;
/*
 * where p.post_name is null;
 */
/*
 * Запрос 420
 */
update {$table_prefix}options set option_value = 420
  where option_name = 'imegateleport_query';

/*
 * Заполняем новыми уникальными товарами
 */
insert {$table_prefix}posts (post_date, post_date_gmt, post_content, post_excerpt, post_title, post_name, post_type, to_ping, pinged, post_content_filtered)
  select CURRENT_TIMESTAMP, UTC_TIMESTAMP, post_content, post_excerpt, post_title, post_name, 'product', '', '', ''
  from {$table_prefix}compareGoodsUnique
  where action = 'insert';

/*
 * Запрос 430
 */
update {$table_prefix}options set option_value = 430
  where option_name = 'imegateleport_query';

/*
 * Список новых постов записываем в imegateleport
 * todo дубликат 500
 */
insert {$table_prefix}imegateleport (object_id, name, guid)
  select p.id object_id, 'posts' name, cu.guid guid
    from {$table_prefix}compareGoodsUnique cu
    left join {$table_prefix}posts p on p.post_name = cu.post_name
    where cu.action = 'insert' and p.post_type='product';

/*
 * Запрос 440
 */
update {$table_prefix}options set option_value = 440
  where option_name = 'imegateleport_query';

/*
 * Обновление информации об уникальном товаре
 */
replace {$table_prefix}posts (id, post_content, post_excerpt, post_title, post_name, post_modified, post_modified_gmt, post_type, to_ping, pinged, post_content_filtered)
  select p.object_id, post_content, post_excerpt, post_title, post_name, CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'product', '', '', ''
    from {$table_prefix}compareGoodsUnique cgu
    left join {$table_prefix}imegateleport p on p.guid = cgu.guid
    where action = 'update' and p.name = 'posts';

/*
 * Запрос 450
 */
update {$table_prefix}options set option_value = 450
  where option_name = 'imegateleport_query';

/*
 * Удалить все что связано с товарами для обновления
 */
delete from {$table_prefix}postmeta where post_id in (
  select p.object_id from {$table_prefix}compareGoodsUnique cgu
    left join {$table_prefix}imegateleport p on p.guid = cgu.guid
    where action = 'update');

/*
 * Запрос 460
 */
update {$table_prefix}options set option_value = 460
  where option_name = 'imegateleport_query';

/*
 * Удалить связи уникального обновляемого товара
 */
delete from {$table_prefix}term_relationships where object_id in (
  select p.object_id from {$table_prefix}compareGoodsUnique cgu
    left join {$table_prefix}imegateleport p on p.guid = cgu.guid
    where action = 'update');

/*
 * Запрос 470
 */
update {$table_prefix}options set option_value = 470
  where option_name = 'imegateleport_query';

/*
 * Удалить картинки связанные с товарами для обновления
 */
delete from {$table_prefix}posts where post_parent in (
  select p.object_id from {$table_prefix}compareGoodsUnique cgu
    left join {$table_prefix}imegateleport p on p.guid = cgu.guid
    where action = 'update');

/*
 * Запрос 480
 */
update {$table_prefix}options set option_value = 480
  where option_name = 'imegateleport_query';

/*
 * Записываем картинку уникальными товарам
 * ver 2
 * - insert {$table_prefix}posts (post_date, post_date_gmt, post_status, post_parent, guid, post_type, post_mime_type, post_excerpt, to_ping, pinged, post_content_filtered)
 * + insert {$table_prefix}posts (post_date, post_date_gmt, post_status, post_parent, guid, post_type, post_mime_type, post_excerpt, to_ping, pinged, post_content_filtered, post_content, post_title)
 * -select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'inherit', p.id, concat(@baseurl, @imgpath, pr.img), 'attachment', 'image/jpeg', '', '', '', ''
 * + select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'inherit', p.id, concat(@baseurl, @imgpath, pr.img), 'attachment', 'image/jpeg', '', '', '', '', '', ''
 */
insert {$table_prefix}posts (post_date, post_date_gmt, post_status, post_parent, guid, post_type, post_mime_type, post_excerpt, to_ping, pinged, post_content_filtered, post_content, post_title)
  select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'inherit', p.id, concat(@baseurl, @imgpath, pr.img), 'attachment', 'image/jpeg', '', '', '', '', '', ''
    from {$table_prefix}compareGoodsUnique cu
    left join {$table_prefix}imega_prod pr on pr.guid = cu.guid
    left join {$table_prefix}posts p on p.post_name = cu.post_name
    where pr.img is not null and pr.img<>'';
/*
 * @todo {$table_prefix}imegateleport
 */
/*
 * Запрос 490
 */
update {$table_prefix}options set option_value = 490
  where option_name = 'imegateleport_query';

/*
 * Список новых постов записываем в imegateleport
 */
insert ignore {$table_prefix}imegateleport (object_id, name, guid)
  select p.id object_id, 'posts' name, cu.guid guid
    from {$table_prefix}compareGoodsUnique cu
    left join {$table_prefix}posts p on p.post_name = cu.post_name
    where cu.action = 'insert' and p.post_type='product';
/*
 * Запрос 500
 */
update {$table_prefix}options set option_value = 500
  where option_name = 'imegateleport_query';

/*
 * Записать post_meta о картинках уникальными товарами
 */
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select img.id, '_{$table_prefix}attached_file', concat(@imgpath, cgu.img)
    from {$table_prefix}posts p
    left join {$table_prefix}compareGoodsUnique cgu on cgu.post_name = p.post_name
    left join {$table_prefix}posts img on img.guid = concat(@baseurl, @imgpath, cgu.img)
    where p.post_type<>'attachment' and cgu.img<>''
  union
  select img.id, '_{$table_prefix}attachment_metadata', cgu.img_prop
    from {$table_prefix}posts p
    left join {$table_prefix}compareGoodsUnique cgu on cgu.post_name = p.post_name
    left join {$table_prefix}posts img on img.guid = concat(@baseurl, @imgpath, cgu.img)
    where p.post_type<>'attachment' and cgu.img_prop<>''
  union
  select p.id, '_thumbnail_id', img.id
    from {$table_prefix}posts p
    left join {$table_prefix}compareGoodsUnique cgu on cgu.post_name = p.post_name
    left join {$table_prefix}posts img on img.guid = concat(@baseurl, @imgpath, cgu.img)
    where p.post_type<>'attachment' and cgu.img_prop<>'';
/*
 * @todo {$table_prefix}imegateleport
 */
/*
 * Запрос 510
 */
update {$table_prefix}options set option_value = 510
  where option_name = 'imegateleport_query';

/*
 * Заполняем связи новых уникальных товаров с группамми
 * ver 3
 * - where p.post_type<>'attachment'and tp.name = 'terms'and tp.object_id is not null;
 * + where p.post_type<>'attachment'and tp.name = 'term_taxonomy'and tp.object_id is not null;
 * ver 2
 * + and tp.name='terms'
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select p.id, tp.object_id from {$table_prefix}posts p
    left join {$table_prefix}compareGoodsUnique cu on cu.post_name = p.post_name
    left join {$table_prefix}imegateleport tp on tp.guid = cu.label
    where p.post_type<>'attachment'and tp.name = 'term_taxonomy'and tp.object_id is not null;

/*
 * Запрос 520
 */
update {$table_prefix}options set option_value = 520
  where option_name = 'imegateleport_query';

/*
 * Удаляем из compareGoods содержимое compareGoodsUnique
 */
delete from {$table_prefix}compareGoods where guid in (
  select guid from {$table_prefix}compareGoodsUnique);
/*
 * Запрос 530
 */
update {$table_prefix}options set option_value = 530
  where option_name = 'imegateleport_query';

/*
 * Удаляем таблицу уникальных товаров
 */
drop table if exists {$table_prefix}compareGoodsUnique;
/*
 * Запрос 540
 */
update {$table_prefix}options set option_value = 540
  where option_name = 'imegateleport_query';

/*
 * Уникальный номер последней записи в posts
 */
set @maxID = (select CAST( ifnull( max(id), 1) as char) from {$table_prefix}posts);
/*
 * Запрос 550
 */
update {$table_prefix}options set option_value = 550
  where option_name = 'imegateleport_query';

/*
 * Прогресс 70
 */
update {$table_prefix}options set option_value = 70
  where option_name = 'imegateleport_progress';
/*
 * Запрос 560
 */
update {$table_prefix}options set option_value = 560
  where option_name = 'imegateleport_query';

/*
 * Обновление информации о НЕуникальном товаре
 * ver 2
 * + left join {$table_prefix}posts p on p.id = tp.object_id and p.post_name = cg.post_name
 * - where cg.action = 'update';
 * + where cg.action = 'update' and p.post_name is null;
 */
replace {$table_prefix}posts (id, post_content, post_excerpt, post_title, post_name, post_modified, post_modified_gmt, post_type, to_ping, pinged, post_content_filtered)
  select tp.object_id, cg.post_content, cg.post_excerpt, cg.post_title, concat(cg.post_name, (cg.id+@maxID)), CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'product', '', '', ''from {$table_prefix}compareGoods cg
    left join {$table_prefix}imegateleport tp on tp.guid = cg.guid
    left join {$table_prefix}posts p on p.id = tp.object_id and p.post_name = cg.post_name
    where cg.action = 'update'and p.post_name is null;
/*
 * Запрос 570
 */
update {$table_prefix}options set option_value = 570
  where option_name = 'imegateleport_query';

/*
 * Создать кэш для удаления
 */
drop table if exists {$table_prefix}imega_temp;
create table {$table_prefix}imega_temp
  select p.object_id from {$table_prefix}compareGoods cg
    left join {$table_prefix}imegateleport p on p.guid = cg.guid
    where cg.action = 'update' group by p.object_id;
/*
 * Удалить все что связано с товарами для обновления
 */
delete from {$table_prefix}postmeta
  where post_id in (select object_id from {$table_prefix}imega_temp);

/*
 * Запрос 580
 */
update {$table_prefix}options set option_value = 580
  where option_name = 'imegateleport_query';

delete from {$table_prefix}term_relationships
  where object_id in (select object_id from {$table_prefix}imega_temp);
/*
 * Запрос 590
 */
update {$table_prefix}options set option_value = 590
  where option_name = 'imegateleport_query';

/*
 * Удалить картинки связанные с товарами для обновления
 */
delete from {$table_prefix}posts
  where post_parent in (select object_id from {$table_prefix}imega_temp);

drop table if exists {$table_prefix}imega_temp, {$table_prefix}compareGoods_temp;
/*
 * Запрос 600
 */
update {$table_prefix}options set option_value = 600
  where option_name = 'imegateleport_query';

/*
 * Временная таблца НЕуникальных товаров
 */
create table {$table_prefix}compareGoods_temp
  select CURRENT_TIMESTAMP post_date, UTC_TIMESTAMP post_date_gmt, cg.post_content post_content, cg.post_title post_title, concat(cg.post_name, (cg.id+@maxID)) post_name, 'product' post_type, '' post_excerpt, '' to_ping, '' pinged, '' post_content_filtered, cg.guid guid
  from {$table_prefix}compareGoods cg
  where cg.action = 'insert';

/*
 * Запрос 605
 */
update {$table_prefix}options set option_value = 605
  where option_name = 'imegateleport_query';

/*
 * Заполняем новыми НЕуникальными товарам
 */
insert {$table_prefix}posts (post_date, post_date_gmt, post_content, post_excerpt, post_title, post_name, post_type, to_ping, pinged, post_content_filtered)
  select post_date, post_date_gmt, post_content, post_excerpt, post_title, post_name, post_type,  to_ping, pinged, post_content_filtered
  from {$table_prefix}compareGoods_temp;
/*
 * Запрос 610
 */
update {$table_prefix}options set option_value = 610
  where option_name = 'imegateleport_query';
/*
 * Список новых НЕуникальными постов записываем в таблицу с гуи
 */
insert {$table_prefix}imegateleport (object_id, name, guid)
  select p.id, 'posts', cgt.guid from {$table_prefix}posts p
    left join {$table_prefix}compareGoods_temp cgt on cgt.post_name = p.post_name
    where cgt.guid is not null;

/*
 * Удаляем временную таблицу НЕуникальных товаров
 */
drop table if exists {$table_prefix}compareGoods_temp;

/*
 * Запрос 615
 */
update {$table_prefix}options set option_value = 615
  where option_name = 'imegateleport_query';

/*
 * Записываем картинку уникальными товарами
 * ver 2
 * - insert {$table_prefix}posts (post_date, post_date_gmt, post_status, post_parent, guid, post_type, post_mime_type, post_excerpt, to_ping, pinged, post_content_filtered)
  * + insert {$table_prefix}posts (post_date, post_date_gmt, post_status, post_parent, guid, post_type, post_mime_type, post_excerpt, to_ping, pinged, post_content_filtered, post_content, post_title)
 * - select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'inherit', p.id, concat(@baseurl, @imgpath, pr.img), 'attachment', 'image/jpeg', '', '', '', ''
 * +select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'inherit', p.id, concat(@baseurl, @imgpath, pr.img), 'attachment', 'image/jpeg', '', '', '', '', '', ''
 */
insert {$table_prefix}posts (post_date, post_date_gmt, post_status, post_parent, guid, post_type, post_mime_type, post_excerpt, to_ping, pinged, post_content_filtered, post_content, post_title)
  select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'inherit', p.id, concat(@baseurl, @imgpath, pr.img), 'attachment', 'image/jpeg', '', '', '', '', '', ''
    from {$table_prefix}compareGoods cg
    left join {$table_prefix}imega_prod pr on pr.guid = cg.guid
    left join {$table_prefix}posts p on p.post_name = concat(cg.post_name, (cg.id+@maxID))
    where pr.img is not null and pr.img<>'';
/*
 * Запрос 620
 */
update {$table_prefix}options set option_value = 620
  where option_name = 'imegateleport_query';

/*
 * Записать post_meta о картинках уникальными товарами
 */
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select p.id, '_{$table_prefix}attached_file', concat(@imgpath, cg.img)
    from {$table_prefix}posts p
    left join {$table_prefix}compareGoods cg on concat(cg.post_name, (cg.id+@maxID)) = p.post_name
    left join {$table_prefix}posts img on img.guid = concat(@baseurl, @imgpath, cg.img)
    where p.post_type<>'attachment'and cg.img<>''
  union
  select p.id, '_{$table_prefix}attachment_metadata', cg.img_prop
    from {$table_prefix}posts p
    left join {$table_prefix}compareGoods cg on concat(cg.post_name, (cg.id+@maxID)) = p.post_name
    left join {$table_prefix}posts img on img.guid = concat(@baseurl, @imgpath, cg.img)
    where p.post_type<>'attachment'and cg.img_prop<>''
  union
  select p.id, '_thumbnail_id', img.id
    from {$table_prefix}posts p
    left join {$table_prefix}compareGoods cg on concat(cg.post_name, (cg.id+@maxID)) = p.post_name
    left join {$table_prefix}posts img on img.guid = concat(@baseurl, @imgpath, cg.img)
    where p.post_type<>'attachment'and cg.img_prop<>'';
/*
 * Запрос 630
 */
update {$table_prefix}options set option_value = 630
  where option_name = 'imegateleport_query';

/*
 * Заполняем связи новых НЕуникальных товаров с группамми
 * ver 3
 * - where tp.object_id is not null and tp.name = 'terms';
 * + where tp.object_id is not null and tp.name = 'term_taxonomy';
 * ver 2
 * - where tp.object_id is not null;
 * + where tp.object_id is not null and tp.name='terms';aa
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select p.id, tp.object_id from {$table_prefix}posts p
    left join {$table_prefix}compareGoods cg on concat(cg.post_name, (cg.id+@maxID)) = p.post_name
    left join {$table_prefix}imegateleport tp on tp.guid = cg.label
    where tp.object_id is not null and tp.name = 'term_taxonomy';
/*
 * Запрос 640
 */
update {$table_prefix}options set option_value = 640
  where option_name = 'imegateleport_query';

/*
 * Заполняем связи НЕновых НЕуникальных товаров с группамми
 * ver 3
 * - where cg.action = 'update'and tpg.object_id is not null and tpg.name = 'terms';
 * + where cg.action = 'update'and tpg.object_id is not null and tpg.name = 'term_taxonomy';
 * ver 2
 * - where cg.action = 'update' and tpg.object_id is not null;
 * + where cg.action = 'update' and tpg.object_id is not null and tpg.name = 'terms';
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select tp.object_id, tpg.object_id from {$table_prefix}compareGoods cg
    left join {$table_prefix}imegateleport tp on tp.guid = cg.guid
    left join {$table_prefix}posts p on p.id = tp.object_id and p.post_name = cg.post_name
    left join {$table_prefix}imegateleport tpg on tpg.guid = cg.label
    where cg.action = 'update'and tpg.object_id is not null and tpg.name = 'term_taxonomy';
/*
 * Запрос 642
 */
update {$table_prefix}options set option_value = 642
  where option_name = 'imegateleport_query';

/*
 * Убираемся, если предыдущий запрос был закончен крахом
 */
drop table if exists {$table_prefix}compareGoods, {$table_prefix}imegaBuffer,
  {$table_prefix}imegaAttrs;
/*
 * Запрос 646
 */
update {$table_prefix}options set option_value = 646
  where option_name = 'imegateleport_query';

/*
 * Буфер
 */
create table {$table_prefix}imegaBuffer(
  id int,
  guid varchar(38),
  name varchar(200),
  val varchar(200),
  uni char(6),
  attr varchar(200),
  key guid (guid),
  key val (val),
  key uni (uni)
)engine=innodb default charset=utf8;
/*
 * Запрос 650
 */
update {$table_prefix}options set option_value = 650
  where option_name = 'imegateleport_query';

/*
 * Буфер слагов чтобы не потерять
 * ver 2
 * - select cg.guid, cg.val, cg.valSlug, 'unique', cg.labelSlug from
 * + select m.guid, m.val, m.valSlug, 'unique', m.labelSlug from {$table_prefix}imega_misc
 * - (select * from {$table_prefix}imega_misc where type='attr') as cg
 * - left join {$table_prefix}terms as t on t.slug = cg.valSlug
 * + left join {$table_prefix}terms as t on t.slug = m.valSlug
 * - where t.slug is null and cg.label<>'ОписаниеФайла' group by cg.valSlug;
 * + where type='attr' and t.slug is null and m.label<>'ОписаниеФайла' group by m.labelSlug, m.valSlug;
 */
insert {$table_prefix}imegaBuffer (guid, name, val, uni, attr)
  select m.guid, m.val, m.valSlug, 'unique', m.labelSlug
    from {$table_prefix}imega_misc m
    left join {$table_prefix}terms t on t.slug = m.valSlug
    where type = 'attr'and t.slug is null and m.label<>'ОписаниеФайла'
    group by m.valSlug;
/*
 * Запрос 660
 */
update {$table_prefix}options set option_value = 660
  where option_name = 'imegateleport_query';

/*
 * Уникальный номер последней записи в terms
 */
set @maxID = (select CAST( ifnull( max(term_id), 1) as char) from {$table_prefix}terms);
/*
 * Запрос 670
 */
update {$table_prefix}options set option_value = 670
  where option_name = 'imegateleport_query';

/*
 * Буфер слагов c Уникальными индексами
 * ver 4
 * + left join {$table_prefix}term_taxonomy as tt on tt.taxonomy = concat('pa_', m.labelSlug)
 * + and tt.term_taxonomy_id is null
 * ver 3
 * + group by m.valSlug
 * ver 2
 * - select cg.id, cg.guid, cg.val, concat(cg.valSlug, (cg.id+@maxID)) as slug, '', cg.labelSlug
 * + select m.id, m.guid, m.val, concat(m.valSlug, (m.id+@maxID)) as slug, '', m.labelSlug
 * - (select * from {$table_prefix}imega_misc where type='attr') as cg
 * + {$table_prefix}imega_misc m
 * + type='attr' and
 * - group by m.valSlug
 */
insert {$table_prefix}imegaBuffer (id, guid, name, val, uni, attr)
  select m.id, m.guid, m.val, concat(m.valSlug, (m.id+@maxID)) slug, '', m.labelSlug
    from {$table_prefix}imega_misc m
    left join {$table_prefix}terms t on t.slug = m.valSlug
    left join {$table_prefix}imegaBuffer b on b.val = m.valSlug and b.attr = m.labelSlug
    left join {$table_prefix}term_taxonomy tt on tt.taxonomy = concat('pa_', m.labelSlug)
    where type = 'attr'and b.val is null and m.label<>'ОписаниеФайла'and tt.term_taxonomy_id is null
    group by m.valSlug;
/*
 * Запрос 680
 */
update {$table_prefix}options set option_value = 680
  where option_name = 'imegateleport_query';

/*
 * Заполняем новыми НЕуникальными атррибутами товаров
 */
insert {$table_prefix}terms (name, slug)
  select name, val from {$table_prefix}imegaBuffer;
/*
 * Запрос 690
 */
update {$table_prefix}options set option_value = 690
  where option_name = 'imegateleport_query';

/*
 * Записать в imegateleport
 */
insert {$table_prefix}imegateleport (object_id, name)
  select term_id, ('terms')
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val;
/*
 * Запрос 700
 */
update {$table_prefix}options set option_value = 700
  where option_name = 'imegateleport_query';

/*
 * Заполнить terms_taxonomy новыми НЕуникальными атррибутами товаров
 */
insert ignore {$table_prefix}term_taxonomy (term_id, taxonomy, description)
  select t.term_id, concat('pa_', b.attr), ''
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val;
/*
 * Запрос 710
 */
update {$table_prefix}options set option_value = 710
  where option_name = 'imegateleport_query';

/*
 * Записать в imegateleport
 */
insert {$table_prefix}imegateleport (object_id, name)
  select tt.term_taxonomy_id, ('term_taxonomy')
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id;
/*
 * Запрос 720
 */
update {$table_prefix}options set option_value = 720
  where option_name = 'imegateleport_query';

/*
 * Записать аттрибуты товара общие в woocommerce_attribute_taxonomies
 * ver 2
 * + left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = b.attr
 * + where wat.attribute_name is null
 */
insert ignore {$table_prefix}woocommerce_attribute_taxonomies (attribute_name, attribute_label, attribute_type, attribute_orderby)
  select b.attr, m.label, 'text', 'name' from {$table_prefix}imegaBuffer b
    left join {$table_prefix}imega_misc m on m.labelSlug = b.attr
    left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = b.attr
    where wat.attribute_name is null
    group by b.attr;
/*
 * Запрос 725
 */
update {$table_prefix}options set option_value = 725
  where option_name = 'imegateleport_query';

/*
 * Записать в imegateleport
 */
insert {$table_prefix}imegateleport (object_id, name)
  select wat.attribute_id, 'woocommerce_attribute_taxonomies'
    from {$table_prefix}woocommerce_attribute_taxonomies wat
    left join {$table_prefix}imegaBuffer b on b.attr = wat.attribute_name
    group by b.attr;
/*
 * Запрос 730
 */
update {$table_prefix}options set option_value = 730
  where option_name = 'imegateleport_query';

/*
 * Записать связи товаров с аттрибутами
 * + and tt.term_taxonomy_id is not null
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select tp.object_id, tt.term_taxonomy_id
    from {$table_prefix}imega_misc m
    left join {$table_prefix}imegateleport tp on tp.guid = m.guid
    left join {$table_prefix}imegaBuffer b on b.name = m.val and b.attr = m.labelSlug
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id
    where tp.id is not null and tt.term_taxonomy_id is not null and m.type = 'attr'and m.label<>'ОписаниеФайла';
/*
 * Запрос 740
 */
update {$table_prefix}options set option_value = 740
  where option_name = 'imegateleport_query';

/*
 * Ищем схожие атрибуты других товаров, дабы не плодить лишних записей
 * ver 2
 * + and tp.object_id is not null
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select tp.object_id, tt.term_taxonomy_id
    from {$table_prefix}imega_misc m
    left join {$table_prefix}imegateleport tp on tp.guid = m.guid
    left join {$table_prefix}term_taxonomy tt on tt.taxonomy = concat('pa_', m.labelSlug)
    left join {$table_prefix}terms t on t.term_id = tt.term_id
    where type = 'attr'and t.name = m.val and m.label<>'ОписаниеФайла'
      and tp.object_id is not null;
/*
 * Запрос 750
 */
update {$table_prefix}options set option_value = 750
  where option_name = 'imegateleport_query';

/*
 * Очистить буфер
 */
truncate table {$table_prefix}imegaBuffer;
/*
 * Запрос 760
 */
update {$table_prefix}options set option_value = 760
  where option_name = 'imegateleport_query';

/*
 * Уникальный номер последней записи в terms
 */
set @maxID = (select CAST( ifnull( max(term_id), 1) as char) from {$table_prefix}terms);
/*
 * Запрос 770
 */
update {$table_prefix}options set option_value = 770
  where option_name = 'imegateleport_query';

/*
 * Закидываем в буффер уникальные свойства товара
 */
insert {$table_prefix}imegaBuffer (guid, name, val, uni)
  select p.guid, p.title, p.slug, 'unique'from {$table_prefix}imega_prop p
    left join {$table_prefix}terms t on t.slug = p.slug
    where t.slug is null and p.val_type is null group by p.slug;
/*
 * Запрос 780
 */
update {$table_prefix}options set option_value = 780
  where option_name = 'imegateleport_query';

/*
 * Закидываем в буффер НЕуникальные свойства товара
 */
insert {$table_prefix}imegaBuffer (guid, name, val)
  select p.guid, p.title, concat(p.slug, (@maxID+p.id))
    from {$table_prefix}imega_prop p
    left join {$table_prefix}terms t on t.slug = p.slug
    left join {$table_prefix}imegateleport tp on tp.guid = p.guid
    left join {$table_prefix}imegaBuffer b on b.guid = p.guid
    where p.val_type is null and b.guid is null;
/*
 * Запрос 790
 */
update {$table_prefix}options set option_value = 790
  where option_name = 'imegateleport_query';

/*
 * Уникальные свойства товара
 */
insert {$table_prefix}terms (name, slug)
  select name, val from {$table_prefix}imegaBuffer;
/*
 * Запрос 800
 */
update {$table_prefix}options set option_value = 800
  where option_name = 'imegateleport_query';

/*
 * Регистрируем в imegateleport новые свойства товаров
 */
insert {$table_prefix}imegateleport (object_id, name, guid)
  select t.term_id, 'terms', b.guid from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}imega_prop p on p.guid = b.guid;
/*
 * Запрос 810
 */
update {$table_prefix}options set option_value = 810
  where option_name = 'imegateleport_query';

/**
  TODO 
  
  Сделать проверку на уникальность названий атрибутов pa_attribute
  Записываем таксономию
  Записываем атрибуты в woocommerce_attribute_taxonomies
*/
/*
 * Записываем таксономию для уникальных новых свойств товаров
 */
insert ignore {$table_prefix}term_taxonomy (term_id, taxonomy, description)
  select t.term_id, concat('pa_', pp.slug), ''
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}imega_prop p on p.guid = b.guid
    left join {$table_prefix}imega_prop pp on p.parent_guid = pp.guid;
/*
 * Запрос 820
 */
update {$table_prefix}options set option_value = 820
  where option_name = 'imegateleport_query';

/*
 * Регистрируем в imegateleport таксономию для уникальных новых свойств товаров
 */
insert {$table_prefix}imegateleport (object_id, name)
  select tt.term_taxonomy_id, 'term_taxonomy'
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id;
/*
 * Запрос 830
 */
update {$table_prefix}options set option_value = 830
  where option_name = 'imegateleport_query';

/*
 * Записываем атрибуты в woocommerce_attribute_taxonomies
 * 
 * TODO Отсутствует проверка на уникальность attribute_name
 */
/*insert {$table_prefix}woocommerce_attribute_taxonomies (attribute_name, attribute_label, attribute_type, attribute_orderby)
  select slug, title, val_type, 'name' from {$table_prefix}imega_prop p
  left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = p.slug
    where val_type = 'select' and wat.attribute_name is null;
//set @maxID = (select CAST( ifnull( max(attribute_id), 1) as char) from {$table_prefix}woocommerce_attribute_taxonomies);*/

insert {$table_prefix}woocommerce_attribute_taxonomies (attribute_name, attribute_label, attribute_type, attribute_orderby)
  select slug, title, val_type, 'name' from {$table_prefix}imega_prop p
    left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = p.slug
    where val_type = 'select' and wat.attribute_name is null;
/*
 * Запрос 840
 */
update {$table_prefix}options set option_value = 840
  where option_name = 'imegateleport_query';


/* Как сохранить уникальные аттрибуты?
  select *,'==', concat(slug,(p.id + @maxID)), title, val_type, 'name' from {$table_prefix}imega_prop p
    left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = p.slug
    where val_type = 'select ' and wat.attribute_name is not null;
*/

/*
 * Регистрируем в imegateleport атрибуты из woocommerce_attribute_taxonomies
 * ver beta
 * +  and wat.attribute_name is null
 */
insert {$table_prefix}imegateleport (object_id, name, guid)
  select wat.attribute_id, 'woocommerce_attribute_taxonomies', p.guid
    from {$table_prefix}imega_prop p
    left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = p.slug
    where val_type = 'select' and wat.attribute_name is null;
/*
 * Запрос 850
 */
update {$table_prefix}options set option_value = 850
  where option_name = 'imegateleport_query';

/*
 * Регистрируем в imegateleport атрибуты из woocommerce_attribute_taxonomies
 * ver beta
 * + and wat.attribute_name is null
 */
insert {$table_prefix}imegateleport (object_id, name, guid)
  select wat.attribute_id, 'woocommerce_attribute_taxonomies', p.guid
    from {$table_prefix}imega_prop p
    left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = p.slug
    where val_type = 'select' and wat.attribute_name is null;
/*
 * Запрос 860
 */
update {$table_prefix}options set option_value = 860
  where option_name = 'imegateleport_query';

/*
 * Записываем связи атрибутов с товаром
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select tp.object_id, tt.term_taxonomy_id
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id
    left join {$table_prefix}imega_misc m on m.val = b.guid
    left join {$table_prefix}imegateleport tp on tp.guid = m.guid
    where tp.object_id is not null;
/*
 * Запрос 870
 */
update {$table_prefix}options set option_value = 870
  where option_name = 'imegateleport_query';

/*
 * Очистить буфер
 */
truncate table {$table_prefix}imegaBuffer;
/*
 * Запрос 880
 */
update {$table_prefix}options set option_value = 880
  where option_name = 'imegateleport_query';

/*
 * Записываем уникальные атрибуты типа text
 */
insert {$table_prefix}imegaBuffer (id, name, val, uni)
  select m.id, m.val, m.valSlug, 'unique' from {$table_prefix}imega_prop p
    left join {$table_prefix}imega_misc m on m.label = p.guid
    left join {$table_prefix}terms t on t.slug = m.valSlug
    where t.slug is null and p.val_type = 'text' and m.type <> 'group'
    group by m.valSlug;
/*
 * Запрос 890
 */
update {$table_prefix}options set option_value = 890
  where option_name = 'imegateleport_query';

/*
 * Уникальный номер последней записи в terms
 */
set @maxID = (select CAST( ifnull( max(term_id), 1) as char) from {$table_prefix}terms);
/*
 * Запрос 900
 */
update {$table_prefix}options set option_value = 900
  where option_name = 'imegateleport_query';

/*
 * Записываем НЕуникальные атрибуты типа text
 */
insert {$table_prefix}imegaBuffer (id, name, val)
  select m.id, m.val, concat(m.valSlug, @maxID+m.id)
    from {$table_prefix}imega_prop p
    left join {$table_prefix}imega_misc m on m.label = p.guid
    left join {$table_prefix}terms t on t.slug = m.valSlug
    where t.slug is not null and p.val_type = 'text' and m.type <> 'group';
/*
 * Запрос 910
 */
update {$table_prefix}options set option_value = 910
  where option_name = 'imegateleport_query';

/*
 * Уникальные свойства товара
 */
insert ignore {$table_prefix}terms (name, slug)
  select name, val from {$table_prefix}imegaBuffer;
/*
 * Запрос 920
 */
update {$table_prefix}options set option_value = 920
  where option_name = 'imegateleport_query';

/*
 * Регистрируем в imegateleport атрибуты
 */
insert {$table_prefix}imegateleport (object_id, name)
  select t.term_id, 'terms'from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val;
/*
 * Запрос 930
 */
update {$table_prefix}options set option_value = 930
  where option_name = 'imegateleport_query';

/*
 * Записываем таксономию для уникальных атрибуты типа text
 */
insert ignore {$table_prefix}term_taxonomy (term_id, taxonomy, description)
  select t.term_id, substr(concat('pa_', p.slug), 0, 32), ''
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}imega_misc m on m.id = b.id
    left join {$table_prefix}imega_prop p on p.guid = m.label;
/*
 * Запрос 940
 */
update {$table_prefix}options set option_value = 950
  where option_name = 'imegateleport_query';

/*
 * Регистрируем в imegateleport таксономию атрибуты типа text
 */
insert {$table_prefix}imegateleport (object_id, name)
  select term_taxonomy_id, 'term_taxonomy'from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id;
/*
 * Запрос 960
 */
update {$table_prefix}options set option_value = 970
  where option_name = 'imegateleport_query';

/*
 * Простые атрибуты
 */
create table {$table_prefix}imegaAttrs(
  post int,
  taxonomy int,
  json varchar(250)
)engine=innodb default charset=utf8;
/*
 * Запрос 980
 */
update {$table_prefix}options set option_value = 980
  where option_name = 'imegateleport_query';

/*
 * Генерим JSON для простых атрибутов товара
 * ver 2
 * + left join {$table_prefix}imega_misc m on m.guid = p.guid and m.labelSlug = wat.attribute_name
 * - 1
 * + ', m._visible,'
 */
insert into {$table_prefix}imegaAttrs (post, taxonomy, json)
  select tr.object_id, tt.term_taxonomy_id,
    concat("s:", length(tt.taxonomy),':"', tt.taxonomy,'";a:6:{s:4:"name";s:',
           length(tt.taxonomy),':"',tt.taxonomy,
           '";s:5:"value";s:0:"";s:8:"position";s:1:"0";s:10:"is_visible";i:',
           m._visible,';s:12:"is_variation";i:0;s:11:"is_taxonomy";i:1;}') json
    from {$table_prefix}imega_prod as p
    left join {$table_prefix}imegateleport as tp on tp.guid = p.guid
    left join {$table_prefix}term_relationships as tr on tr.object_id = tp.object_id
    left join {$table_prefix}term_taxonomy as tt on tt.term_taxonomy_id = tr.term_taxonomy_id
    left join {$table_prefix}woocommerce_attribute_taxonomies as wat on concat('pa_', wat.attribute_name) = tt.taxonomy
    left join {$table_prefix}imega_misc m on m.guid = p.guid and m.labelSlug = wat.attribute_name
    where wat.attribute_id is not null
    group by tp.object_id, tt.taxonomy;
/*
 * Запрос 990
 */
update {$table_prefix}options set option_value = 990
  where option_name = 'imegateleport_query';

/*
 * OFFERS
 */
/*
 * Прогресс 75
 */
update {$table_prefix}options set option_value = 75
  where option_name = 'imegateleport_progress';
/*
 * Запрос 1000
 */
update {$table_prefix}options set option_value = 1000
  where option_name = 'imegateleport_query';

/*
 * Записываем все варианты товара
 * ver 1.4
 * - insert {$table_prefix}posts (post_date, post_date_gmt, post_type, post_parent, post_name, post_excerpt, to_ping, pinged, post_content_filtered)
 * + insert {$table_prefix}posts (post_date, post_date_gmt, post_type, post_parent, post_name, post_excerpt, to_ping, pinged, post_content_filtered, post_content, post_title)
 * - select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'product_variation', tp.object_id, o.guid, '', '', '', ''
 * + select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'product_variation', tp.object_id, o.guid, '', '', '', '', '', ''
 */
insert {$table_prefix}posts (post_date, post_date_gmt, post_type, post_parent, post_name, post_excerpt, to_ping, pinged, post_content_filtered, post_content, post_title)
  select CURRENT_TIMESTAMP, UTC_TIMESTAMP, 'product_variation', tp.object_id, o.guid, '', '', '', '', '', ''
    from {$table_prefix}imega_offers o
    left join {$table_prefix}imegateleport tp on tp.guid = o.prod_guid
    where o.postType = 'product_variation';
/*
 * Запрос 1010
 */
update {$table_prefix}options set option_value = 1010
  where option_name = 'imegateleport_query';

/*
 * Регистрируем варианты в imegateleport
 * insert into a1_imegateleport (object_id, name, guid)
 * select p.id, 'posts', o.guid from a1_posts as p
 * left join a1_imega_offers as o on o.guid = p.post_name;
 */
/*
 * Записать количество цены и баркод, цена распродажи и даты распродажи
 * ver 5
 * + where p.id is not null
 * ver 4
 * + union
 * + select t.object_id, '_weight', m.val from {$table_prefix}imega_misc as m
 * + left join {$table_prefix}imegateleport as t on t.guid = m.guid
 * + where m.labelSlug = 'ves'
 * ver 3
 * + union
 * +   select p.id, '_manage_stock', 'yes'from {$table_prefix}imega_offers_prices as op
 *   +   left join {$table_prefix}imegateleport as t on t.guid = op.offer_guid
 *   +   left join {$table_prefix}posts as p on p.id = t.object_id
 *   +   where p.id is not null
 * ver 2
 * + where p.id is not null
 * ver 3
 * - where o.postType<>'product_variation'
 * + where o.postType<>'product_variation' and p.id is not null
 * ver 4
 * - where o.postType<>'product_variation'
 * + where o.postType<>'product_variation' and p.id is not null
 */
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select p.id, '_sku', o.barcode from {$table_prefix}imega_offers o
    left join {$table_prefix}posts p on p.post_name = o.guid
    where p.id is not null
  union
  select p.id, '_sku', o.barcode from {$table_prefix}imega_offers o
    left join {$table_prefix}imegateleport t on t.guid = o.prod_guid
    left join {$table_prefix}posts p on p.id = t.object_id
    where o.postType<>'product_variation' and p.id is not null;
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select p.id, '_stock', o.amount from {$table_prefix}imega_offers o
    left join {$table_prefix}posts p on p.post_name = o.guid
    where p.id is not null
  union
  select p.id, '_stock', o.amount from {$table_prefix}imega_offers o
    left join {$table_prefix}imegateleport t on t.guid = o.prod_guid
    left join {$table_prefix}posts p on p.id = t.object_id
    where o.postType<>'product_variation' and p.id is not null;
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select p.id, '_regular_price', op.price from {$table_prefix}imega_offers_prices op
    left join {$table_prefix}posts p on p.post_name = op.offer_guid
    where p.id is not null
  union
  select p.id, '_regular_price', op.price from {$table_prefix}imega_offers_prices op
    left join {$table_prefix}imegateleport t on t.guid = op.offer_guid
    left join {$table_prefix}posts p on p.id = t.object_id
    where p.id is not null;
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select p.id, '_manage_stock', 'yes' from {$table_prefix}imega_offers_prices op
    left join {$table_prefix}imegateleport t on t.guid = op.offer_guid
    left join {$table_prefix}posts p on p.id = t.object_id
    where p.id is not null;
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select p.id, '_price', op.price from {$table_prefix}imega_offers_prices op
    left join {$table_prefix}posts p on p.post_name = op.offer_guid
    where p.id is not null
  union
  select p.id, '_price', op.price from {$table_prefix}imega_offers_prices op
    left join {$table_prefix}imegateleport t on t.guid = op.offer_guid
    left join {$table_prefix}posts p on p.id = t.object_id
    where p.id is not null;
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select t.object_id, '_weight', m.val from {$table_prefix}imega_misc m
    left join {$table_prefix}imegateleport t on t.guid = m.guid
    where m.labelSlug = 'ves';
/*
 * Запрос 1020
 */
update {$table_prefix}options set option_value = 1020
  where option_name = 'imegateleport_query';

/*
 * Прогресс 80
 */
update {$table_prefix}options set option_value = 80
  where option_name = 'imegateleport_progress';
/*
 * Запрос 1030
 */
update {$table_prefix}options set option_value = 1030
  where option_name = 'imegateleport_query';

/*
 * Очистить буфер
 */
truncate table {$table_prefix}imegaBuffer;
/*
 * Запрос 1040
 */
update {$table_prefix}options set option_value = 1040
  where option_name = 'imegateleport_query';

/*
 * Закинуть в буффер уникальные terms
 */
insert {$table_prefix}imegaBuffer (id, name, val, uni, attr)
  select of.id, of.val, of.valSlug, 'unique', of.titleSlug
    from {$table_prefix}imega_offers_features of
    left join {$table_prefix}terms t on t.slug = of.valSlug
    where t.slug is null
    group by of.valSlug;
/*
 * Запрос 1050
 */
update {$table_prefix}options set option_value = 1050
  where option_name = 'imegateleport_query';

/*
 * Уникальный номер последней записи в posts
 */
set @maxID = (select CAST( ifnull( max(term_id), 1) as char) from {$table_prefix}terms);
/*
 * Запрос 1060
 */
update {$table_prefix}options set option_value = 1060
  where option_name = 'imegateleport_query';

/*
 * Закинуть в буффер НЕуникальные terms
 */
insert {$table_prefix}imegaBuffer (id, name, val, attr)
  select of.id, of.val, concat(of.valSlug, (@maxID+of.id)), of.titleSlug
    from {$table_prefix}imega_offers_features of
    left join {$table_prefix}terms t on t.slug = of.valSlug
    left join {$table_prefix}imegaBuffer b on b.val = of.valSlug and b.attr = of.titleSlug
    where b.id is null
    group by of.valSlug, titleSlug;
/*
 * Запрос 1070
 */
update {$table_prefix}options set option_value = 1070
  where option_name = 'imegateleport_query';

/*
 * Записываем terms
 */
insert {$table_prefix}terms (name, slug)
  select name, val from {$table_prefix}imegaBuffer;
/*
 * Запрос 1080
 */
update {$table_prefix}options set option_value = 1080
  where option_name = 'imegateleport_query';

/*
 * Прогресс 85
 */
update {$table_prefix}options set option_value = 85
  where option_name = 'imegateleport_progress';
/*
 * Запрос 1090
 */
update {$table_prefix}options set option_value = 1090
  where option_name = 'imegateleport_query';

/*
 * Записываем таксономию для уникальных атрибуты типа text
 */
insert ignore {$table_prefix}term_taxonomy (term_id, taxonomy, description)
  select t.term_id, concat('pa_', of.titleSlug), ''
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}imega_offers_features of on of.id = b.id;
/*
 * Запрос 1100
 */
update {$table_prefix}options set option_value = 1100
  where option_name = 'imegateleport_query';

/*
 * Записываем атрибуты в woocommerce_attribute_taxonomies
 * ver 2
 * + left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = b.attr
 * + where wat.attribute_name is null
 */
insert {$table_prefix}woocommerce_attribute_taxonomies (attribute_name, attribute_label, attribute_type, attribute_orderby)
  select of.titleSlug, of.title, 'text', 'name'
    from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}imega_offers_features of on of.id = b.id
    left join {$table_prefix}woocommerce_attribute_taxonomies wat on wat.attribute_name = b.attr
    where wat.attribute_name is null
    group by of.titleSlug;
/*
 * Запрос 1110
 */
update {$table_prefix}options set option_value = 1110
  where option_name = 'imegateleport_query';

/*
 * Записать в imegateleport
 */
insert {$table_prefix}imegateleport (object_id, name)
  select wat.attribute_id, 'woocommerce_attribute_taxonomies'from {$table_prefix}woocommerce_attribute_taxonomies wat
    left join {$table_prefix}imegaBuffer b on b.attr = wat.attribute_name
    where b.attr is not null
    group by b.attr;
/*
 * Запрос 1120
 */
update {$table_prefix}options set option_value = 1120
  where option_name = 'imegateleport_query';

/*
 * Регистрируем в imegateleport атрибуты и их таксономию
 */
insert {$table_prefix}imegateleport (object_id, name)
  select t.term_id, 'terms'from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
  union
  select term_taxonomy_id, 'term_taxonomy'from {$table_prefix}imegaBuffer b
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id;
/*
 * Запрос 1130
 */
update {$table_prefix}options set option_value = 1130
  where option_name = 'imegateleport_query';

/*
 * Запишем связи атрибутов с товарами
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select tp.object_id, tt.term_taxonomy_id
    from {$table_prefix}imega_prod p
    left join {$table_prefix}imegateleport tp on tp.guid = p.guid
    left join {$table_prefix}imega_offers_features of on of.prodGuid = p.guid
    left join {$table_prefix}imegaBuffer b on of.titleSlug = b.attr and of.val = b.name
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id
    where of.id is not null
    group by tp.object_id, tt.term_taxonomy_id;
/*
 * Запрос 1140
 */
update {$table_prefix}options set option_value = 1140
  where option_name = 'imegateleport_query';

/*
 * Прогресс 90
 */
update {$table_prefix}options set option_value = 90
  where option_name = 'imegateleport_progress';
/*
 * Запрос 1150
 */
update {$table_prefix}options set option_value = 1150
  where option_name = 'imegateleport_query';

/*
 * Генерим JSON для вариантов атрибутов товара
 */
drop table if exists {$table_prefix}imega_temp, {$table_prefix}imega_temp2;

create table {$table_prefix}imega_temp
  select tr.object_id, tt.term_taxonomy_id, length(tt.taxonomy) len_taxonomy, tt.taxonomy
    from {$table_prefix}imega_prod as p
    left join {$table_prefix}imegateleport as tp on tp.guid = p.guid
    left join {$table_prefix}term_relationships as tr on tr.object_id = tp.object_id
    left join {$table_prefix}term_taxonomy as tt on tt.term_taxonomy_id = tr.term_taxonomy_id
    left join {$table_prefix}woocommerce_attribute_taxonomies as wat on concat('pa_', wat.attribute_name) = tt.taxonomy
    left join {$table_prefix}imegaAttrs as a on a.post = tr.object_id and a.taxonomy = tt.term_taxonomy_id
    where wat.attribute_id is not null and a.post is null
    group by tp.object_id, tt.taxonomy;

create table {$table_prefix}imega_temp2
  select t.object_id post, t.term_taxonomy_id taxonomy, concat("s:", length(t.taxonomy),':"',
         t.taxonomy,'";a:6:{s:4:"name";s:',length(t.taxonomy),':"',t.taxonomy,
         '";s:5:"value";s:0:"";s:8:"position";s:1:"0";s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}') json
  from {$table_prefix}imega_temp t;

/*
 * Запрос 1160
 */
update {$table_prefix}options set option_value = 1160
  where option_name = 'imegateleport_query';

/*
 * Формируем json к товарам с атрибутами
 */
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select post, '_product_attributes', concat('a:', count(post), ':{', group_concat(json separator''), '}')
  from {$table_prefix}imega_temp2 group by post;

drop table if exists {$table_prefix}imega_temp, {$table_prefix}imega_temp2;

/*
 * Запрос 1160
 */
update {$table_prefix}options set option_value = 1160
  where option_name = 'imegateleport_query';

/*
 * Записать каждому варианту его атрибуты
 */
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select po.id, concat('attribute_', tt.taxonomy), t.slug
    from {$table_prefix}imega_prod p
    left join {$table_prefix}imegateleport tp on tp.guid = p.guid
    left join {$table_prefix}imega_offers_features of on of.prodGuid = p.guid
    left join {$table_prefix}posts po on po.post_name = of.offer_guid
    left join {$table_prefix}imegaBuffer b on of.titleSlug = b.attr and of.val = b.name
    left join {$table_prefix}terms t on t.slug = b.val
    left join {$table_prefix}term_taxonomy tt on tt.term_id = t.term_id
    where of.id is not null;
/*
 * Прогресс 95
 */
update {$table_prefix}options set option_value = 95
  where option_name = 'imegateleport_progress';
/*
 * Запрос 1170
 */
update {$table_prefix}options set option_value = 1170
  where option_name = 'imegateleport_query';

/* 
 * Запишем связи с товарами вариационного типа
 * ver 2
 * - where o.postType = 'product_variation'
 * + where o.postType = 'product_variation' and tp.object_id is not null
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select tp.object_id, @prodVariable from {$table_prefix}imega_offers o
    left join {$table_prefix}imegateleport tp on tp.guid = o.prod_guid
    where o.postType = 'product_variation'and tp.object_id is not null
    group by o.prod_guid;
/*
 * Запрос 1180
 */
update {$table_prefix}options set option_value = 1180
  where option_name = 'imegateleport_query';

/*
 * Запишем связи с товарами простого типа
 * ver 2
 * + and tp.object_id is not null
 * ver 3
 * - where o.postType<>'product_variation'or o.postType is null
 * + where (o.postType<>'product_variation'or o.postType is null)
 */
insert ignore {$table_prefix}term_relationships (object_id, term_taxonomy_id)
  select tp.object_id, @prodSimple
    from {$table_prefix}imega_prod p
    left join {$table_prefix}imega_offers o on o.prod_guid = p.guid
    left join {$table_prefix}imegateleport tp on tp.guid = p.guid
    where (o.postType<>'product_variation'or o.postType is null)
      and tp.name = 'posts'
      and tp.object_id is not null
    group by p.guid;
/*
 * Запрос 1190
 */
update {$table_prefix}options set option_value = 1190
  where option_name = 'imegateleport_query';

/*
 * Сделать видимым товар
 * ver 2
 * + where t.object_id is not null;
 */
insert {$table_prefix}postmeta (post_id, meta_key, meta_value)
  select t.object_id, '_visibility', 'visible'from {$table_prefix}imega_prod p
    left join {$table_prefix}imegateleport t on t.guid = p.guid
    where t.object_id is not null;
/*
 * Запрос 1200
 */
update {$table_prefix}options set option_value = 1200
  where option_name = 'imegateleport_query';
