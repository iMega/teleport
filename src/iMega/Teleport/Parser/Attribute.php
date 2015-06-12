<?php

namespace iMega\Teleport\Parser;

use iMega\WalkerXML\WalkerXML;

trait Attribute
{
    /**
     * @var WalkerXML
     */
    protected $xml;

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
