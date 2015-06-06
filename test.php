<?php

include 'tests/bootstrap.php';

use iMega\WalkerXML\WalkerXML;
use iMega\Teleport\Parser\Description;
use iMega\Teleport\StringsTools;

class StockTest
{
    const KEY_GROUPS = 1,
        KEY_PROP   = 10,
        KEY_PROD   = 20,
        KEY_MISC   = 30,
        KEY_SETS   = 40;

    /**
     * @var string
     */
    protected $data;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    /**
     * setUp
     */
    public function __construct()
    {
        $adapter          = new Gaufrette\Adapter\Local(__DIR__ . '/tests/Fixtures/2.04');
        $fs               = new Gaufrette\Filesystem($adapter);
        $data       = $fs->read('import.xml');
        $this->xml = new WalkerXML($data);
    }

    public function test()
    {
        $groups = $this->xml->deepElement(
            $this->xml->root(),
            Description::CLASSI,
            Description::GROUPS,
            Description::GROUP
        );
        if (null !== $groups) {
            $this->createGroups($groups);
        }
    }

    private function createGroups($groups, $parent = '')
    {
        var_dump(is_array($groups));
        foreach ($groups as $group) {
            $id = $this->createGroup($group, $parent);

            $subGroup = $this->xml->deepElement(
                $group,
                Description::GROUPS,
                Description::GROUP
            );
var_dump(1111111,$subGroup);
            if (null !== $subGroup) {
                $this->createGroups($subGroup, $id);
            }
        }
    }

    private function createGroup($group, $parent){
        $id   = $this->xml->element(Description::ID, $group);
        $name = $this->xml->element(Description::NAME, $group);

        /*var_dump([
                'entityType' => self::KEY_GROUPS,
                'guid'       => $id,
                'parent'     => $parent,
                'title'      => $name,
                'slug'       => StringsTools::translite($name),
            ]);*/

        return $id;
    }

    private function createProperties($properties)
    {
        foreach ($properties as $property) {
            $id   = $this->xml->element(Description::ID, $property);
            $name = $this->xml->element(Description::NAME, $property);

            $isDictionary = $this->xml->element(Description::VALUETYPE, $property);
            $valueType = ($isDictionary == Description::DIC) ? 'select' : 'text';

            $slug = StringsTools::translite($name, '-', 199);

            var_dump([
                    'entityType'  => self::KEY_PROP,
                    'guid'        => $id,
                    'title'       => $name,
                    'slug'        => $slug,
                    'val_type'    => $valueType,
                    'parent_guid' => '',
                ]);


            $dictonaries = $this->xml->deepElement(
                $property,
                Description::ATTRIBUTESVARIANTS
            );

            if (null !== $dictonaries) {
                foreach ($dictonaries as $item) {
                    $dictonaryId    = $this->xml->element(Description::VALUEID, $item);
                    $dictonaryValue = $this->xml->element(Description::VALUE, $item);
                    $slug           = StringsTools::translite($dictonaryValue, '-', 199);

                    var_dump([
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
}

$a = new StockTest();

$a->test();
