<?php

namespace iMega\Teleport\Parser;

trait Attribute
{
    /**
     * Аттрибут предложений "Содержит только изменения"
     */
    private function attrChanges($element)
    {
        $offers = $this->xml->elements(
            $element
        );
        $attrs = $offers[0]->attribute();

        $this->event([
            'entityType' => self::KEY_SETS,
            'changes'    => isset($attrs[Description::CONTAINS_ONLY_THE_CHANGES]),
        ]);
    }
}
