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
   
    private static $casting = array(
		'Link' => 'Text'
	);
    
    /**
	 * @config
	 */
	private static $summary_fields = array(
        'ID',
		'Title',
        'Summary',
        'Type'
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
    
    public function singular_name()
	{
		return _t('EventResource.SINGULARNAME', 'Resource');
	}

	public function plural_name()
	{
		return _t('EventResource.PLURALNAME', 'Resources');
	}
    
    public function fieldLabels($includerelations = true)
	{
		return array_merge(parent::fieldLabels($includerelations),(array) $this->translatedLabels());
	}
	
	protected function translatedLabels() {
		return array(
			'Title' => _t('EventResource.Title','Title'),
            'Summary' => _t('EventResource.Summary','Summary'),
            'Type' => _t('EventResource.Type','Type')
		);
	}

    protected function setExternalURL($value)
	{
		$this->setField('ExternalURL',$this->prepareURL($value));
	}
    
    /**
	 * Adds scheme if missing
	 * @param  string $value
	 * @return string
	 */
	protected function prepareURL($value)
	{
		$scheme = parse_url($value,PHP_URL_SCHEME);
		if(!empty($value) && (empty($scheme) || strpos($value,'://' === false))) {
			$value = 'http://'. $value;
		}
		return $value;
	}
    
    /*
	 * -------------------------------------------------------------------------
	 * Template methods
	 * -------------------------------------------------------------------------
	 */
	public function Link()
	{
		switch($this->Type) {
			case 'InternalPage':
				$obj = $this->InternalPage();
				return ($obj) ? $obj->AbsoluteLink() : '';
				break;
			case 'InternalFile':
				$obj = $this->InternalFile();
				return ($obj) ? $obj->AbsoluteLink() : '';
				break;
			case 'ExternalURL':
				return $this->ExternalURL;
				break;
		}

	}
   
}

