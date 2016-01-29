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
use iMega\Teleport\Events\ParseOffer;
use iMega\Teleport\StringsTools;
use iMega\Teleport\Events;

class Offers
{
    use Attribute;
    use RegisterNamespaceXml;

    const KEY_SETS   = 40,
        KEY_OFFERS   = 50,
        KEY_FEATURES = 60,
        KEY_PRICES   = 70;

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
     * Offers parser
     */
    public function parse()
    {
        $this->dispatcher->dispatch(Events::BUFFER_PARSE_OFFERS_PRE, null);

        $this->attrChanges(Description::PACKAGEOFFERS);

        $offers = $this->xml->elements(
            Description::PACKAGEOFFERS,
            Description::OFFERS,
            Description::OFFER
        );
        $this->createOffers($offers);

        $this->dispatcher->dispatch(Events::BUFFER_PARSE_OFFERS_END, null);
    }

    /**
     * Создать предложения
     *
     * @param array $offers Array with offers.
     */
    private function createOffers(array $offers)
    {
        foreach ($offers as $offer) {
            $this->registerNamespace($offer, 'ones');
            /**
             * @var WalkerXML $offer
             */
            $id       = $offer->value(Description::ID);
            $baseUnit = $offer->value(Description::BASEUNIT);
            $baseUnitRecount = $offer->elements(Description::BASEUNIT);

            if ($baseUnit) {
                $units = $offer->elements(Description::BASEUNIT);
            }

            if (empty($baseUnit) && $baseUnitRecount) {
                $units = $baseUnitRecount[0]->attribute();
            }

            $this->event([
                'entityType'      => self::KEY_OFFERS,
                'guid'            => $id,
                'prod_guid'       => substr($id, 0, 36),
                'barcode'         => $offer->value(Description::BARCODE),
                'title'           => $offer->value(Description::NAME),
                'base_unit'       => empty($baseUnit) ? 'шт' : $baseUnit,
                'base_unit_key'   => isset($units[Description::KEY]) ? $units[Description::KEY] : '',
                'base_unit_title' => isset($units[Description::FULLNAME]) ? $units[Description::FULLNAME] : '',
                'base_unit_int'   => isset($units[Description::INTERNATIONALABBREVIATION]) ? $units[Description::INTERNATIONALABBREVIATION] : '',
                'amount'          => $offer->value(Description::AMOUNT),
                'postType'        => 'product_variation',
            ]);

            $features = $offer->elements(
                Description::PRODUCTFEATURES,
                Description::PRODUCTFEATURE
            );
            $this->createOffersFeatures($features, $id);

            $prices = $offer->elements(
                Description::PRICES,
                Description::PRICE
            );
            $this->createOffersPrices($prices, $id);
        }
    }

    /**
     * Создать характеристику товара
     *
     * @param array  $features
     * @param string $id
     */
    private function createOffersFeatures(array $features, $id)
    {
        foreach ($features as $feature) {
            /**
             * @var WalkerXML $feature
             */
            list($productGuid, $variantGuid) = explode('#', $id);

            $name  = $feature->value(Description::NAME);
            $value = $feature->value(Description::VALUE);

            $this->event([
                'entityType'  => self::KEY_FEATURES,
                'offer_guid'  => $id,
                'prodGuid'    => $productGuid,
                'variantGuid' => $variantGuid,
                'title'       => $name,
                'val'         => $value,
                'titleSlug'   => StringsTools::t15n($name, '-', 199),
                'valSlug'     => StringsTools::t15n($value, '-', 199),
            ]);
        }
    }

    /**
     * Создать цены товара
     *
     * @param array  $prices
     * @param string $id
     */
    private function createOffersPrices(array $prices, $id)
    {
        foreach ($prices as $price) {
            /**
             * @var WalkerXML $price
             */
            $this->event([
                'entityType' => self::KEY_PRICES,
                'offer_guid' => $id,
                'title'      => $price->value(Description::REPRESENTATION),
                'price'      => $price->value(Description::PRICEBYUNIT),
                'currency'   => $price->value(Description::CURRENCY),
                'unit'       => $price->value(Description::UNIT),
                'ratio'      => $price->value(Description::RATIO),
                'type_guid'  => $price->value(Description::PRICETYPEID),
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
            Events::BUFFER_PARSE_OFFERS,
            new ParseOffer($data)
        );
    }
}
