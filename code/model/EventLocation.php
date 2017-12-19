<?php


class EventLocation extends DataObject
{
 
    private static $db = array(
        'Title' => 'Varchar(200)',
        'Description' => 'HTMLText',
        'TimeZone' => 'Varchar(100)'
    );
    
    private static $has_many = array(
        'Events' => 'Event'
    );
    
    private static $many_many = array(
        'Images' => 'Image'
    );
    
    private static $casting = array(
		'FullAddressString' => 'Text'
	);
    
    private static $summary_fields = array(
        'Title',
        'City',
        'State',
        'Country',
        'UsageCount'
    );
    
    /*
	 * -------------------------------------------------------------------------
	 * Admin methods
	 * -------------------------------------------------------------------------
	 */
    public function getCMSFields()
    {
        
        $fields = FieldList::create();
        $fields->push(TabSet::create("Root", $mainTab = Tab::create("Main")));
        
        /*
         * Main tab
         */
        // Title
        $fields->addFieldsToTab('Root.Main',array(
            TextField::create('Title',_t('EventLocation.Title','Title')),
            HtmlEditorField::create('Description',_t('EventLocation.Description','Description'))
        ));
       
        /*
         * Usage tab
         */
        $fields->findOrMakeTab('Root.UsageTab',_t('EventLocation.UsageTab','Usage'));
        $eventsList = array();
        foreach($this->Events() as $event) {
            $eventsList[] = $event->ID .' '. $event->StartDateTime .' '. $event->Title;
        }
        $fields->addFieldToTab('Root.UsageTab',
            ReadonlyField::create('EventsList',_t('EventLocation.Events','Events'))->setValue(implode("\n",$eventsList))
        );
        
        $this->extend('updateCMSFields', $fields);
        
        return $fields;
	}
    
    public function getAddNewFields() {
        $fields = $this->scaffoldFormFields(array(
			'includeRelations' => false,
			'tabbed' => false,
			'ajaxSafe' => true
		));
		
        $fields->removeByName('Description');
        $fields->removeByName('TimeZone');
        
        /** @todo
        $tzField = TimeZoneField::create('TimeZone', _t('EventLocation.TimeZone'));
		$fields->replaceField('TimeZone',$tzField);
        */
        return $fields;
	}
    
    public function singular_name()
	{
		return _t('EventLocation.SINGULARNAME', 'Location');
	}

	public function plural_name()
	{
		return _t('EventLocation.PLURALNAME', 'Locations');
	}
    
    public function fieldLabels($includeRelations = true)
	{
		return array_merge(
			parent::fieldLabels($includeRelations),
			array(
				'Title' => _t('EventLocation.Title', 'Title'),
				'Description' => _t('EventLocation.Description', 'Description'),
				'Address' => _t('EventLocation.Address', 'Address'),
				'City' => _t('EventLocation.City', 'Suburb / city'),
				'State' => _t('EventLocation.State', 'State / province'),
				'Postcode' => _t('EventLocation.Postcode', 'Post code'),
                'Country' => _t('EventLocation.Country', 'Country'),
                'TimeZone' => _t('EventLocation.TimeZone', 'Disabled'),
                'Phone' => _t('EventLocation.Phone', 'Phone'),
                'UsageCount' => _t('EventLocation.UsageCount', 'Usage count')
			)
		);
	}
    
    public function UsageCount()
    {
        return $this->Events()->count();
    }
    
}

