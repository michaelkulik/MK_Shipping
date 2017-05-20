<?php

class MK_Shipment_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    // интерфейс Mage_Shipping_Model_Carrier_Interface имплементировать не обязательно

    protected $_code = 'mkshipment';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        /** @var Mage_Shipping_Model_Rate_Result $result
         * Мы установим объекту $result доступные методы доставки и вернём его
         */
        $result = Mage::getModel('shipping/rate_result');
        $weight = $request->getPackageWeight();
        /** @var Mage_Shipping_Model_Rate_Result_Method $method */
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));// также как и в Payment Method есть этот метод, он
        // возьмёт значение из system.xml

        // определим, какой способ доставки использовать в зависимости от веса
        if ($weight > $this->getConfigData('max_packet_weight')) {
            $this->_getBoxMethod($weight, $method);// parcel
        } else {
            $this->_getPacketMethod($weight, $method); // packet
        }
        $result->append($method);// в append() можно "аппэндить" более 1-го метода; в нашем случае
        // из управляющей конструкции выше может вызываться только один метод в зависимости от веса
        return $result;

    }

    protected function _getBoxMethod($weight, $method)
    {
        $method->setMethod('box');
        $method->setMethodTitle('Belpost parcel');
        $sum = Mage::helper('mkshipment')->getBoxCost($weight);
        $method->setPrice($sum / 0.5);
    }

    protected function _getPacketMethod($weight, $method)
    {
        $method->setMethod('packet');
        $method->setMethodTitle('Belpost packet');
        $sum = Mage::helper('mkshipment')->getPacketCost($weight);
        $method->setPrice($sum / 0.5);
    }

    public function isTrackingAvailable()
    {
        return false;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        // возвращаемый здесь список не решает непосредственно, какие методы будут выводиться
        // как доступные на чекауте, за это отвечает метод collectRates()
        return [ // ключи и значения элементов - произвольные
            'box' => 'Belpost parcel',
            'packet' => 'Belpost packet'
        ];
    }
}