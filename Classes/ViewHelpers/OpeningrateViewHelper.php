<?php
namespace Phi\PhiNewsletter\ViewHelpers;
/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
/**
 * This class is the text color view helper for the Fluid templating engine.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class OpeningrateViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * emailsRepository
	 *
	 * @var \Phi\PhiNewsletter\Domain\Repository\EmailsRepository
	 * @inject
	 */
	protected $emailsRepository = NULL;

	/**
     * Renders the image inputs or the image name
     *
	 * @param \int $edition
     * @return \object
     */
	public function render($edition) {
		$totalOpening = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*","tx_phinewsletter_domain_model_openingrate","edition = '".$edition."'");
		$totalOpening = $GLOBALS["TYPO3_DB"]->sql_num_rows($totalOpening);
		$openingDistincUser = $GLOBALS["TYPO3_DB"]->exec_SELECTquery("*","tx_phinewsletter_domain_model_openingrate","edition = '".$edition."'","ip");
		$openingDistincUser = $GLOBALS["TYPO3_DB"]->sql_num_rows($openingDistincUser);
		$emails = $this->emailsRepository->findByEdition($edition);
		$emails = $emails->count();


		return $totalOpening. "/" .$openingDistincUser . "/" . $emails;
	}
}

?>
