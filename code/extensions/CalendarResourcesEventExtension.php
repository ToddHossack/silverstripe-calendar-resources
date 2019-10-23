<?php


class CalendarResourcesEventExtension extends DataExtension {

    private static $has_many = array(
		'Resources' => 'EventResource'
	);
    
    public function updateCMSFields(FieldList $fields)
    {
        // Add tab
		$fields->findOrMakeTab(
			'Root.Resources', _t('CalendarResourcesEventExtension.ResourcesTab','Resources')
		);
        if($this->owner->exists()) {
            $config = GridFieldConfig_RecordEditor::create();
            $fields->addFieldToTab('Root.Resources',GridField::create('Resources',
			_t('CalendarResourcesEventExtension.Resources', 'Resources'),
			$this->owner->Resources(),
			$config));
		} else {
            $fields->addFieldToTab('Root.Resources', LiteralField::create('NotSaved', "<p class='message warning'>"._t('CalendarResourcesEventExtension.AddResourcesAfterSaving', 'Links to resources may be added after the event has been saved  for the first time.').'</p>'));
        }
    }

}
