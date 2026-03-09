<?php
namespace Phi\PhiNewsletter\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
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
use \TYPO3\CMS\Extbase\Annotation\Inject;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * AbstractController
 */
class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


    /**
     * getConfVal
     *
     * @param \string $name
     * @param \string $default
     * @return void
     */
    public function getConfVal($name,$default = "")
    {

      switch($name){
          case 'storagePid':
          case 'imagePath':
            if(!isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['phi_newsletter'][$name]) && strlen($default) == 0){
              echo "set $name in the conf vars \$GLOBALS['TYPO3_CONF_VARS']['EXT']['phi_newsletter']['$name']";
              exit;
            }elseif(isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['phi_newsletter'][$name])){
              return $GLOBALS['TYPO3_CONF_VARS']['EXT']['phi_newsletter'][$name];
            }elseif(strlen($default)){
              return $default;
            }
            break;
      }
    }


}
