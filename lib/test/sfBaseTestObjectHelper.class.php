<?php

/**
 * Test helper for creating model objects
 *
 * @package    sfPhpunitPlugin
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class sfBaseTestObjectHelper
{
    /**
     * Unique counter
     */
    protected static $_counter = 0;

    /**
     * Safe classes
     */
    protected $_safe = array();


    /**
     * Get unique counter
     *
     * @return int
     */
    public function getUniqueCounter()
    {
        return ++self::$_counter;
    }


    /**
     * Init safe classes
     *
     * @param  array $safe
     * @return void
     */
    public function setSafe(array $safe)
    {
        $this->_safe = $safe;
    }


    /**
     * Is class safe
     *
     * @param  string $item
     * @return bool
     */
    public function isSafe($item)
    {
        return in_array($item, $this->_safe);
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
    public function makeText($text, $item = null)
    {
        $template = '%s %04d';
        if (!$item || !$this->isSafe($item)) {
            $template = "<span class=\"xss\">{$template}</span>";
        }

        return sprintf($template, $text, $this->getUniqueCounter());
    }

}
