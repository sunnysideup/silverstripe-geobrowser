<?php

class BrowseRegionsPage extends BrowseAbstractPage {

		/**
	 * Standard SS Static
	 **/ 	
	static $icon = "geobrowser/images/treeicons/BrowseRegionsPage";

		/**
	 * Standard SS Static
	 **/ 	
	static $allowed_children = array("BrowseCitiesPage");

		/**
	 * Standard SS Static
	 **/ 	
	static $default_child = "BrowseCitiesPage";

		/**
	 * Standard SS Static
	 **/ 	
	static $default_parent = "BrowseCountriesPage";

		/**
	 * Standard SS Static
	 **/ 	
	static $can_be_root = false;

		/**
	 * Standard SS Static
	 **/ 	
	static $db = array(
		"Code" => "Varchar(8)",
	);

	/**
	 * Standard SS Static
	 **/ 
	static $defaults = array(
		"ShowInMenus" => false
	);
	
	/**
	 * Standard SS Static
	 **/ 	
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}


	/**
	 * Name of the level
	 **/ 	
	public function GeoLevelName() {
		return "Regions";
	}

	/**
	 * Number of the level
	 **/ 	
	public function GeoLevelNumber() {
		return 2;
	}


	/**
	 * Setup pages...
	 **/ 	
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$parents = DataObject::get("BrowseRegionsPage");
		if($parents && isset($_GET["geobuild"]) && $_GET["geobuild"] && $this->allowBrowseChildren()) {
			foreach($parents as $parent) {
			  if($parent->CreateChildren && $parent->HiddenDataID) {
					echo "<li>creating cities for ".$parent->Title."<ul>";
					$cities = $this->getDataFromTable("cities", "RegionID = ".$parent->HiddenDataID, "City");
					foreach($cities as $city) {
						if(!DataObject::get_one("BrowseCitiesPage", "{$bt}BrowseAbstractPage{$bt}.{$bt}HiddenDataID{$bt} = ".$city["CityID"])) {
							$page = new BrowseCitiesPage();
							$page->CreateCity($city, $parent);
							$page->destroy();
						}
					}
					echo "</ul></li>";
				}
			}
		  $parents->destroy();
		}
	}

	/**
	 * Creates a region... called from BrowseCountriesPage
	 * 
	 *@param Array $region - array of region details
	 *@param Object $parent - a BrowseCountriesPage object 
	 *
	 **/ 
	public function CreateRegion(array $region, BrowseCountriesPage $parent) {
		if($parent && isset($region["Region"])) {
			$name = htmlentities($region["Region"]);
			if($name) {
				if(isset($_GET["geobuild"])) {echo "<li>creating ".$name."</li>";}
				$this->ParentID = $parent->ID;
				$this->Title = $name;
				$this->MetaTitle = $name;
				$this->MenuTitle = $name;
				$this->HiddenDataID = $region["RegionID"];

				$this->Code = $region["Code"];

				$this->CreateChildren = $parent->CreateAllChildren;
				$this->CreateAllChildren = $parent->CreateAllChildren;

				$this->URLSegment = $this->generateURLSegment($this->Title);

				$this->writeToStage('Stage');
				$this->publish('Stage', 'Live');
				$this->flushCache();
			}
			else {
				if(isset($_GET["geobuild"])) {debug::show("region does not exist");}
			}
		}
		else {
			if(isset($_GET["geobuild"])) {debug::show("Parent does not exist");}
		}
	}

}

class BrowseRegionsPage_Controller extends BrowseAbstractPage_Controller {


}

