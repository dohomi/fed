<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due, Wildside A/S <claus@wildside.dk>
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
 * ViewHelper used to override page title
 *
 * @author Georg Ringer
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage ViewHelpers/Page
 */
class Tx_Fed_ViewHelpers_Page_Head_TitleViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('title', 'string', 'Title tag content');
	}


	/**
	 * Render method
	 *
	 * @return void
	*/
	public function render() {
		if (TYPO3_MODE == 'BE') {
			return '';
		}

		if (!empty($this->arguments['title'])) {
			$title = $this->arguments['title'];
		} else {
			$title = $this->renderChildren();
		}
		$GLOBALS['TSFE']->getPageRenderer()->setTitle($title);
	}

}

?>