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
        'TimeZone' => 'Varchar(100)', /** @todo */
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
    
    /*
	 * -------------------------------------------------------------------------
	 * Admin methods
	 * -------------------------------------------------------------------------
	 */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
		
        $tzField = TimeZoneField::create('TimeZone', _t('EventLocation.TimeZone'));

		$fields->replaceField('TimeZone',$tzField);

        return $fields;
	}
    
    
    /*
	 * -------------------------------------------------------------------------
	 * Template methods
	 * -------------------------------------------------------------------------
	 */
    public function MapHTML($width,$height)
    {
		$data = ArrayData::create(array(
			'Width'    => $width,
			'Height'   => $height,
			'Address' => rawurlencode($this->getLocationAddressString()),
			'ApiKey' => SiteConfig::current_site_config()->MapsApiKey
		));
		return $data->renderWith('LocationMap');
	}
    
    /*
	 * -------------------------------------------------------------------------
	 * Helper methods
	 * -------------------------------------------------------------------------
	 */
    /**
	 * Determines whether object has minimum location fields
	 * @return boolean
	 */
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
    
}

