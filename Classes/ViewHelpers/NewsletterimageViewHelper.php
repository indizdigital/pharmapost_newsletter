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
 use \TYPO3\CMS\Extbase\Annotation\Inject;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * This class is the text color view helper for the Fluid templating engine.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class NewsletterimageViewHelper extends AbstractViewHelper{


  public function initializeArguments()
  {
      $this->registerArgument('newsuid', 'int', 'uid of the news entry', true);
  }

	/**
     * return the category items
     *
     * @return \string
     */
	public static function renderStatic($arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {

            $newsuid = $arguments["newsuid"];
      $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_reference');
      $statement = $queryBuilder->select("uid","uid_local")->from('sys_file_reference')->where(
        $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter("tx_indiznews_domain_model_news")),
        $queryBuilder->expr()->eq('uid_foreign', $queryBuilder->createNamedParameter($newsuid))
     )->execute();
      $sysref = 0;
      if ($row = $statement->fetch()) {
        $sysref = $row["uid"];
      }

			return $sysref;
	}

  //public function setViewHelperNode($object){}

}

?>
