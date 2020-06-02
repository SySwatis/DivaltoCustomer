<?php

namespace Divalto\Customer\Model\Config\Source;

class Stores implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '0' => 'Choisir ...',
            '1' => 'Centrale Ethnique',
            '2' => 'Pacha Distribution',
            '3' => 'Stockhall MARSEILLE',
            '4' => 'Stockhall NICE',
            '5' => 'Stockhall AVIGNON',
            '6' => 'Stockhall TOULOUSE'
        ];
    }
}