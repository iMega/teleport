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

use iMega\WalkerXML\WalkerXML;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use iMega\Teleport\Events\ParseStock;
use iMega\Teleport\StringsTools;

/**
 * Class Stock
 */
class Stock
{
    const KEY_GROUPS = 1,
        KEY_PROP   = 10,
        KEY_PROD   = 20,
        KEY_MISC   = 30,
        KEY_SETS   = 40;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var WalkerXML
     */
    protected $xml;

    public function __construct($data, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->xml = new WalkerXML($data);
    }

    /**
     * Stock parser
     */
    public function parse()
    {
        $this->attrChanges();

        $groups = $this->xml->deepElement(
            $this->xml->root(),
            Description::CLASSI,
            Description::GROUPS,
            Description::GROUP
        );

        if ($groups) {
            $this->createGroups($groups);
        }

        $properties = $this->xml->deepElement(
            $this->xml->root(),
            Description::CLASSI,
            Description::PROPERTIES,
            Description::PROPERY
        );
        if (is_array($properties)) {
            $this->createProperties($properties);
        }

        $catalogId = $this->xml->deepElement(
            $this->xml->root(),
            Description::CATALOG,
            Description::ID
        );

        $products = $this->xml->deepElement(
            $this->xml->root(),
            Description::CATALOG,
            Description::PRODUCTS,
            Description::PRODUCT
        );
        if ($products) {
            $this->createProducts($products, $catalogId);
        }
    }

    /**
     * Аттрибут каталога "Содержит только изменения"
     */
    private function attrChanges()
    {
        $catalog = $this->xml->deepElement(
            $this->xml->root(),
            Description::CATALOG
        );
        $attrs = $this->xml->attribute($catalog);

        $this->event([
            'entityType' => self::KEY_SETS,
            'changes' => isset($attrs[Description::CONTAINS_ONLY_THE_CHANGES]),
        ]);
    }

    /**
     * Создание группы
     *
     * @param string $group
     * @param string $parent
     *
     * @return string
     */
    private function createGroup($group, $parent){
        $id   = $this->xml->element(Description::ID, $group);
        $name = $this->xml->element(Description::NAME, $group);

        $this->event([
            'entityType' => self::KEY_GROUPS,
            'guid'       => $id,
            'parent'     => $parent,
            'title'      => $name,
            'slug'       => StringsTools::translite($name),
        ]);

        return $id;
    }

    /**
     * Cоздание групп из xml
     *
     * @param array $groups
     * @param int   $parent
     *
     * @return void
     */
    private function createGroups($groups, $parent = '')
    {
        if (! is_array($groups)) {
            $id = $this->createGroup($groups, $parent);

            $subGroup = $this->xml->deepElement(
                $groups,
                Description::GROUPS,
                Description::GROUP
            );

            if (is_array($subGroup)) {
                $this->createGroups($subGroup, $id);
            }
            return;
        }

        foreach ($groups as $group) {
            $id = $this->createGroup($group, $parent);

            $subGroup = $this->xml->deepElement(
                $group,
                Description::GROUPS,
                Description::GROUP
            );

            if (is_array($subGroup)) {
                $this->createGroups($subGroup, $id);
            }
        }
    }

    /**
     * Создать товар
     *
     * @param $product
     * @param int   $catalogId
     */
    private function createProduct($product, $catalogId)
    {
        $id = $this->xml->element(Description::ID, $product);
        $id = substr($id, 0, 36);

        $name = $this->xml->element(Description::NAME, $product);
        $desc = $this->xml->element(Description::DESC, $product);
        $img  = $this->xml->element(Description::IMAGE, $product);
        $article = $this->xml->element(Description::ARTICLE, $product);
        $slug = StringsTools::translite($name, '-', 199);
        $this->event([
            'entityType' => self::KEY_PROD,
            'title'        => $name,
            'descr'        => $desc,
            'guid'         => $id,
            'slug'         => $slug,
            'catalog_guid' => $catalogId,
            'article'      => $article,
            'img'          => $img,
            'img_prop'     => '',
        ]);

        $group = $this->xml->deepElement(
            $product,
            Description::GROUPS,
            Description::ID
        );
        if ($group) {
            $this->createProductsGroup($group, $id);
        }

        $propertyValues = $this->xml->deepElement(
            $product,
            Description::PROPERTYVALUES,
            Description::PROPERTYVALUE
        );
        if (is_array($propertyValues)) {
            $this->createProductsPropertyValue($propertyValues, $id);
        }

        $attributes = $this->xml->deepElement(
            $product,
            Description::ATTRIBUTEVALUES,
            Description::ATTRIBUTEVALUE
        );
        if (is_array($attributes)) {
            $this->createProductsAttributes($attributes, $id);
        }
    }

    /**
     * Создать товары
     *
     * @param array $products
     * @param int   $catalogId
     */
    private function createProducts($products, $catalogId){
        if (! is_array($products)) {
            $this->createProduct($products, $catalogId);
            return;
        }
        foreach ($products as $product) {
            $this->createProduct($product, $catalogId);
        }
    }

    /**
     * Создать атрибуты товара
     *
     * @param array $attributes
     * @param int   $productId
     */
    private function createProductsAttributes($attributes, $productId)
    {
        foreach ($attributes as $attribute) {
            $name  = $this->xml->element(Description::NAME, $attribute);
            $value = $this->xml->element(Description::VALUE, $attribute);

            if (empty($value)) {
                continue;
            }

            $this->event([
                'entityType' => self::KEY_MISC,
                'type'      => 'attr',
                'guid'      => $productId,
                'label'     => $name,
                'val'       => $value,
                'labelSlug' => StringsTools::translite($name, '-', 199),
                'countAttr' => count($attributes),
                'valSlug'   => StringsTools::translite($value, '-', 199),
                '_visible'  => 0, //@todo Управление отображением атрибутов
            ]);
        }
    }

    /**
     * Создать группу товара
     *
     * @param array $groups
     * @param int   $productId
     */
    private function createProductsGroup($groups, $productId)
    {
        if (! is_array($groups)) {
            $this->event([
                'entityType' => self::KEY_MISC,
                'type'       => 'group',
                'guid'       => $productId,
                'label'      => $groups,
                'val'        => '',
                'labelSlug'  => '',
                'countAttr'  => '',
                'valSlug'    => '',
                '_visible'   => 0,
            ]);

            return;
        }

        foreach ($groups as $group) {
            $this->event([
                'entityType' => self::KEY_MISC,
                'type'       => 'group',
                'guid'       => $productId,
                'label'      => $this->xml->element(Description::ID, $group),
                'val'        => '',
                'labelSlug'  => '',
                'countAttr'  => '',
                'valSlug'    => '',
                '_visible'   => 0,
            ]);
        }
    }

    /**
     * Создать значания свойств товара
     *
     * @param array $properties
     * @param int   $productId
     */
    private function createProductsPropertyValue($properties, $productId)
    {
        foreach ($properties as $property) {
            $propertyValue = $this->xml->element(Description::VALUE, $property);
            if (empty($propertyValue)) {
                continue;
            }
            $this->event([
                'entityType' => self::KEY_MISC,
                'type'       => 'prop',
                'guid'       => $productId,
                'label'      => $this->xml->element(Description::ID, $property),
                'val'        => StringsTools::cropText($propertyValue, 199),
                'labelSlug'  => '',
                'countAttr'  => '',
                'valSlug'    => StringsTools::translite($propertyValue, '-', 199),
                '_visible'   => 0,
            ]);
        }
    }

    /**
     * Создать свойства
     *
     * @param array $properties
     */
    private function createProperties($properties)
    {
        foreach ($properties as $property) {
            $id   = $this->xml->element(Description::ID, $property);
            $name = $this->xml->element(Description::NAME, $property);

            $isDictionary = $this->xml->element(Description::VALUETYPE, $property);
            $valueType = ($isDictionary == Description::DIC) ? 'select' : 'text';

            $slug = StringsTools::translite($name, '-', 199);

            $this->event([
                'entityType'  => self::KEY_PROP,
                'guid'        => $id,
                'title'       => $name,
                'slug'        => $slug,
                'val_type'    => $valueType,
                'parent_guid' => '',
            ]);


            $dictonaries = $this->xml->deepElement(
                $property,
                Description::ATTRIBUTESVARIANTS,
                Description::DIC
            );

            if (is_array($dictonaries)) {
                foreach ($dictonaries as $item) {
                    $dictonaryId    = $this->xml->element(Description::VALUEID, $item);
                    $dictonaryValue = $this->xml->element(Description::VALUE, $item);
                    $slug           = StringsTools::translite($dictonaryValue, '-', 199);

                    $this->event([
                        'entityType'  => self::KEY_PROP,
                        'guid'        => $dictonaryId,
                        'title'       => $dictonaryValue,
                        'slug'        => $slug,
                        'val_type'    => '',
                        'parent_guid' => $id,
                    ]);
                }
            }
        }
    }

    /**
     * Send event
     *
     * @param array $data
     */
    public function event($data)
    {
        $this->dispatcher->dispatch(
            'buffer.parse.stock',
            new ParseStock($this->json($data))
        );
    }

    /**
     * @param $data
     *
     * @return string
     */
    private function json($data)
    {
        //JSON_UNESCAPED_UNICODE
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
