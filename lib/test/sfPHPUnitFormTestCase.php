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
    protected function getValidationTestingPlan()
    {
        return array();
    }


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

        $actual = array_keys(array_merge(
            $form->getWidgetSchema()->getFields(),
            $form->getEmbeddedForms()
        ));
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
    protected function assertFormHasErrors(sfForm $form, $errorsCount, $message = null)
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
            $message = $this->makeErrorMess($form, $message."Expected error `{$expectedError}` for field `{$field}");
            $this->assertErrorSchemaError($form->getErrorSchema(), $field, $expectedError, $message);
        }
    }


    /**
     * Assert error schema error
     *
     * @param sfValidatorErrorSchema $errors
     * @param string $field         - Form field name
     * @param string $expectedError - Expected error, "required"
     * @param string $message
     */
    protected function assertErrorSchemaError(sfValidatorErrorSchema $errors, $field, $expectedError, $message = null)
    {
        $this->assertTrue(isset($errors[$field]), $message);
        $error = $errors[$field]->getCode();
        $this->assertEquals($expectedError, $error, $message);
    }


    /**
     * Check form with test plan
     *
     * @param sfForm $form
     * @param array  $plan
     *      fieldName => array(
     *          array($result = true/false, $inputValue),
     *      )
     * @param array  $input
     * @param string $errorCode
     * @param string $message
     */
    protected function checkFormWithPlan(sfForm $form, array $plan, array $input, $errorCode, $message)
    {
        foreach ($plan as $fieldName => $data) {
            foreach ($data as $row) {
                list($success, $inputValue) = $row;

                $testInput = $input;
                $testInput[$fieldName] = $inputValue;
                $errorMessage = sprintf("%s (with input: %s)", $message, var_export($inputValue, true));

                $error = $success ? false : $errorCode;
                $this->checkFormFieldValidation($form, $testInput, $fieldName, $error, $errorMessage);
            }
        }
    }


    /**
     * Check form field validation
     *
     * @param  sfForm $form
     * @param  array  $input
     * @param  string $fieldName
     * @param  string $errorCode
     * @param  string $message
     * @return array  clean values
     */
    protected function checkFormFieldValidation(sfForm $form, array $input, $fieldName, $errorCode, $message)
    {
        $expectedErrors = !$errorCode ? array() : array($fieldName => $errorCode);
        return $this->checkValidatorSchema($form->getValidatorSchema(), $input, $expectedErrors, $message);
    }


    /**
     * Check validator schema
     */
    protected function checkValidatorSchema(sfValidatorSchema $schema, array $input, array $expectedErrors, $message)
    {
        try {
            $result = $schema->clean($input);
            $this->assertSame(array(), $expectedErrors, $this->makeErrorMess2(null, $input, "Expected from HAS errors\n".$message));
            return $result;

        } catch (sfValidatorErrorSchema $e) {
            $this->assertGreaterThan(0, count($expectedErrors), $this->makeErrorMess2($e, $input, "Expected from has NO errors\n".$message));
            $this->assertEquals(count($expectedErrors), $e->count(), $this->makeErrorMess2($e, $input, $message));

            foreach ($expectedErrors as $field => $expectedError) {
                $message = $this->makeErrorMess2($e, $input, $message."\nExpected error `{$expectedError}` for field `{$field}");
                $this->assertErrorSchemaError($e, $field, $expectedError, $message);
            }
        }
    }


    /**
     * Make error message
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
     * Make error message - TMP
     */
    protected function makeErrorMess2(sfValidatorErrorSchema $e = null, array $input, $message)
    {
        return sprintf("%s\n\nErrors: %s\n\nInput:\n%s",
            $message,
            $e,
            var_export($input, true)
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

        foreach ($this->form->getEmbeddedForms() as $name => $eForm) {
            if (isset($input[$name])) {
                unset($input[$name]);
            }
        }

        return $input;
    }


    /**
     * Is given field name is embeded form
     *
     * @param  string $fieldName
     * @return bool
     */
    protected function isEmbeddedForm($fieldName)
    {
        $embedded = $this->form->getEmbeddedForms();
        return $embedded && isset($embedded[$fieldName]);
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

            if (!$this->isEmbeddedForm($fieldName) && !isset($requirements['required'])) {
                $requirements['required'] = false;
            }

            foreach ($requirements as $errorCode => $value) {

                $testName = "{$fieldName}: {$errorCode}";
                $form = $this->makeForm();
                $form->getValidatorSchema()->setPostValidator(new sfValidatorPass());


                switch ($errorCode) {

                    # Required
                    case 'required':
                        $input = $this->getValidInput();
                        unset($input[$fieldName]);
                        $plan = array($fieldName => array('' => !$value));
                        $error = $value ? $errorCode : false;
                        $this->checkFormFieldValidation($form, $input, $fieldName, $error, $testName);
                        break;

                    # Min Length
                    case 'min_length':
                        if (!is_array($value)) {
                            $plan = array(
                                array(false, str_repeat('z', $value - 1)), // min - 1
                                array(true,  str_repeat('z', $value)),     // min
                            );
                        } else {
                            $plan = array();
                            foreach ($value as $inputValue => $success) {
                                $plan[] = array($success, $inputValue);
                            }
                        }
                        $plan = array($fieldName => $plan);
                        $this->checkFormWithPlan($form, $plan, $this->getValidInput(), $errorCode, $testName);
                        break;

                    # Max Length
                    case 'max_length':
                        if (!is_array($value)) {
                            $plan = array(
                                array(true,  str_repeat('z', $value)),     // max
                                array(false, str_repeat('z', $value + 1)), // max + 1
                            );
                        } else {
                            $plan = array();
                            foreach ($value as $inputValue => $success) {
                                $plan[] = array($success, $inputValue);
                            }
                        }
                        $plan = array($fieldName => $plan);
                        $this->checkFormWithPlan($form, $plan, $this->getValidInput(), $errorCode, $testName);
                        break;

                    # Min
                    case 'min':
                        $plan = array($fieldName => array(
                            array(true,  $value),
                            array(false, $value-1),
                        ));
                        $this->checkFormWithPlan($form, $plan, $this->getValidInput(), $errorCode, $testName);
                        break;

                    # Max
                    case 'max':
                        $plan = array($fieldName => array(
                            array(true,  $value),
                            array(false, $value+1),
                        ));
                        $this->checkFormWithPlan($form, $plan, $this->getValidInput(), $errorCode, $testName);
                        break;

                    # Invalid
                    case 'invalid':
                        $plan = array();
                        foreach ($value as $inputString) {
                            $plan[] = array(false, $inputString);
                        }
                        $plan = array($fieldName => $plan);
                        $this->checkFormWithPlan($form, $plan, $this->getValidInput(), $errorCode, $testName);
                        break;

                    # Success
                    case 'success':
                        $input = $this->getValidInput();
                        foreach ($value as $inputString ) {
                            $input[$fieldName] = $inputString;
                            $errorMessage = "{$testName} ({$inputString})";

                            $form->bind($input, array());
                            $this->assertFormIsValid($form, $errorMessage);
                        }
                        break;

                    # Trim
                    case 'trim':
                        $input = $this->getValidInput();
                        $expectedString = $input[$fieldName];
                        $input[$fieldName] = " {$expectedString} ";
                        $errorMessage = "{$testName} ({$input[$fieldName]})";

                        $actual = $this->checkFormFieldValidation($form, $input, $fieldName, false, $testName);
                        $this->assertEquals($expectedString, $actual[$fieldName], $errorMessage);
                        break;

                    # Trim
                    case 'transform':
                        $input = $this->getValidInput();
                        foreach ($value as $origin => $expected) {
                            $input[$fieldName] = $origin;
                            $errorMessage = "{$testName} ({$input[$fieldName]})";

                            $actual = $this->checkFormFieldValidation($form, $input, $fieldName, false, $testName);
                            $this->assertEquals($expected, $actual[$fieldName], $errorMessage);
                        }
                        break;

                    # InstanceOf
                    case 'instanceof':
                        $errorMessage = "{$testName} ({$value})";
                        $this->assertInstanceOf($value, $form->getValidatorSchema()->offsetGet($fieldName), $errorMessage);
                        break;

                    # Embed forms
                    case 'embed':
                        $errorMessage = "{$testName} ({$value})";
                        $this->assertInstanceOf($value, $form->getEmbeddedForm($fieldName), $errorMessage);
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
                $this->assertFormHasErrors($form, $item->getErrorsCount(), $name);
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

        if ($this->saveForm && $this->form instanceof sfFormDoctrine) {
            $object = $this->form->save();
            $this->assertEquals(1, $this->queryFind(get_class($object), $this->cleanInput($input))->count(), 'Expected found 1 object');
        }

        return $input;
    }

}
