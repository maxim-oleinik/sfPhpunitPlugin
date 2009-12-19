<?php

/**
 * Test helper for creating model objects
 *
 * @package    sfPhpunitPlugin
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class sfPHPUnitObjectHelper
{
    /**
     * Unique counter
     */
    protected static $_counter = 0;


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
     * Create model object
     *
     * @param  string $modelName - model class name
     * @param  array  $props     - model props
     * @param  bool   $save      - save object
     *
     * @return Doctrine_Record
     */
    public function makeModel($modelName, array $props = array(), $save = true)
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
    public function makeText($text, $xss = true)
    {
        $template = '%s %04d';
        if ($xss) {
            $template = "<span class=\"xss\">{$template}</span>";
        }

        return sprintf($template, $text, $this->getUniqueCounter());
    }

}
