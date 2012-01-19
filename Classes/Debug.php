<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due, Wildside A/S <claus@wildside.dk>
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
 * FED DEBUG
 *
 * Quick-access Alias methods for accessing FED's debugging features
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 */
abstract class Tx_Fed_Debug {

	/**
	 * Dumps a variable - second parameter returns the dump as string rather
	 * than echo to browser.
	 *
	 * @param mixed $variable Variable to dump
	 * @param string $return
	 * @api
	 */
	public static function dump($variable, $return=FALSE) {
		return Tx_Fed_Error_Debugger::var_export($variable, $return);
	}

}
?>