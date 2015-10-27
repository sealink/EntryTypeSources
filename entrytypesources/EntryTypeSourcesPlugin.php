<?php
namespace Craft;

class EntryTypeSourcesPlugin extends BasePlugin
{
	function getName()
	{
		return Craft::t('Entry Type Sources');
	}

	function getVersion()
	{
		return '1.0';
	}

	function getDeveloper()
	{
		return 'JB @ R15';
		// i'm sure Bob Olde Hampsink will offer a rewrite
	}

	function getDeveloperUrl()
	{
		return 'http://reactor15.com';
	}

	protected function defineSettings()
	{
		return array(
			'sections' => array(AttributeType::Mixed, 'default' => array()),
		);
	}

	public function getSettingsHtml()
	{
		$groups = craft()->sections->getAllSections();
		$selectedGroups = $this->getSettings()->sections;
		$groupOptions = array();

		foreach ($groups as $group)
		{
			$groupOptions[] = array('label' => $group->name, 'value' => $group->id);
		}

		return craft()->templates->renderMacro('_includes/forms', 'checkboxSelectField', array(
			array(
				'first' => true,
				'label' => 'Choose Sections',
				'instructions' => 'Choose which sections should be included in entry type source lists.',
				'name' => 'sections',
				'options' => $groupOptions,
				'values' => $selectedGroups,
			)
		));
	}

	public function prepSettings($settings)
	{
		if ($settings['sections'] == '*')
		{
			unset($settings['sections']);
		}

		return $settings;
	}

	public function modifyEntrySources(&$sources, $context)
	{
		$selectedSections = $this->getSettings()->sections;

		if (!$selectedSections)
		{
			$selectedSections = craft()->sections->getAllSectionIds();
		}

		foreach ($selectedSections as $sectionId)
		{

			$section = craft()->sections->getSectionById($sectionId);

			$sources[] = array('heading' => Craft::t($section->name));

			foreach ($section->getEntryTypes() as $entryType)
			{

				$levelSources = &$sources;

				$levelSources['type:'.$entryType->id] = array(
					'label' => $entryType->name,
					'criteria' => array('type' => $entryType->id)
				);

			}
		}
	}
}
