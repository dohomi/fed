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
*  the Free Software Foundation; either version 3 of the License, or
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


require_once t3lib_extMgm::extPath('cms', 'layout/interfaces/interface.tx_cms_layout_tt_content_drawitemhook.php');

/**
 * Flexible Content Element Backend Renderer
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package Fed
 * @subpackage Controller
 */

class Tx_Fed_Backend_Preview implements tx_cms_layout_tt_content_drawItemHook {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @var Tx_Fluid_View_StandaloneView
	 */
	protected $view;

	/**
	 * @var Tx_Fed_Domain_Repository_FceRepository
	 */
	protected $fceRepository;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$templatePathAndFilename = t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/FlexibleContentElement/BackendPreview.html');
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->fceRepository = $this->objectManager->get('Tx_Fed_Domain_Repository_FceRepository');
		$this->view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
		$this->view->setTemplatePathAndFilename($templatePathAndFilename);
	}

	/**
	 * Preprocessing
	 *
	 * @param tx_cms_layout $parentObject
	 * @param boolean $drawItem
	 * @param type $headerContent
	 * @param type $itemContent
	 * @param array $row
	 */
	public function preProcess(tx_cms_layout &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
		$uid = $row['tx_fed_fceuid'];
		if ($uid > 0) {
			$drawItem = FALSE;
			$fceParser = $this->objectManager->get('Tx_Fed_Backend_FCEParser');
			$record = $this->fceRepository->findByUid($uid);
			$filename = PATH_site . $record->getFilename();
			$itemContent = $filename;
			$stored = $fceParser->getFceDefinitionFromTemplate($filename);
			$this->view->assign('fce', $stored);
			$itemContent = $this->view->render();
		}

	}

}


?>