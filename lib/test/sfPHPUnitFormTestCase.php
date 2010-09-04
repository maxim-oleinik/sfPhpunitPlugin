<?php
/**
 * Base TestCase for testing forms
 * Contains auto tests
 *
 * @package    sfPhpunitPlugin
 * @author     Maxim Oleinik <maxim.oleinik@gmail.com>
 */


/**
 * Form validation test item
 *
 * @see sfPHPUnitFormTestCase::getValidationTestingPlan()
 */
class sfPHPUnitFormValidationItem
{
    private
        $_input,
        $_errors,
        $_errorsCount;


    /**
     * Construct
     *
     * @param array $input          - "name" => "My Name"
     * @param array $expectedErrors - "name" => "invalid"
     * @return void
     */
    public function __construct(array $input, array $expectedErrors = array())
    {
        $this->_input  = $input;
        $this->_errors = $expectedErrors;
        $this->_errorsCount = count($expectedErrors);
    }


    /**
     * Get incoming data
     *
     * @return array
     */
    public function getInput()
    {
        return $this->_input;
    }


    /**
     * Get list of expected errors
     *
     * @return array
     */
    public function getExpectedErrors()
    {
        return $this->_errors;
    }


    /**
     * Count expected errors
     *
     * @return int
     */
    public function getErrorsCount()
    {
        return $this->_errorsCount;
    }

}


/**
 * Form tester
 */
abstract class sfPHPUnitFormTestCase extends myUnitTestCase
{
    /**
     * Test form saving
     */
    protected $saveForm = true;

    /**
     * Test form
     */
    protected $form;


    // Fixtures
    // -------------------------------------------------------------------------

    /**
     * SetUp
     */
    public function setUp()
    {
        parent::setup();
        $this->form = $this->makeForm();
    }


    /**
     * Create form
     *
     * Example:
     *   return new ArticleForm;
     *
     * @return sfForm
     */
    abstract protected function makeForm();


    /**
     * Get expected form fields (use fields)
     *
     * @return array
     *   'id' => null,
     *   "title" => array(
     *       'min_length'  => 5,
     *       'min_length'  => array('1234' => false, '12345' => true),
     *       'max_length'  => 255,
     *       'max_length'  => array(str_repeat('1', 50) => true, str_repeat('1', 51) => false),
     *       'trim'        => true,
     *       'required'    => true,
     *       'invalid'     => array('invalid value 1', 'invalid value 2'),
     *       'success'     => array('success value 1', 'success value 2'),
     *   ),
     *   '_csrf_token' => array(
     *       'required'    => true,
     *   ),
     */
    abstract protected function getFields();


    /**
     * Form validation testing plan
     *
     * Example:
     *   return array(
     *       'Empty request' => new myFormValidationTestItem(
     *           array(),
     *           array(
     *               'title'       => 'required',
     *               'description' => 'required',
     *               '_csrf_token' => 'required',
     *           )),
     *
     *       'No errors' => new myFormValidationTestItem(
     *           $this->makeInput(array('title' => 'My Article'))
     *           ),
     *   );
     *
     * @return array sfPHPUnitFormValidationItem
     */
    abstract protected function getValidationTestingPlan();


    /**
     * Get valid data
     *
     * @return array - array("title" => "My title")
     */
    abstract protected function getValidData();


    /**
     * Get valid input with csrf token
     *
     * @return array
     */
    protected function getValidInput()
    {
        return $this->makeInput($this->getValidData());
    }


    /**
     * Add CSRF token to input
     *
     * @param  array $input
     * @return array
     */
    protected function makeInput(array $input)
    {
        if ($this->form->isCsrfProtected()) {
            $input[$this->form->getCsrfFieldName()] = $this->form->getCSRFtoken();
        }
        return $input;
    }


    // Assertions
    // -------------------------------------------------------------------------


    /**
     * Assert form fields
     *
     * @param  array  $expected - array(name, title)
     * @param  sfForm $form
     * @param  string $message
     */
    protected function assertFormFields($expected, sfForm $form, $message = null)
    {
        sort($expected);

        $actual = array_keys($form->getWidgetSchema()->getFields());
        sort($actual);

        $this->assertEquals($expected, $actual, $message);
    }


    /**
     * Assert form is valid
     *
     * @param sfForm $form
     * @param string $message
     */
    protected function assertFormIsValid(sfForm $form, $message = null)
    {
        $message = $message ? $message.PHP_EOL : null;

        if ($form->hasErrors()) {
            $this->fail($this->makeErrorMess($form, $message.'Expected form is valid'));
        }
    }


    /**
     * Assert form has erros
     *
     * @param sfForm $form
     * @param int    $errorsCount
     * @param string $message
     */
    protected function assertFormHasErros(sfForm $form, $errorsCount, $message = null)
    {
        $message = $message ? $message.PHP_EOL : null;

        if (!$form->hasErrors()) {
            $this->fail($this->makeErrorMess($form, $message.'Expected form HAS errors'));
        }
        $this->assertEquals((int)$errorsCount, $form->getErrorSchema()->count(), $this->makeErrorMess($form, $message.'Errors count'));
    }


    /**
     * Assert form error
     *
     * @param sfForm $form
     * @param string $field         - Form field name
     * @param string $expectedError - Expected error, "required"
     * @param string $message
     */
    protected function assertFormError(sfForm $form, $field, $expectedError, $message = null)
    {
        $message = $message ? $message.PHP_EOL : null;

        // Global error
        if (!$field) {
            $this->assertTrue($form->hasGlobalErrors(), $this->makeErrorMess($form, $message."Expected form HAS global errors"));
            $errors = implode('; ', $form->getGlobalErrors());
            $this->assertContains($expectedError, $errors, $this->makeErrorMess($form, $message."Global errors contains next text"));
        } else {
            $errors = $form->getErrorSchema();
            $this->assertTrue(isset($errors[$field]), $this->makeErrorMess($form, $message."Expected field `{$field}` HAS error"));
            $error = $errors[$field]->getCode();
            $this->assertEquals($expectedError, $error, $this->makeErrorMess($form, $message."Expected error `{$expectedError}` for field `{$field}`"));
        }
    }


    /**
     * Make error messge
     *
     * Display incoming data and errors list
     *
     * @param sfForm $form
     * @param string $message
     */
    protected function makeErrorMess(sfForm $form, $message)
    {
        return sprintf("%s\n\nErrors: %s\n\nInput:\n%s",
            $message,
            $form->getErrorSchema(),
            var_export($form->getTaintedValues(), true)
        );
    }


    /**
     * Clean input to search in database
     *
     * @param  array $input
     * @return array
     */
    protected function cleanInput(array $input)
    {
        if (isset($input[$this->form->getCsrfFieldName()])) {
            unset($input[$this->form->getCsrfFieldName()]);
        }

        return $input;
    }


    // Tests
    // -------------------------------------------------------------------------


    /**
     * Check form fields
     *
     * @see getFields()
     */
    public function testAutoFields()
    {
        $this->assertFormFields(array_keys($this->getFields()), $this->form, get_class($this->form));

        // Check getValidData
        $this->assertFormFields(array_keys($this->getValidInput()), $this->form,
            "Expected getValidData() returns all avaliable fields");
    }


    /**
     * Check each form item basic requirements
     *
     * @see getFields()
     */
    public function testAutoRequirements()
    {
        foreach ($this->getFields() as $fieldName => $requirements) {

            if (!$requirements) {
                $requirements = array();
            }
            if (!isset($requirements['required'])) {
                $requirements['required'] = false;
            }

            foreach ($requirements as $errorCode => $value) {

                $testName = "{$fieldName}: {$errorCode}";
                $form = $this->makeForm();

                switch ($errorCode) {

                    # Required
                    case 'required':
                        $input = $this->getValidInput();
                        unset($input[$fieldName]);
                        if ($value) {
                            $form->bind($input);
                            $this->assertFormHasErros($form, 1, $testName);
                            $this->assertFormError($form, $fieldName, $errorCode, $testName);
                        } else {
                            $form->bind($input);
                            $this->assertFormIsValid($form, $testName);
                        }
                        break;

                    # Min Length
                    case 'min_length':
                        if (!is_array($value)) {
                            $plan = array(
                                str_repeat('я', $value - 1) => false, // min - 1
                                str_repeat('я', $value)     => true,  // min
                            );
                        } else {
                            $plan = $value;
                        }
                        $input = $this->getValidInput();
                        foreach ($plan as $inputString => $success) {
                            $input[$fieldName] = $inputString;
                            $errorMessage = "{$testName} ({$inputString})";

                            $form->bind($input);
                            if ($success) {
                                $this->assertFormIsValid($form, $errorMessage);
                            } else {
                                $this->assertFormHasErros($form, 1, $errorMessage);
                                $this->assertFormError($form, $fieldName, $errorCode, $errorMessage);
                            }
                        }
                        break;

                    # Max Length
                    case 'max_length':
                        if (!is_array($value)) {
                            $plan = array(
                                str_repeat('я', $value)     => true,   // max
                                str_repeat('я', $value + 1) => false,  // max + 1
                            );
                        } else {
                            $plan = $value;
                        }
                        $input = $this->getValidInput();
                        foreach ($plan as $inputString => $success) {
                            $input[$fieldName] = $inputString;
                            $errorMessage = "{$testName} ({$inputString})";

                            $form->bind($input);
                            if ($success) {
                                $this->assertFormIsValid($form, $errorMessage);
                            } else {
                                $this->assertFormHasErros($form, 1, $errorMessage);
                                $this->assertFormError($form, $fieldName, $errorCode, $errorMessage);
                            }
                        }
                        break;

                    # Invalid
                    case 'invalid':
                        $input = $this->getValidInput();
                        foreach ($value as $inputString) {
                            $input[$fieldName] = $inputString;
                            $errorMessage = "{$testName} ({$inputString})";

                            $form->bind($input);
                            $this->assertFormHasErros($form, 1, $errorMessage);
                            $this->assertFormError($form, $fieldName, $errorCode, $errorMessage);
                        }
                        break;

                    # Success
                    case 'success':
                        $input = $this->getValidInput();
                        foreach ($value as $inputString ) {
                            $input[$fieldName] = $inputString;
                            $errorMessage = "{$testName} ({$inputString})";

                            $form->bind($input);
                            $this->assertFormIsValid($form, $errorMessage);
                        }
                        break;

                    # Trim
                    case 'trim':
                        $input = $this->getValidInput();
                        $expectedString = $input[$fieldName];
                        $input[$fieldName] = " {$expectedString} ";
                        $errorMessage = "{$testName} ({$input[$fieldName]})";

                        $form->bind($input);
                        $this->assertFormIsValid($form, $errorMessage);
                        $this->assertEquals($expectedString, $form->getValue($fieldName), $errorMessage);
                        break;

                    default:
                        throw new Exception(__METHOD__.": Unknown option or error code `{$errorCode}`");
                }
            }

        }
    }


    /**
     * Check validation
     *
     * @see getValidationTestingPlan()
     */
    public function testAutoValidation()
    {
        foreach ($this->getValidationTestingPlan() as $name => $item) {
            $form = $this->makeForm();
            $form->bind($input = $item->getInput(), array());

            // Valid
            if (!$item->getErrorsCount()) {
                $this->assertFormIsValid($form, $name);

                if ($this->saveForm && $this->form instanceof sfFormObject) {
                    $object = $form->save();
                    $this->assertEquals(1, $this->queryFind(get_class($object), $this->cleanInput($input))->count(), $name.PHP_EOL.'Expected found 1 object');
                }

            // Errors
            } else {
                $this->assertFormHasErros($form, $item->getErrorsCount(), $name);
                foreach ($item->getExpectedErrors() as $field => $error) {
                    $this->assertFormError($form, $field, $error, $name);
                }
            }

        }
    }


    /**
     * Check valid input
     *
     * @see getValidInput()
     */
    public function testAutoFormIsValid()
    {
        $input = $this->getValidInput();
        $this->form->bind($input, array());
        $this->assertFormIsValid($this->form);

        if ($this->saveForm && $this->form instanceof sfFormObject) {
            $object = $this->form->save();

            $q = $this->queryFind(get_class($object), $this->cleanInput($input), 'a');
            $found = $q->execute(array(), \Doctrine\ORM\Query::HYDRATE_SCALAR);

            $this->assertEquals(1, count($found), 'Expected found 1 object');
        }
    }

}
