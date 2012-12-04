<?php

/**
 * There is no data for this page.
 * We can use Google Maps to choose suburbs...?
 * 
 * 
 **/ 

class BrowseSuburbPage extends BrowseAbstractPage {
	
	/**
	 * Standard SS Static
	 **/ 
	static $icon = "geobrowser/images/treeicons/BrowseSuburbPage";
	
	/**
	 * Standard SS static
	 **/ 
	static $db = array(
		"Longitude" => "Double(12,7)",
		"Lattitude" => "Double(12,7)"
	);

	/**
	 * Standard SS Static
	 **/ 
	static $default_parent = "BrowseCitiesPage";
	
	/**
	 * Standard SS Static
	 **/ 
	static $can_be_root = false;

	/**
	 * Standard SS Static
	 **/ 
	static $defaults = array(
		"ShowInMenus" => false
	);
	

	/**
	 * Standard SS method
	 * CMS Fields
	 **/ 	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}
	
	
	/**
	 * Name of the level.
	 **/ 	
	public function GeoLevelName() {
		return "Suburbs";
	}
	
	/**
	 * number of the level.
	 **/ 	
	public function GeoLevelNumber() {
		return 4;
	}

	/**
	 * Creates a region... called from BrowseCountriesPage
	 * 
	 *@param Array $googleMapAddressArray - array of google Map Address Data
	 *@param Object $parent - a BrowseCountriesPage object 
	 *@return Object | false - returns the BrowseSuburbPage if there is one.
	 *
	 **/ 
	public static function create_suburb($googleMapAddressArray, BrowseCitiesPage $parent) {
		if($parent && isset($googleMapAddressArray["LocalityName"]) && isset($googleMapAddressArray[0]) && isset($googleMapAddressArray[1])) {
			$name = htmlentities($googleMapAddressArray["LocalityName"]);
			if($name) {
				$page = DataObject::get("BrowseSuburbPage", "\"Title\" = '".$name."' AND \"ParentID\" = ".$parent->ID);
				if(!$page) {
					$page = new BrowseSuburbPage();
					$page->ParentID = $parent->ID;
					$page->Title = $name;
					$page->MetaTitle = $name;
					$page->MenuTitle = $name;
					$page->Longitude = $googleMapAddressArray[0];
					$page->Latitude = $googleMapAddressArray[1];
					$page->writeToStage('Stage');
					$page->publish('Stage', 'Live');
					$page->flushCache();
				}
				$page = DataObject::get("BrowseSuburbPage", "\"Title\" = '".$name."' AND \"ParentID\" = ".$parent->ID);				
			}
		}
	}

}

class BrowseSuburbPage_Controller extends BrowseAbstractPage_Controller {

}

