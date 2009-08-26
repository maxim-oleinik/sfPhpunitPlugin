<?php
/**
 * Base class for sfPhpunit tasks which generate test files from templates.
 *
 * @package    sfPhpunitPlugin
 * @subpackage task

 * @author     Pablo Godel <pgodel@gmail.com>
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfPhpunitCreateBaseTask extends sfBaseTask
{
	/**
	 * Creates a file from a template
	 *
	 * @param array $fileName
	 * @param string $templateName
	 * @param array $vars
	 * @param array $options the task options
	 *
	 * @return bool
	 *
	 * @throws sfCommandException
	 */
	protected function createFile($fileName, $templateName, array $vars, array $options)
	{
		if (!$options['overwrite'] && file_exists($fileName))
		{
			if ($options['verbose'])
			{
				$this->logSection('phpunit', sprintf('Skipped existing file %s', $fileName));
			}
			return false;
		}

		$tpl = $this->getTemplate($templateName);

		if (empty($tpl))
		{
			throw new sfCommandException(sprintf('%s template file is empty.', $templateFile));
		}

		if (!$fp = fopen($fileName, 'w'))
		{
			throw new sfCommandException(sprintf('Failed to open %s for writing', basename($fileName)));
		}

		fputs($fp, $this->renderTemplate($tpl, $vars));

		fclose($fp);

		return true;
	}

	/**
	 * Returns the (raw) content of a template
	 *
	 * @param string $templateName
	 * @return string the file content
	 */
	protected function getTemplate($templateName)
	{
		// Try to load the template from the project data dir.
		// If it does not exist, fall back to the plugin data dir.
		$templateBaseName = DIRECTORY_SEPARATOR.$templateName.'.tpl';
		$templateFile = $this->getTemplateDir(false).$templateBaseName;
		if (!file_exists($templateFile))
		{
			$templateFile = $this->getTemplateDir().$templateBaseName;
		}

		return file_get_contents($templateFile);
	}

	/**
	 * Renders a template content and assembles the variable placeholders
	 *
	 * @param string $content the template content
	 * @param array $vars
	 *
	 * @return string the assembled content
	 */
	protected function renderTemplate($content, array $vars)
	{
		foreach($vars as $key => $val)
		{
			$content = str_replace('{'.$key.'}', $val, $content);
		}

		return $content;
	}

	/**
	 * Creates the base class for a unit/functional test
	 *
	 * @param array $options
	 * @param string $templateName
	 * @param string $targetName
	 * @return bool
	 */
	protected function createBaseTestCaseClassFile(array $options, $templateName, $targetName)
	{
		$fname = sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'phpunit'.DIRECTORY_SEPARATOR.$targetName.'.class.php';

		// skipping generation of base test makes only sense, when file already exists
		if($options['skip_base_test'] && file_exists($fname))
		{
			return true;
		}

		$vars = array(
			'application' => $options['application'],
			'env' => $options['env'],
			'baseTestClassName' => $targetName
		);

		if ($this->createFile($fname, $templateName, $vars, $options))
		{
			$this->logSection('phpunit', sprintf('Created %s file.', $fname));
			return true;
		}

		return false;
	}

	/**
	 * Generated the "AllTests" file for PHPUnit
	 *
	 * @param array $options
	 * @return bool
	 */
	protected function createAllTestFile(array $options)
	{
		$fname = $options['alltests'];

		if ($fname[0] != DIRECTORY_SEPARATOR)
		{
			$fname = sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'phpunit'.DIRECTORY_SEPARATOR.$fname;
		}

		// skipping the alltests generation makes only sense, when the file already exists
		if ($options['skip_alltests'] && file_exists($fname))
		{
			return true;
		}

		$vars = array(
    'application' => $options['application'],
    'env' => $options['env'],
    'target' => '',
    'connection' => $options['connection'],
		);

		if ($this->createFile($fname, 'alltests', $vars, $options))
		{
			$this->logSection('phpunit', sprintf('Created %s file.', $fname));
		}

		return true;
	}

	/**
	 * Creates the bootstrap file for unit testing from a template file
	 *
	 * @param string $type could be unit or functional
	 * @param array $options the task options
	 *
	 * @return bool
	 */
	protected function createBootstrapFile($type, array $options)
	{
		$bootstrapDir = sfConfig::get('sf_test_dir').DIRECTORY_SEPARATOR.'phpunit'.DIRECTORY_SEPARATOR.'bootstrap';
		if (!file_exists($bootstrapDir))
		{
			mkdir($bootstrapDir);
		}
		$templateName = $type.DIRECTORY_SEPARATOR.$type;
		$target = $bootstrapDir.DIRECTORY_SEPARATOR.$type.'.php';

		$vars = array('application' => $options['application']);
		return $this->createFile($target, $templateName, $vars, $options );
	}

	/**
	 * Returns the path to the template dir of either this plugin or the project
	 *
	 * @param bool $pluginDir flag if template dir of this plugin or the project should be calculated
	 *
	 * @return string
	 */
	protected function getTemplateDir($pluginDir = true)
	{
		if ($pluginDir)
		{
			return dirname(__FILE__).'/../../data';
		}

		return sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'sfPhpunitPlugin';
	}
}