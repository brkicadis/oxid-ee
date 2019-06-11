<?php
/**
 * Shop System Plugins:
 * - Terms of Use can be found under:
 * https://github.com/wirecard/oxid-ee/blob/master/_TERMS_OF_USE
 * - License can be found under:
 * https://github.com/wirecard/oxid-ee/blob/master/LICENSE
 */

namespace Wirecard\Oxid\Model;

use OxidEsales\Eshop\Core\Registry;

use Wirecard\Oxid\Core\Helper;
use Wirecard\Oxid\Core\PaymentMethodHelper;
use Wirecard\PaymentSdk\Config\Config;
use Wirecard\PaymentSdk\Config\PaymentMethodConfig;
use Wirecard\PaymentSdk\Transaction\PayolutionInvoiceTransaction;

/**
 * Payment method implementation for payolution Invoice
 *
 * @since 1.2.0
 */
class PayolutionInvoicePaymentMethod extends PaymentMethod
{

    protected static $_sName = "payolution-inv";

    /**
     * @inheritdoc
     *
     * @return Config
     *
     * @since 1.2.0
     */
    public function getConfig()
    {
        // get the currency-specific config values
        $oSession = Registry::getSession();
        $oBasket = $oSession->getBasket();
        $sCurrency = strtolower($oBasket->getBasketCurrency()->name);

        $sHttpUserField = 'oxpayments__httpuser_' . $sCurrency;
        $sHttpPassField = 'oxpayments__httppass_' . $sCurrency;
        $sMaidField = 'oxpayments__maid_' . $sCurrency;
        $sSecretField = 'oxpayments__secret_' . $sCurrency;

        $oConfig = new Config(
            $this->_oPayment->oxpayments__wdoxidee_apiurl->value,
            $this->_oPayment->$sHttpUserField->value,
            $this->_oPayment->$sHttpPassField->value
        );

        self::_addAdditionalConfigInfo($oConfig);

        $oPaymentMethodConfig = new PaymentMethodConfig(
            PayolutionInvoiceTransaction::NAME,
            $this->_oPayment->$sMaidField->value,
            $this->_oPayment->$sSecretField->value
        );

        $oConfig->add($oPaymentMethodConfig);

        return $oConfig;
    }

    /**
     * Get the payments method transaction configuration
     *
     * @return \Wirecard\PaymentSdk\Transaction\Transaction
     *
     * @since 1.2.0
     */
    public function getTransaction()
    {
        return new PayolutionInvoiceTransaction();
    }

    /**
     * @inheritdoc
     *
     * @return array
     *
     * @since 1.2.0
     */
    public function getConfigFields()
    {
        // the configuration from the parent class is not used because payolution should support
        // MAID/secret/HTTP user & password per shop currency
        $aConfigFields = [
            'descriptor' => [
                'type' => 'select',
                'field' => 'oxpayments__wdoxidee_descriptor',
                'options' => [
                    '1' => Helper::translate('wd_yes'),
                    '0' => Helper::translate('wd_no'),
                ],
                'title' => Helper::translate('wd_config_descriptor'),
                'description' => Helper::translate('wd_config_descriptor_desc'),
            ],
            'additionalInfo' => [
                'type' => 'select',
                'field' => 'oxpayments__wdoxidee_additional_info',
                'options' => [
                    '1' => Helper::translate('wd_yes'),
                    '0' => Helper::translate('wd_no'),
                ],
                'title' => Helper::translate('wd_config_additional_info'),
                'description' => Helper::translate('wd_config_additional_info_desc'),
            ],
            'deleteCanceledOrder' => [
                'type' => 'select',
                'field' => 'oxpayments__wdoxidee_delete_canceled_order',
                'options' => [
                    '1' => Helper::translate('wd_yes'),
                    '0' => Helper::translate('wd_no'),
                ],
                'title' => Helper::translate('wd_config_delete_cancel_order'),
                'description' => Helper::translate('wd_config_delete_cancel_order_desc'),
            ],
            'deleteFailedOrder' => [
                'type' => 'select',
                'field' => 'oxpayments__wdoxidee_delete_failed_order',
                'options' => [
                    '1' => Helper::translate('wd_yes'),
                    '0' => Helper::translate('wd_no'),
                ],
                'title' => Helper::translate('wd_config_delete_failure_order'),
                'description' => Helper::translate('wd_config_delete_failure_order_desc'),
            ],
            'shippingCountries' => [
                'type' => 'multiselect',
                'field' => 'oxpayments__shipping_countries',
                'options' => PaymentMethodHelper::getCountryOptions(),
                'title' => Helper::translate('wd_config_shipping_countries'),
                'description' => Helper::translate('wd_config_shipping_countries_desc'),
                'required' => true,
            ],
            'billingCountries' => [
                'type' => 'multiselect',
                'field' => 'oxpayments__billing_countries',
                'options' => PaymentMethodHelper::getCountryOptions(),
                'title' => Helper::translate('wd_config_billing_countries'),
                'description' => Helper::translate('wd_config_billing_countries_desc'),
                'required' => true,
            ],
            'billingShipping' => [
                'type' => 'select',
                'field' => 'oxpayments__billing_shipping',
                'options' => [
                    '1' => Helper::translate('wd_yes'),
                    '0' => Helper::translate('wd_no'),
                ],
                'title' => Helper::translate('wd_config_billing_shipping'),
                'description' => Helper::translate('wd_config_billing_shipping_desc'),
            ],
            'allowedCurrencies' => [
                'type' => 'multiselect',
                'field' => 'oxpayments__allowed_currencies',
                'options' => PaymentMethodHelper::getCurrencyOptions(),
                'title' => Helper::translate('wd_config_allowed_currencies'),
                'description' => Helper::translate('wd_config_allowed_currencies_desc'),
                'required' => true,
            ],
            'apiUrl' => [
                'type' => 'text',
                'field' => 'oxpayments__wdoxidee_apiurl',
                'title' => Helper::translate('wd_config_base_url'),
                'description' => Helper::translate('wd_config_base_url_desc'),
                'required' => true,
            ],
        ];
        return $aConfigFields + $this->_getCustomCurrencyConfigFields();
    }

    /**
     * Returns an array of all custom config fields per activated currency.
     * One separator, HTTP user/password and MAID and secret fields are added per currency.
     *
     * @return array
     *
     * @since 1.2.0
     */
    private function _getCustomCurrencyConfigFields()
    {
        $aCurrencyFields = [];

        $aCurrencies = $this->_oPayment->oxpayments__allowed_currencies->value;

        // fields that are configurable per currency
        $aFields = [
            'httpUser_%s' => [
                'type' => 'text',
                'field' => 'oxpayments__httpuser_%s',
                'title' => Helper::translate('wd_config_http_user'),
            ],
            'httpPassword_%s' => [
                'type' => 'text',
                'field' => 'oxpayments__httppass_%s',
                'title' => Helper::translate('wd_config_http_password'),
            ],
            'maid_%s' => [
                'type' => 'text',
                'field' => 'oxpayments__maid_%s',
                'title' => Helper::translate('wd_config_merchant_account_id'),
                'description' => Helper::translate('wd_config_merchant_account_id_desc'),
            ],
            'secret_%s' => [
                'type' => 'text',
                'field' => 'oxpayments__secret_%s',
                'title' => Helper::translate('wd_config_merchant_secret'),
                'description' => Helper::translate('wd_config_merchant_secret_desc'),
            ],
         ];

        foreach ($aCurrencies as $sCurrency) {
            $aCurrencyFields['groupSeparator_' . strtolower($sCurrency)] = [
                'type' => 'separator',
                'title' => $sCurrency,
            ];

            foreach ($aFields as $sFieldName => $aConfigProps) {
                $sConfigKey = sprintf($sFieldName, strtolower($sCurrency));
                $aFieldProps = [
                    'type' => $aConfigProps['type'],
                    'field' => sprintf($aConfigProps['field'], strtolower($sCurrency)),
                    'title' => $aConfigProps['title'],
                ];

                if (isset($aConfigProps['description'])) {
                    $aFieldProps['description'] = $aConfigProps['description'];
                }

                $aCurrencyFields[$sConfigKey] = $aFieldProps;
            }

            $aCurrencyFields['testCredentials_' . strtolower($sCurrency)] = [
                'type' => 'button',
                'onclick' => 'wdTestPaymentMethodCredentials(\'' . $sCurrency . '\')',
                'text' => Helper::translate('wd_test_credentials'),
                'colspan' => '2',
            ];
        }

        return $aCurrencyFields;
    }

    /**
     * @inheritdoc
     *
     * @return array
     *
     * @since 1.2.0
     */
    public function getPublicFieldNames()
    {
        return array_merge(
            parent::getPublicFieldNames(),
            [
                'descriptor',
                'additionalInfo',
                'deleteCanceledOrder',
                'deleteFailedOrder',
                'shippingCountries',
                'billingCountries',
                'billingShipping',
            ]
        );
    }

    /**
     * @inheritdoc
     *
     * @return array
     *
     * @since 1.2.0
     */
    public function getMetaDataFieldNames()
    {
        $aReturn = [
            'allowed_currencies',
            'shipping_countries',
            'billing_countries',
            'billing_shipping',
        ];

        $aCurrencies = PaymentMethodHelper::getCurrencyOptions();
        $aFieldNames = ['httpuser', 'httppass', 'maid', 'secret'];

        foreach ($aCurrencies as $sCurrency) {
            foreach ($aFieldNames as $sFieldName) {
                $aReturn[] = $sFieldName . '_' . strtolower($sCurrency);
            }
        }

        return $aReturn;
    }
}
