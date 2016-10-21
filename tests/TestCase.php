<?php

namespace yii2tech\tests\unit\filestorage;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * Base class for the test cases.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array config params.
     */
    public static $params;

    /**
     * @var \yii\mongodb\Connection MongoDB connection instance.
     */
    protected $mongodb;


    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
        ], $config));
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return dirname(dirname(__DIR__)) . '/vendor';
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
    }

    /**
     * Returns a test configuration param from /data/config.php
     * @param string $name params name
     * @param mixed $default default value to use when param is not set.
     * @return mixed  the value of the configuration param
     */
    public static function getParam($name, $default = null)
    {
        if (static::$params === null) {
            static::$params = require(__DIR__ . '/data/config.php');
        }

        return isset(static::$params[$name]) ? static::$params[$name] : $default;
    }

    /**
     * @param boolean $reset whether to clean up the test database
     * @param boolean $open  whether to open test database
     * @return \yii\mongodb\Connection
     */
    public function getMongodb($reset = false, $open = true)
    {
        if (!$reset && $this->mongodb) {
            return $this->mongodb;
        }

        $config = self::getParam('mongodb');

        $db = new \yii\mongodb\Connection($config);

        $db->enableLogging = false;
        $db->enableProfiling = false;
        if ($open) {
            $db->open();
        }
        $this->mongodb = $db;

        return $db;
    }
}