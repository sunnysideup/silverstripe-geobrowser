<?php

class BrowseContinentsPage extends BrowseAbstractPage {

	/**
	 * Standard SS static
	 **/ 
	static $icon = "geobrowser/images/treeicons/BrowseContinentsPage";


	/**
	 * Standard SS static
	 **/ 
	static $allowed_children = array("BrowseCountriesPage");

	/**
	 * Standard SS static
	 **/ 
	static $default_child = "BrowseCountriesPage";

	/**
	 * Standard SS static
	 **/ 
	static $default_parent = "BrowseWorldPage";

		/**
	 * Standard SS Static
	 **/ 	
	static $can_be_root = false;
	
	/**
	 * Standard SS Static
	 **/ 
	static $defaults = array(
		"ShowInMenus" => true
	);
	
	/**
	 * name of the level
	 **/ 
	public function GeoLevelName() {
		return "Continents";
	}

	/**
	 * number of the level
	 **/ 
	public function GeoLevelNumber() {
		return 0;
	}

	/**
	 * Standard SS Method
	 * setup records
	 **/ 
	public function requireDefaultRecords() {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		parent::requireDefaultRecords();
		$parents = DataObject::get("BrowseContinentsPage");
		if($parents && isset($_GET["geobuild"]) && $_GET["geobuild"] && $this->allowBrowseChildren()) {
			foreach($parents as $parent) {
				if($parent->CreateChildren && $parent->HiddenDataID) {
					echo "<li>creating countries for ".$parent->Title."<ul>";
					$countries = $this->getDataFromTable("countries", "ContinentID = ".$parent->HiddenDataID, "Country");
					foreach($countries as $country) {
						if(!DataObject::get_one("BrowseCountriesPage", "{$bt}HiddenDataID{$bt} = ".$country["CountryID"])) {
							$page = new BrowseCountriesPage();
							$page->CreateCountry($country, $parent);
							$page->destroy();
						}
					}
					echo "</ul></li>";
				}
			}
		}
	}
	
	/**
	 * create a continent
	 * @param array - $contentint, continent data
	 * @param Object - $parent, a BrowseWorldPage object that will be the parent page of the Continent.
	 **/ 
	public function CreateContinent(array $continent, BrowseWorldPage $parent) {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if($parent && isset($continent["Continent"])) {
			$name = htmlentities($continent["Continent"]);
			if($name) {
				if(isset($_GET["geobuild"])) {echo "<li>creating ".$name."</li>";}
				$this->ParentID = $parent->ID;
				$this->Title = $name;
				$this->MetaTitle = $name;
				$this->MenuTitle = $name;
				$this->HiddenDataID = $continent["ContinentID"];
				$this->CreateChildren = $parent->CreateAllChildren;
				$this->CreateAllChildren = $parent->CreateAllChildren;
				$this->writeToStage('Stage');
				$this->publish('Stage', 'Live');
				$this->flushCache();
			}
			else {
				if(isset($_GET["geobuild"])) {debug::show("name does not exist");}
			}
		}
		else {
			if(isset($_GET["geobuild"])) {debug::show("Parent does not exist");}
		}
	}
}

class BrowseContinentsPage_Controller extends BrowseAbstractPage_Controller {

}

