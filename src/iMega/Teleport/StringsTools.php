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
 * Class StringsTools
 */
class StringsTools
{
    /**
     * Конвертиррования в транслит
     *
     * @param string $str
     * @param string $delimiter
     * @param int    $crop
     *
     * @return string
     */
    public static function t15n($str, $delimiter = '-', $crop = 0)
    {
        $result = mb_strtolower($str, 'UTF-8');
        $result = $crop > 0 ? self::cropText($result, $crop) : $result;

        $rus = mb_split(
            '-',
            'а-б-в-г-д-е-и-й-к-л-м-н-о-п-р-с-т-у-ф-х-ц-ы-э-ж-з-ч-ш-щ-ю-я'
        );
        $tra = mb_split(
            '-',
            'a-b-v-g-d-e-i-y-k-l-m-n-o-p-r-s-t-u-f-h-c-y-e-zh-z-ch-sh-shch-u-ya'
        );
        $result = str_replace($rus, $tra, $result);
        $result = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $result);
        $result = preg_replace("/[\/_|+ -]+/", $delimiter, $result);

        return $result;
    }

    /**
     * @param string $text
     * @param int    $length
     */
    public static function cropText($text, $length)
    {
        return mb_substr($text, 0, $length);
    }
}
