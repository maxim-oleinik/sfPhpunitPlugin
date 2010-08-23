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
     * Doctrine\ORM\EntityManager
     */
    protected $em;


    /**
     * Construct
     *
     * @param  Doctrine\ORM\EntityManager $em
     */
    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * Get entity manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }


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
        foreach ($props as $name => $value) {
            $setter = 'set' . $name;
            $model->$setter($value);
        }

        $this->em->persist($model);
        if ($save) {
            $this->em->flush();
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
     * Make array from Doctrine object suitable for supplied form
     *
     * @param  Doctrine_Record $model
     * @param  sfFormDoctrine  $form
     * @return array
     */
    public function extractFormData($model, sfFormDoctrine $form)
    {
        $fields = $form->getWidgetSchema()->getFields();

        $props = array();
        $reflFields = $this->getEntityManager()->getClassMetadata(get_class($model))->reflFields;
        foreach ($reflFields as $name => $reflField) {
            $getter = 'get'.$name;

            if (method_exists($model, $getter)) {
                $value = $model->$getter();

                if (!is_object($value)) {
                    $props[$name] = $value;
                }
            }
        }

        return array_intersect_key($props, $fields);
    }

}
