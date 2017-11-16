<?php


class CalendarLocationsEventExtension extends DataExtension {

    private static $has_one = array(
		'Location' => 'EventLocation'
	);
    
    public function updateCMSFields(FieldList $fields)
    {
        // Add tab
		$fields->findOrMakeTab(
			'Root.Location', _t('CalendarLocationsEventExtension.LocationTab','Location')
		);
        
        $source = function() {
            return EventLocation::get()->map()->toArray();
        };
        
        $locationField = DropdownField::create('LocationID', _t('CalendarLocationsEventExtension.LocationField', 'Select location'), $source())
            ->setHasEmptyDefault(true)
            ->useAddNew('EventLocation',$source);
		$fields->addFieldToTab('Root.Location',$locationField);
  
    }

}
