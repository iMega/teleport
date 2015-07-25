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

trait Attribute
{
    /**
     * Send event
     *
     * @param array $data Data of record.
     */
    abstract public function event(array $data);

    /**
     * Аттрибут предложений "Содержит только изменения"
     *
     * @param string $element Element name.
     */
    private function attrChanges($element)
    {
        $value = $this->xml->elements($element);
        if (empty($value)) {
            return;
        }
        $attrs = $value[0]->attribute();

        $this->event([
            'entityType' => self::KEY_SETS,
            'changes'    => isset($attrs[Description::CONTAINS_ONLY_THE_CHANGES]),
        ]);
    }
}
