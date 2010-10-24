<?php

/**
 * Test helper for creating model objects
 *
 * @package    sfPhpunitPlugin
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */
class sfPHPUnitObjectHelper
{
    const XSS_TOKEN = 'class="sf_phpunit_xss_tocken"';


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
            $template = '<span '.self::XSS_TOKEN.'>'.$template.'</span>';
        }

        return sprintf($template, $text, $this->getUniqueCounter());
    }


    /**
     * Make unique email
     *
     * @return string
     */
    public function makeEmail()
    {
        return $this->getUniqueCounter() . 'test.email@example.org';
    }


    /**
     * Make array from Doctrine object suitable for supplied form
     *
     * @param  BaseObject $model
     * @param  sfForm     $form
     * @return array
     */
    public static function extractFormData(BaseObject $model, sfForm $form)
    {
        $fields = $form->getWidgetSchema()->getFields();
        $props  = $model->toArray(BasePeer::TYPE_FIELDNAME, false);
        return array_intersect_key($props, $fields);
    }

}
