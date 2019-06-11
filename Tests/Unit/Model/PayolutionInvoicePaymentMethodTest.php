<?php
/**
 * Shop System Plugins:
 * - Terms of Use can be found under:
 * https://github.com/wirecard/oxid-ee/blob/master/_TERMS_OF_USE
 * - License can be found under:
 * https://github.com/wirecard/oxid-ee/blob/master/LICENSE
 */

use Wirecard\Oxid\Model\PayolutionInvoicePaymentMethod;
use Wirecard\PaymentSdk\Config\Config;
use Wirecard\PaymentSdk\Config\PaymentMethodConfig;
use Wirecard\PaymentSdk\Transaction\PayolutionInvoiceTransaction;

class PayolutionInvoicePaymentMethodTest extends \Wirecard\Test\WdUnitTestCase
{
    /**
     * @var PayolutionInvoicePaymentMethod
     */
    private $_oPaymentMethod;

    protected function setUp()
    {
        parent::setUp();

        $this->_oPaymentMethod = new PayolutionInvoicePaymentMethod();
    }

    public function testGetConfig()
    {
        $oConfig = $this->_oPaymentMethod->getConfig();

        $this->assertInstanceOf(PaymentMethodConfig::class, $oConfig->get(PayolutionInvoicePaymentMethod::getName()));
    }

    public function testGetTransaction()
    {
        $oTransaction = $this->_oPaymentMethod->getTransaction();
        $this->assertInstanceOf(PayolutionInvoiceTransaction::class, $oTransaction);
    }

    public function testGetConfigFields()
    {
        $aFields = $this->_oPaymentMethod->getConfigFields();

        $this->assertEquals([
            'descriptor',
            'additionalInfo',
            'deleteCanceledOrder',
            'deleteFailedOrder',
            'shippingCountries',
            'billingCountries',
            'billingShipping',
            'allowedCurrencies',
            'apiUrl',
            'groupSeparator_eur',
            'httpUser_eur',
            'httpPassword_eur',
            'maid_eur',
            'secret_eur',
            'testCredentials_eur',
        ], array_keys($aFields));
    }

    public function testGetPublicFieldNames()
    {
        $aFieldNames = $this->_oPaymentMethod->getPublicFieldNames();

        $this->assertEquals([
            'apiUrl',
            'maid',
            'descriptor',
            'additionalInfo',
            'deleteCanceledOrder',
            'deleteFailedOrder',
            'shippingCountries',
            'billingCountries',
            'billingShipping',
        ], $aFieldNames);
    }

    public function testGetMetaDataFieldNames()
    {
        $aMinimumExpectedKeys = [
            'shipping_countries',
            'billing_countries',
            'billing_shipping',
            'allowed_currencies',
            'billing_countries',
            'billing_shipping',
            'httpuser_eur',
            'httppass_eur',
            'maid_eur',
            'secret_eur',
        ];

        foreach ($aMinimumExpectedKeys as $sKey) {
            $this->assertContains($sKey, $this->_oPaymentMethod->getMetaDataFieldNames());
        }
    }
}
