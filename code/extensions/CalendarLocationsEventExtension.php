<?php


class CalendarLocationsEventExtension extends DataExtension {

	private static $db = array(
		
	);

    private static $has_one = array(
		'Location' => 'EventLocation'
	);
    
    public function updateCMSFields(FieldList $fields)
    {

		

    }

}
