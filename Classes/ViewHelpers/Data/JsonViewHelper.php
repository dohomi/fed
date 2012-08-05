<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
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
***************************************************************/

/**
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers\Data
 */
class Tx_Fed_ViewHelpers_Data_JsonViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('data', 'mixed', 'Either an array or an object containing data to be encoded');
		if (!$this->objectManager) {
			$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		}
	}

	/**
	 * Encodes $data as JSON using internal JSON encoding
	 * @return string
	 */
	public function render() {
		$jsonHandler = $this->objectManager->get('Tx_Fed_Service_Json');
		$string = $jsonHandler->encode($this->arguments['data']);
		return (string) $string;
	}
}
