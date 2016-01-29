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

use iMega\Teleport\Exception\NotExistElementException;
use iMega\WalkerXML\WalkerXML;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use iMega\Teleport\Events\ParseStock;
use iMega\Teleport\StringsTools;
use iMega\Teleport\Events;

/**
 * Class Stock
 */
class Stock
{
    use Attribute;
    use RegisterNamespaceXml;

    const KEY_GROUPS = 1,
          KEY_PROP   = 10,
          KEY_PROD   = 20,
          KEY_MISC   = 30,
          KEY_SETS   = 40;

    const LENGTH_SLUG = 199;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var WalkerXML
     */
    protected $xml;

    /**
     * @param string                   $data
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($data, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->xml = new WalkerXML($data);
        if (!empty($this->xml->getNamespaces())) {
            $this->xml->registerXPathNamespace('ones', $this->xml->getNamespaces()['']);
            $this->xml->namespace = 'ones';
            $this->xml->root = true;
        }
    }

    /**
     * Stock parser
     */
    public function parse()
    {
        $this->dispatcher->dispatch(Events::BUFFER_PARSE_STOCK_PRE, null);

        $this->attrChanges(Description::CATALOG);

        $groups = $this->xml->elements(
            Description::CLASSI,
            Description::GROUPS,
            Description::GROUP
        );
        $this->createGroups($groups);

        $properties = $this->xml->elements(
            Description::CLASSI,
            Description::PROPERTIES,
            Description::PROPERY
        );
        $this->createProperties($properties);

        $catalog = $this->xml->elements(
            Description::CATALOG
        );
        if (empty($catalog)) {
            throw new NotExistElementException('Need ID catalog');
        }
        $catalogId = $catalog[0]->value(Description::ID);
        $products = $this->xml->elements(
            Description::CATALOG,
            Description::PRODUCTS,
            Description::PRODUCT
        );
        $this->createProducts($products, $catalogId);

        $this->dispatcher->dispatch(Events::BUFFER_PARSE_STOCK_END, null);
    }

    /**
     * Создание группы
     *
     * @param WalkerXML $group
     * @param string    $parent
     *
     * @return string
     */
    private function createGroup(WalkerXML $group, $parent)
    {
        $id   = $group->value(Description::ID);
        $name = $group->value(Description::NAME);

        $this->event([
            'entityType' => self::KEY_GROUPS,
            'guid'       => $id,
            'parent'     => $parent,
            'title'      => $name,
            'slug'       => StringsTools::t15n($name),
        ]);

        return $id;
    }

    /**
     * Cоздание групп
     *
     * @param array  $groups
     * @param string $parent
     *
     * @return void
     */
    private function createGroups($groups, $parent = '')
    {
        foreach ($groups as $group) {
            /**
             * @var WalkerXML $group
             */
            $id = $this->createGroup($group, $parent);
            $this->registerNamespace($group, 'ones');
            $subGroup = $group->elements(
                Description::GROUPS,
                Description::GROUP
            );
            if (!empty($subGroup)) {
                $this->createGroups($subGroup, $id);
            }
        }
    }

    /**
     * Создать товар
     *
     * @param WalkerXML $product
     * @param int   $catalogId
     */
    private function createProduct(WalkerXML $product, $catalogId)
    {
        $this->registerNamespace($product, 'ones');

        $id   = $product->value(Description::ID);
        $name = $product->value(Description::NAME);

        $image    = $product->value(Description::IMAGE);
        $imageUrl = $image;
        if (is_array($image)) {
            $imageUrl = $image[0];
        }

        $this->event([
            'entityType' => self::KEY_PROD,
            'title'        => $name,
            'descr'        => $product->value(Description::DESC),
            'guid'         => $id,
            'slug'         => StringsTools::t15n($name, '-', self::LENGTH_SLUG),
            'catalog_guid' => $catalogId,
            'article'      => $product->value(Description::ARTICLE),
            'img'          => $imageUrl,
            'img_prop'     => '',
        ]);

        $this->registerNamespace($product, 'ones');
        $group = $product->elements(
            Description::GROUPS
        );
        $this->createProductsGroup($group, $id);

        $this->registerNamespace($product, 'ones');
        $propertyValues = $product->elements(
            Description::PROPERTYVALUES,
            Description::PROPERTYVALUE
        );
        $this->createProductsPropertyValue($propertyValues, $id);

        $attributes = $product->elements(
            Description::ATTRIBUTEVALUES,
            Description::ATTRIBUTEVALUE
        );
        $this->createProductsAttributes($attributes, $id);
    }

    /**
     * Создать товары
     *
     * @param array  $products
     * @param string $catalogId
     */
    private function createProducts(array $products, $catalogId)
    {
        foreach ($products as $product) {
            $this->createProduct($product, $catalogId);
        }
    }

    /**
     * Создать атрибуты товара
     *
     * @param array  $attributes
     * @param string $productId
     */
    private function createProductsAttributes(array $attributes, $productId)
    {
        foreach ($attributes as $attribute) {
            /**
             * @var WalkerXML $attribute
             */
            $name  = $attribute->value(Description::NAME);
            $value = $attribute->value(Description::VALUE);

            if (empty($value)) {
                continue;
            }

            $this->event([
                'entityType' => self::KEY_MISC,
                'type'      => 'attr',
                'guid'      => $productId,
                'label'     => $name,
                'val'       => $value,
                'labelSlug' => StringsTools::t15n($name, '-', self::LENGTH_SLUG),
                'countAttr' => count($attributes),
                'valSlug'   => StringsTools::t15n($value, '-', self::LENGTH_SLUG),
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
    private function createProductsGroup(array $groups, $productId)
    {
        foreach ($groups as $group) {
            /**
             * @var WalkerXML $group
             */
            $this->event([
                'entityType' => self::KEY_MISC,
                'type'       => 'group',
                'guid'       => $productId,
                'label'      => $group->value(Description::ID),
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
     * @param array  $properties
     * @param string $productId
     */
    private function createProductsPropertyValue(array $properties, $productId)
    {
        foreach ($properties as $property) {
            /**
             * @var WalkerXML $property
             */
            $propertyValue = $property->value(Description::VALUE);
            if (empty($propertyValue)) {
                continue;
            }
            $this->event([
                'entityType' => self::KEY_MISC,
                'type'       => 'prop',
                'guid'       => $productId,
                'label'      => $property->value(Description::ID, $property),
                'val'        => StringsTools::cropText($propertyValue, self::LENGTH_SLUG),
                'labelSlug'  => '',
                'countAttr'  => '',
                'valSlug'    => StringsTools::t15n($propertyValue, '-', self::LENGTH_SLUG),
                '_visible'   => 0,
            ]);
        }
    }

    /**
     * Создать свойства
     *
     * @param array $properties
     */
    private function createProperties(array $properties)
    {
        foreach ($properties as $property) {
            /**
             * @var WalkerXML $property
             */
            $id   = $property->value(Description::ID);
            $name = $property->value(Description::NAME);

            $isDictionary = $property->value(Description::VALUETYPE);
            $valueType = ($isDictionary == Description::DIC) ? 'select' : 'text';

            $this->event([
                'entityType'  => self::KEY_PROP,
                'guid'        => $id,
                'title'       => $name,
                'slug'        => StringsTools::t15n($name, '-', self::LENGTH_SLUG),
                'val_type'    => $valueType,
                'parent_guid' => '',
            ]);

            $this->registerNamespace($property, 'ones');
            $variants = $property->elements(
                Description::ATTRIBUTESVARIANTS,
                Description::DIC
            );
            $this->createVariants($variants, $id);
        }
    }

    /**
     * Создать варианты значений
     *
     * @params array  $variants
     * @params string $propertyId
     */
    protected function createVariants(array $variants, $propertyId)
    {
        foreach ($variants as $item) {
            /**
             * @var WalkerXML $item
             */
            $dictonaryValue = $item->value(Description::VALUE);

            $this->event([
                'entityType'  => self::KEY_PROP,
                'guid'        => $item->value(Description::VALUEID),
                'title'       => $dictonaryValue,
                'slug'        => StringsTools::t15n($dictonaryValue, '-', self::LENGTH_SLUG),
                'val_type'    => '',
                'parent_guid' => $propertyId,
            ]);
        }
    }

    /**
     * Send event
     *
     * @param array $data Data of record.
     */
    public function event(array $data)
    {
        $this->dispatcher->dispatch(
            Events::BUFFER_PARSE_STOCK,
            new ParseStock($data)
        );
    }
}
