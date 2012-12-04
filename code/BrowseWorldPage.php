<?php



class BrowseWorldPage extends BrowseAbstractPage {

	/**
	 * Standard SS Static
	 **/ 
	static $icon = "geobrowser/images/treeicons/BrowseWorldPage";

	/**
	 * Standard SS static 
	 **/ 
	static $allowed_children = array("BrowseContinentsPage");

	/**
	 * Standard SS Static
	 **/ 
	static $default_child = "BrowseContinentsPage";

	/**
	 * Standard SS Static
	 **/ 
	static $db = array(
		"LevelOfDetail" => "Int",
	);
	
	/**
	 * Standard SS Static
	 **/ 
	static $defaults = array(
		"CreateChildren" => true,
		"LevelOfDetail" => 2,
		"ShowInMenus" => true
	);
	
	/**
	 * @var Array
	 * Sets the level of detail in terms of the pages that are automatically created.
	 **/ 
	protected static $LevelOfDetailArray = Array (
		"0" => "Continents",
		"1" => "Countries",
		"2" => "Regions",
		"3" => "Cities",
		"4" => "Suburbs"
	);
	

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.AddSubRegion", new DropdownField("LevelOfDetail", "Greatest Level of Detail in pages shown ...", self::$LevelOfDetailArray));
		return $fields;
	}

	/**
	 * Standard SS function
	 * Creating level of detail
	 * Note that the _GET variable "geobuild" needs to be turned on.
	 **/ 
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		$parents = DataObject::get("BrowseWorldPage");
		if($parents && isset($_GET["geobuild"]) && $_GET["geobuild"]) {
			foreach($parents as $parent) {
				if($parent->CreateChildren) {
					echo "<li>creating continents for ".$parent->Title."<ul>";
					$continents = $this->getDataFromTable("continents", null, "Continent");
					foreach($continents as $continent) {
						if(!DataObject::get("BrowseContinentsPage", "{$bt}BrowseAbstractPage{$bt}.{$bt}HiddenDataID{$bt} = ".$continent["ContinentID"])) {
							$page = new BrowseContinentsPage();
							$page->CreateContinent($continent, $parent);
							$page->destroy();
						}
					}
					echo "</ul></li>";
				}
			}
		}
	}
}

class BrowseWorldPage_Controller extends BrowseAbstractPage_Controller {
	
}

