<?php


class EventLocation extends DataObject
{
 
    private static $db = array(
        'Title' => 'Varchar(200)',
        'Description' => 'HTMLText',
        'Address'  => 'Varchar(255)',
		'City'   => 'Varchar(100)',
		'State'    => 'Varchar(100)',
		'Country'  => 'Varchar(100)',
        'Postcode' => 'Varchar(10)',
        'TimeZone' => 'Varchar(100)',
        'Phone' => 'Varchar(32)'
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
         * Address tab
         */
        $fields->findOrMakeTab('Root.AddressTab',_t('EventLocation.AddressTab','Address'));
        // Address
        $fields->addFieldsToTab('Root.AddressTab',array(
            TextField::create('Address',_t('EventLocation.Address','Street address')),
            TextField::create('City',_t('EventLocation.City','Suburb / city')),
            TextField::create('State',_t('EventLocation.State','State / province')),
            TextField::create('Country',_t('EventLocation.Country','Country')),
            TextField::create('Postcode',_t('EventLocation.Postcode','Post code')),
            //TimeZoneField::create('TimeZone', _t('EventLocation.TimeZone')) @todo
            TextField::create('Phone',_t('EventLocation.Phone','Phone'))
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
    
    /*
	 * -------------------------------------------------------------------------
	 * Template methods - @todo
	 * -------------------------------------------------------------------------
	 
    public function MapHTML($width,$height)
    {
		$data = ArrayData::create(array(
			'Width'    => $width,
			'Height'   => $height,
			'Address' => rawurlencode($this->getFullAddressString()),
			'ApiKey' => SiteConfig::current_site_config()->MapsApiKey
		));
		return $data->renderWith('LocationMap');
	}
    
    
    public function UsageCount()
    {
        return $this->Events()->count();
    }
    
     * 
     */
    
    /*
	 * -------------------------------------------------------------------------
	 * Helper methods @todo
	 * -------------------------------------------------------------------------
	 */
    /**
	 * Determines whether object has minimum location fields
	 * @return boolean
	 
	public function hasFullAddress()
	{
		return ($this->Address
			&& $this->City
			&& $this->Country
		);
	}

	public function isMappable()
	{
        $mapsApiKey = $this->findMapsApiKey();
		return $this->hasFullAddress() && $this->findMapsApiKey();
	}
    
    
    protected function findMapsApiKey()
    {
        $key = null;
        // First check for custom extension method
        $result = $this->extend('extendFindMapsApiKey');
        if (is_array($result) && !empty($result)) {
            $key = array_pop($result);
        }
        // Fall back to SiteConfig, if present
        if(!$key && class_exists('SiteConfig')) {
            $siteConfig = SiteConfig::current_site_config();
            $key = ($siteConfig && !empty($siteConfig->MapsApiKey)) ? $siteConfig->MapsApiKey : null;
        }
        
        return $key;
    }
    
    public function getFullAddressString()
	{
		$arr = array(
			$this->Address,
			$this->City,
			$this->State,
			$this->Country
		);
		$str = implode(', ',array_filter($arr));
		return trim(preg_replace('/\n+/',', ', $str));
	}
    */
}

