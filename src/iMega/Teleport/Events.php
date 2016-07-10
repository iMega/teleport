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
namespace iMega\Teleport;

/**
 * Class Events
 */
final class Events
{
    const BUFFER_PARSE_END        = 'buffer.parse.end',
          BUFFER_PARSE_START      = 'buffer.parse.start',
          BUFFER_PARSE_DUMP       = 'buffer.parse.dump',
          BUFFER_PARSE_STOCK_PRE  = 'buffer.parse.stock.pre',
          BUFFER_PARSE_STOCK      = 'buffer.parse.stock',
          BUFFER_PARSE_STOCK_END  = 'buffer.parse.stock.end',
          BUFFER_PARSE_OFFERS_PRE = 'buffer.parse.offers.pre',
          BUFFER_PARSE_OFFERS     = 'buffer.parse.offers',
          BUFFER_PARSE_OFFERS_END = 'buffer.parse.offers.end';
}
