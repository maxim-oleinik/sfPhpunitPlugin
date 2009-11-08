<?php

/**
 * Test helper for creating model objects
 */
class sfBaseTestObjectHelper
{
    /**
     * Singleton
     */
    protected static $_instance = null;
    protected function __construct() {}
    protected function __clone() {}


    /**
     * Get Instance
     *
     * @return myTestObjectHelper
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new myTestObjectHelper;
        }
        return self::$_instance;
    }


    /**
     * Get unique counter
     *
     * @return int
     */
    public function getUniqueCounter()
    {
        static $counter = 0;
        return ++$counter;
    }


    /**
     * Create model object
     *
     * @param  string $modelName - model class name
     * @param  bool   $save      - save object
     * @param  array  $props     - model props
     *
     * @return Doctrine_Record
     */
    public function makeModel($modelName, $save = false, array $props = array())
    {
        $model = new $modelName;
        $model->fromArray($props);

        if ($save) {
            $model->save();
        }

        return $model;
    }


    /**
     * Make unique text string
     *
     * Contains html-code
     *
     * @param  string $text
     * @return string
     */
    protected function _makeText($text)
    {
        return sprintf('%s %04d</html>', $text, $this->getUniqueCounter());
    }

}
