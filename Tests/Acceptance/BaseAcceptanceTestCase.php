<?php
/**
 * Shop System Plugins:
 * - Terms of Use can be found under:
 * https://github.com/wirecard/oxid-ee/blob/master/_TERMS_OF_USE
 * - License can be found under:
 * https://github.com/wirecard/oxid-ee/blob/master/LICENSE
 */

namespace Wirecard\Oxid\Tests\Acceptance;

/**
 * Basic acceptance test class to be used by all acceptance tests.
 */
abstract class BaseAcceptanceTestCase extends \OxidEsales\TestingLibrary\AcceptanceTestCase
{
    private $config;
    private $locators;

    public function __construct($name = null, $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->config = require_once(__DIR__ . '/inc/config.php');
        $this->locators = require_once(__DIR__ . '/inc/locators.php');
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->activateTheme('azure');
    }

    /**
     * Do not fail tests on log messages.
     * @inheritdoc
     */
    protected function failOnLoggedExceptions()
    {
    }

    /**
     * Returns an value in an array by a path in dot notation (e.g. "a.b.c").
     * @param array $array
     * @param string $path
     * @return mixed
     */
    private function getArrayValueByPath($array, $path)
    {
        $value = $array;

        foreach (explode('.', $path) as $pathPart) {
            if (!isset($value[$pathPart])) {
                return null;
            }

            $value = $value[$pathPart];
        }

        return $value;
    }

    /**
     * Returns a config value by path.
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->getArrayValueByPath($this->config, $path);
    }

    /**
     * Returns a locator by path.
     * @param string $path
     * @return mixed
     */
    public function getLocator($path)
    {
        return $this->getArrayValueByPath($this->locators, $path);
    }
}
