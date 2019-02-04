<?php


class EventResource extends DataObject
{
 
    private static $db = array(
        'Title' => 'Varchar(200)',
        'Summary' => 'Text',
        'Type' => "Enum('InternalPage,InternalFile,ExternalURL','ExternalURL')",
        'ExternalURL' => 'Varchar(2083)',
    );
    
    private static $has_one = array(
        'Event' => 'Event',
        'InternalPage' => 'SiteTree',
        'InternalFile' => 'File'
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
            TextField::create('Title',_t('EventResource.Title','Title')),
            TextAreaField::create('Summary',_t('EventResource.Summary','Summary'))->setRows(2)
        ));

       
        // Type 
        $typeOptions = $this->dbObject('Type')->enumValues();
		foreach($typeOptions as $k => $v) {
			$typeOptions[$k] = _t('EventResource.Type-'. $k);
		} 
        
        $typeField = OptionsetField::create('Type',
            _t('EventResource.Type','Type'),
            $typeOptions,
            'Article'
        );
        
        $fields->addFieldToTab('Root.Main',$typeField);
        
        // Internal page
        $internalPageField = DisplayLogicWrapper::create(TreeDropdownField::create('InternalPageID',_t('EventResource.InternalPage','Internal Page'),'SiteTree'));
        $internalPageField->hideUnless('Type')->isEqualTo('InternalPage');
        $fields->addFieldToTab('Root.Main',$internalPageField);
        
         // Internal file
        $internalFileField = DisplayLogicWrapper::create(UploadField::create('InternalFile',_t('EventResource.InternalFile','Internal File')));
        $internalFileField->hideUnless('Type')->isEqualTo('InternalFile');
        $fields->addFieldToTab('Root.Main',$internalFileField);
        
        // External URL
        $externalUrlField = DisplayLogicWrapper::create(TextField::create('ExternalURL',_t('EventResource.ExternalURL','External URL')));
        $externalUrlField->hideUnless('Type')->isEqualTo('ExternalURL');
        $fields->addFieldToTab('Root.Main',$externalUrlField);
      
        $this->extend('updateCMSFields', $fields);
        
        return $fields;
	}
    
    public function getAddNewFields() {
        $fields = FieldList::create($this->createAddressFields());
        $fields->removeByName('AddressHeader');
        $fields->removeByName('LocationName');
        $fields->removeByName('UseMailingAddress');
        $fields->unshift(TextField::create('Title',_t('EventResource.Title','Title')));
        
         /** @todo
        $tzField = TimeZoneField::create('TimeZone', _t('EventResource.TimeZone'));
		$fields->replaceField('TimeZone',$tzField);
        */
       
        return $fields;
	}
    
    public function singular_name()
	{
		return _t('EventResource.SINGULARNAME', 'Location');
	}

	public function plural_name()
	{
		return _t('EventResource.PLURALNAME', 'Locations');
	}
    
    public function fieldLabels($includeRelations = true)
	{
		return array_merge(
			parent::fieldLabels($includeRelations),
			array(
				'Title' => _t('EventResource.Title', 'Title'),
				'Description' => _t('EventResource.Description', 'Description'),
				'Address' => _t('EventResource.Address', 'Address'),
				'City' => _t('EventResource.City', 'Suburb / city'),
				'State' => _t('EventResource.State', 'State / province'),
				'Postcode' => _t('EventResource.Postcode', 'Post code'),
                'Country' => _t('EventResource.Country', 'Country'),
                'TimeZone' => _t('EventResource.TimeZone', 'Disabled'),
                'Phone' => _t('EventResource.Phone', 'Phone'),
                'UsageCount' => _t('EventResource.UsageCount', 'Usage count')
			)
		);
	}
    
    public function UsageCount()
    {
        return $this->Events()->count();
    }
    
    
    public function getSummaryString()
    {
        return $this->getAddressDetailString(['Address','CountryName']);
    }
    
    public function getAddressString()
    {
        return $this->getAddressDetailString(['Title']);
    }
    
    protected function getAddressDetailString($excludeFields=array())
    {
        $arr = [];
        $excludeFields = (array) $excludeFields;
        $fields = array_diff(['Title','City','StateName','CountryName'],$excludeFields);
        
        foreach($fields as $v) {
            $arr[] = $this->{$v};
        }
     
        $str = implode(', ', array_filter($arr));
        return trim(preg_replace('/\n+/', ', ', $str));
    }
    
}

