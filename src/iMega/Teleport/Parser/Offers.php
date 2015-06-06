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

class Offers
{
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

    public function __construct($data, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->xml = new WalkerXML($data);
    }

    /**
     * Offers parser
     */
    public function parse()
    {
        $this->attrChanges();

        $offers = $this->xml->deepElement(
            $this->xml->root(),
            Description::PACKAGEOFFERS,
            Description::OFFERS,
            Description::OFFER
        );
        if ($offers) {
            $this->createOffers($offers);
        }
    }

    /**
     * Аттрибут предложений "Содержит только изменения"
     */
    private function attrChanges()
    {
        $offers = $this->xml->deepElement(
            $this->xml->root(),
            Description::PACKAGEOFFERS
        );
        $attrs = $this->xml->attribute($offers);

        $this->event([
            'entityType' => self::KEY_SETS,
            'changes'    => isset($attrs[Description::CONTAINS_ONLY_THE_CHANGES]),
        ]);
    }

    /**
     * Создать предложения
     *
     * @param array $offers Array with offers.
     */
    private function createOffers(array $offers)
    {
        foreach ($offers as $offer) {
            $id = $this->xml->element(Description::ID, $offer);
            $baseUnit = $this->xml->element(Description::BASEUNIT, $offer);

            $attrUnit = array();
            if ($baseUnit) {
                $attrUnit = $this->xml->attribute($offer->{Description::BASEUNIT});
            }

            $this->event([
                'entityType'      => self::KEY_OFFERS,
                'guid'            => $id,
                'prod_guid'       => substr($id, 0, 36),
                'barcode'         => $this->xml->element(Description::BARCODE, $offer),
                'title'           => $this->xml->element(Description::NAME, $offer),
                'base_unit'       => $baseUnit,
                'base_unit_key'   => isset($attrUnit[Description::KEY]) ? $attrUnit[Description::KEY] : '',
                'base_unit_title' => isset($attrUnit[Description::FULLNAME]) ? $attrUnit[Description::FULLNAME] : '',
                'base_unit_int'   => isset($attrUnit[Description::INTERNATIONALABBREVIATION]) ? $attrUnit[Description::INTERNATIONALABBREVIATION] : '',
                'amount'          => $this->xml->element(Description::AMOUNT, $offer),
                'postType'        => 'product_variation',
            ]);

            $features = $this->xml->deepElement(
                $offer,
                Description::PRODUCTFEATURES,
                Description::PRODUCTFEATURE
            );
            if (is_array($features)) {
                $this->createOffersFeatures($features, $id);
            }

            $prices = $this->xml->deepElement(
                $offer,
                Description::PRICES,
                Description::PRICE
            );
            if ($prices) {
                $this->createOffersPrices($prices, $id);
            }
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
            list($productGuid, $variantGuid) = explode('#', $id);

            $name  = $this->xml->element(Description::NAME, $feature);
            $value = $this->xml->element(Description::VALUE, $feature);

            $this->event([
                'entityType'  => self::KEY_FEATURES,
                'offer_guid'  => $id,
                'prodGuid'    => $productGuid,
                'variantGuid' => $variantGuid,
                'title'       => $name,
                'val'         => $value,
                'titleSlug'   => StringsTools::translite($name, '-', 199),
                'valSlug'     => StringsTools::translite($value, '-', 199),
            ]);
        }
    }

    /**
     * Создать цену товара
     *
     * @param \SimpleXMLElement $price Array prices.
     * @param string            $id    GUID price.
     */
    private function createOfferPrice(\SimpleXMLElement $price, $id)
    {
        $this->event([
            'entityType' => self::KEY_PRICES,
            'offer_guid' => $id,
            'title'      => $this->xml->element(Description::REPRESENTATION, $price),
            'price'      => $this->xml->element(Description::PRICEBYUNIT, $price),
            'currency'   => $this->xml->element(Description::CURRENCY, $price),
            'unit'       => $this->xml->element(Description::UNIT, $price),
            'ratio'      => $this->xml->element(Description::RATIO, $price),
            'type_guid'  => $this->xml->element(Description::PRICETYPEID, $price),
        ]);
    }

    /**
     * Создать цены товара
     *
     * @param array|\SimpleXMLElement $prices
     * @param string $id
     */
    private function createOffersPrices($prices, $id)
    {
        if (! is_array($prices)) {
            $this->createOfferPrice($prices, $id);
            return;
        }
        foreach ($prices as $price) {
            $this->createOfferPrice($price, $id);
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
            'buffer.parse.offers',
            new ParseStock($this->json($data))
        );
    }

    /**
     * @param array $data The data being encoded.
     *
     * @return string
     */
    private function json(array $data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
