<?php

class BrowseAbstractPage extends Page {

	/**
	 * Standard SS static
	 **/ 
	static $db = array(
		"CreateChildren" => "Boolean",
		"CreateAllChildren" => "Boolean",
		"HiddenDataID" => "Int",
		"AlternativeURL" => "Varchar",
		"ExtraNote" => "Varchar(255)"
	);

	/**
	 * Standard SS static
	 **/ 
	public static $breadcrumbs_delimiter = " &raquo; ";

	/**
	 * Standard SS method: can only create if the parent exists...
	 **/ 
	public function canCreate($member = null) {
		$bt = defined('DB::USE_ANSI_SQL') ? "\"" : "`";
		if("BrowseAbstractPage" == $this->ClassName) {
			return false;
		}
		else {
			return parent::canCreate();
		}
	}

	/**
	 * Name of the level
	 **/ 
	public function GeoLevelName() {
		return "No level";
	}

	/**
	 * Number of the level
	 **/ 
	public function GeoLevelNumber() {
		return -1;
	}


	/**
	 * works out if the child page needs to be created
	 **/ 
	public function allowBrowseChildren() {
		if ( DataObject::get_one("BrowseWorldPage")->LevelOfDetail > $this->GeoLevelNumber() ) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * retrieves data from a DB table that is not part of the DataObject Model. 
	 **/ 
	protected function getDataFromTable($tableName, $where  = null, $orderby = null) {
		$sqlQuery = new SQLQuery();
		$sqlQuery->select = array('*');
		$sqlQuery->from = Array($tableName);
		if($where) {
			$sqlQuery->where = array($where);
		}
		if($orderby) {
			$sqlQuery->orderby = $orderby;
		}
		$result = $sqlQuery->execute();
		return $result;
	}

	/**
	 * standard SS method
	 **/ 
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.AddSubRegion", new CheckboxField("CreateChildren", "Create Child Pages (e.g. countries below continents)"));
		$fields->addFieldToTab("Root.Content.AddSubRegion", new CheckboxField("CreateAllChildren", "Create All Child Pages (e.g. countries, regions, and cities below continents)"));
		//$fields->addFieldToTab("Root.Content.AddSubRegion", new ReadonlyField("HiddenDataID", "Data ID number (should have number)"));
		$fields->addFieldToTab("Root.Content.AddSubRegion", new LiteralField("build", "Create now... ", '<a href="db/build/?flush=1&geobuild=1">create pages now</a>'));
		if(!$this->allowBrowseChildren()) {
			$fields->removeFieldFromTab("Root.Content.AddSubRegion", "CreateChildren");
			$fields->removeFieldFromTab("Root.Content.AddSubRegion", "CreateAllChildren");
		}

		$fields->addFieldsToTab('Root.Content.Main', new TextField('AlternativeURL', 'Alternative URL'));

		return $fields;
	}

	/**
	 * standard SS method
	 **/ 
	function onBeforeWrite() {
		if($this->CreateAllChildren) {
			$this->CreateChildren = 1;
		}
		return parent::onBeforeWrite();
	}

}

class BrowseAbstractPage_Controller extends Page_Controller {


}

