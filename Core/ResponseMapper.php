<?php
/**
 * Shop System Plugins:
 * - Terms of Use can be found under:
 * https://github.com/wirecard/oxid-ee/blob/master/_TERMS_OF_USE
 * - License can be found under:
 * https://github.com/wirecard/oxid-ee/blob/master/LICENSE
 */

namespace Wirecard\Oxid\Core;

use Wirecard\PaymentSdk\Entity\AccountHolder;
use Wirecard\PaymentSdk\Entity\Basket;
use Wirecard\PaymentSdk\Entity\Card;
use Wirecard\PaymentSdk\Entity\PaymentDetails;
use Wirecard\PaymentSdk\Entity\TransactionDetails;
use Wirecard\PaymentSdk\Response\SuccessResponse;

/**
 * Converts an XML response to a Response object and provides various getters.
 */
class ResponseMapper
{
    /**
     * @var SuccessResponse
     */
    private $oResponse;

    /**
     * ResponseMapper constructor.
     *
     * @param string $sXml
     */
    public function __construct(string $sXml)
    {
        $this->setResponse($sXml);
    }

    /**
     * Response setter.
     *
     * @param string $sXml
     */
    public function setResponse(string $sXml)
    {
        $this->oResponse = new SuccessResponse(simplexml_load_string($sXml));
    }

    /**
     * Returns the response's payment details.
     *
     * @return array
     */
    public function getPaymentDetails(): array
    {
        return $this->_getObjectDataArray($this->oResponse->getPaymentDetails());
    }

    /**
     * Returns the response's transaction details.
     *
     * @return array
     */
    public function getTransactionDetails(): array
    {
        return $this->_getObjectDataArray($this->oResponse->getTransactionDetails());
    }

    /**
     * Returns the response's account holder.
     *
     * @return array
     */
    public function getAccountHolder(): array
    {
        return $this->_getObjectDataArray($this->oResponse->getAccountHolder());
    }

    /**
     * Returns the response's shipping data.
     *
     * @return array
     */
    public function getShipping(): array
    {
        return $this->_getObjectDataArray($this->oResponse->getShipping());
    }

    /**
     * Returns the response's basket.
     *
     * @return array
     */
    public function getBasket(): array
    {
        return $this->_getObjectDataArray($this->oResponse->getBasket());
    }

    /**
     * Returns the response's card.
     *
     * @return array
     */
    public function getCard(): array
    {
        return $this->_getObjectDataArray($this->oResponse->getCard());
    }

    /**
     * Returns the whole data from the response xml.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->oResponse->getData();
    }

    /**
     * Returns an array with the given object's data
     *
     * @param PaymentDetails|TransactionDetails|AccountHolder|Basket|Card $oResponseObject
     * @return array
     */
    private function _getObjectDataArray($oResponseObject)
    {
        return $oResponseObject ? $this->_parseHtml($oResponseObject->getAsHtml()) : [];
    }

    /**
     * Converts HTML returned by the SDK to an associative array.
     *
     * @param string $sHtml
     * @return array
     */
    private function _parseHtml(string $sHtml): array
    {
        $aFields = [];
        preg_match_all('/<tr><td>(.+?)<\/td><td>(.+?)<\/td><\/tr>/', $sHtml, $aMatches, PREG_SET_ORDER);

        if (!$aMatches) {
            return $aFields;
        }

        foreach ($aMatches as $aMatch) {
            $aFields[$aMatch[1]] = $aMatch[2];
        }

        return $aFields;
    }
}