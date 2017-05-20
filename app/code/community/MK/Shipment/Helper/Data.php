<?php

class MK_Shipment_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getBoxCost($weight)
    {
        $request = new Zend_Http_Client();
        $request->setUri('http://tarifikator.belpost.by/forms/international/ems.php');

        $request->setParameterPost([
            'who' => 'ur',
            'type' => 'goods',
            'to' => 'n7',
            'weight' => $weight,
        ]);
        $response = $request->request(Zend_Http_Client::POST);
        $html = $response->getBody();
        $regex = "/<h1>Сумма: (\d+) руб. (\d+) коп. <\/h1>/im";
        preg_match_all($regex, $html, $matches, PREG_PATTERN_ORDER);
        if (isset($matches[0][0])) {
            return $matches[1][0] . '.' . $matches[2][0];
        }
        return Mage::getStoreConfig('carriers/mkshipment/price');
        // может быть вариант возврата false, как флага того, что этот метод не работает
    }

    public function getPacketCost($weight)
    {
        $request = new Zend_Http_Client();
        $request->setUri('http://tarifikator.belpost.by/forms/international/packet.php');

        $request->setParameterPost([
            'who' => 'ur',
            'type' => 'registered',
            'priority' => 'priority',
            'to' => 'other',
            'weight' => $weight,
        ]);
        $response = $request->request(Zend_Http_Client::POST);
        $html = $response->getBody();
        $regex = "/<h1>Сумма: (\d+) руб. (\d+) коп.<\/h1>/im";
        preg_match_all($regex, $html, $matches, PREG_PATTERN_ORDER);
        if (isset($matches[0][0])) {
            return $matches[1][0] . '.' . $matches[2][0];
        }
        return Mage::getStoreConfig('carriers/mkshipment/price');
        // может быть вариант возврата false, как флага того, что этот метод не работает
    }
}