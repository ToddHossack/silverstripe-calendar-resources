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
        
        
    }

}
