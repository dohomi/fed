<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 *
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Backend
 */

class Tx_Fed_Backend_TCEMain {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexFormService;

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Extbase_Reflection_Service
	 */
	protected $reflectionService;

	/**
	 * @var Tx_Fed_Service_Content
	 */
	protected $contentService;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->flexFormService = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
		$this->infoService = $this->objectManager->get('Tx_Fed_Utility_DomainObjectInfo');
		$this->reflectionService = $this->objectManager->get('Tx_Extbase_Reflection_Service');
		$this->contentService = $this->objectManager->get('Tx_Fed_Service_Content');
	}

	/**
	 * @param string $table
	 * @param string $action
	 * @param array $record
	 * @param array $arguments
	 * @return array
	 */
	protected function executeBackendControllerCommand($table, $action, $record, $arguments=array()) {
		$objectType = $this->infoService->getObjectType($table);
		try {
			if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableBackendRecordController'] && $objectType) {
				$keys = array_keys($record);
				$controllerClassName = $this->infoService->getBackendControllerClassName($objectType);
				if ($controllerClassName) {
					if ($record['uid'] < 1) {
						$object = $this->objectManager->get($objectType);
					} else {
						$repository = $this->infoService->getRepositoryInstance($objectType);
						$object = $repository->findByUid($record['uid']);
					}
					$translatedKeys = $this->infoService->convertLowerCaseUnderscoredToLowerCamelCase($keys);
					$translatedRecordValues = array_combine($translatedKeys, $record);
					foreach ($translatedRecordValues as $underScoredName=>$value) {
						$upperCamelCaseName = Tx_Extbase_Utility_Extension::convertLowerUnderscoreToUpperCamelCase($underScoredName);
						$setter = 'set' . $upperCamelCaseName;
						$methodArray = array($object, $setter);
						if (method_exists($object, $setter)) {
							call_user_func_array($methodArray, array($value));
						}
					}
					$controller = $this->objectManager->get($controllerClassName);
				}
			}
			if ($controller && $object) {
				array_unshift($arguments, $object);
				$method = $action . 'Action';
				if (method_exists($controller, $method)) {
					$object = call_user_func_array(array($controller, $method), $arguments);
					$properties = $this->infoService->getValuesByAnnotation($object, 'var');
					foreach ($properties as $key=>$value) {
						$indexName = $this->infoService->convertCamelCaseToLowerCaseUnderscored($key);
						$record[$indexName] = $value;
					}
				}
			}
		} catch (Exception $e) {
			unset($e);
		}
		return $record;
	}

	/**
	 * @param	string		$command: The TCEmain operation status, fx. 'update'
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	array		$relativeTo: Filled if command is relative to another element
	 * @param	object		$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 */
	public function processCmdmap_preProcess(&$command, $table, $id, &$relativeTo, t3lib_TCEmain &$reference) {
		if ($table === 'tt_content') {
			switch ($command) {
				case 'delete':
					$rows = $this->contentService->getChildContentElementUids($id);
					foreach ($rows as $row) {
						$reference->deleteAction($table, $row['uid']);
					}
					break;
				case 'move':
					if ($relativeTo > 0) {
						$area = ''; // moving directly to a new page, remove area
					} else if (is_numeric($relativeTo)) {
						$area = $this->contentService->getFlexibleContentElementArea(array('pid' => $relativeTo));
					} else if (strpos($relativeTo, 'FED')) {
						$parts = explode('-', $relativeTo);
						$parts = array_slice($parts, 1, count($parts) - 2);
						$pid = array_pop($parts);
						$area = implode(':', $parts);
						$relativeTo = $pid;
					}
					$data = array('tx_fed_fcecontentarea' => $area);
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, "uid = '" . $id . "'", $data);
					break;
				default:
			}
		}
	}

	/**
	 * @param	string		$command: The TCEmain operation status, fx. 'update'
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	array		$relativeTo: Filled if command is relative to another element
	 * @param	object		$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 */
	public function processCmdmap_postProcess(&$command, $table, $id, $relativeTo, t3lib_TCEmain &$reference) {
	}

	/**
	 * @param	array		$incomingFieldArray: The original field names and their values before they are processed
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	t3lib_TCEmain	$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 */
	public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, $table, $id, t3lib_TCEmain &$reference) {
		if ($incomingFieldArray['uid'] > 0) {
			$action = 'read';
		} else {
			$action = 'create';
		}
		$incomingFieldArray = $this->executeBackendControllerCommand($table, $action, $incomingFieldArray);
		if ($table === 'tt_content' && $id) {
			foreach ((array) $incomingFieldArray['pi_flexform']['data']['options']['lDEF'] as $key=>$value) {
				if (strpos($key, 'tt_content') === 0) {
					$realKey = array_pop(explode('.', $key));
					if (isset($incomingFieldArray[$realKey])) {
						$incomingFieldArray[$realKey] = $value['vDEF'];
					}
				}
			}
			$incomingFieldArray['tx_fed_fcecontentarea'] = $this->contentService->getFlexibleContentElementArea($incomingFieldArray, $id);;
		}
	}

	/**
	 * @param	string		$status: The TCEmain operation status, fx. 'update'
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	array		$fieldArray: The field names and their values to be processed
	 * @param	object		$reference: Reference to the parent object (TCEmain)
	 * @return	void
	 */
	public function processDatamap_postProcessFieldArray ($status, $table, $id, &$fieldArray, t3lib_TCEmain &$reference) {
		$record = $this->executeBackendControllerCommand($table, $status, $fieldArray);
		if ($record) {
			$fieldArray = $record;
		}
	}

	/**
	 * @param	string		$status: The command which has been sent to processDatamap
	 * @param	string		$table:	The table we're dealing with
	 * @param	mixed		$id: Either the record UID or a string if a new record has been created
	 * @param	array		$fieldArray: The record row how it has been inserted into the database
	 * @param	object		$reference: A reference to the TCEmain instance
	 * @return	void
	 */
	public function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, t3lib_TCEmain &$reference) {
		if ($table == 'tt_content' && $fieldArray['CType'] == 'fed_fce') {
			switch ($status) {
				case 'new':
					$newUid = $reference->substNEWwithIDs[$id];
					$oldUid = $fieldArray['t3_origuid'];
					$children = $this->contentService->getChildContentElementUids($oldUid);
					foreach ($children as $child) {
						$areaAndUid = explode(':', $child['tx_fed_fcecontentarea']);
						$areaAndUid[1] = $newUid; // re-assign parent UID
						$overrideValues = array('tx_fed_fcecontentarea' => implode(':', $areaAndUid));
						$childUid = $reference->copyRecord($table, $child['uid'], $fieldArray['pid']);
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, "uid = '" . $childUid . "'", $overrideValues);
					}
					break;
				default:
			}
		}
	}

}
?>