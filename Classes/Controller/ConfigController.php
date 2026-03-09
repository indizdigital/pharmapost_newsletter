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
 * ConfigController
 */
class ConfigController extends AbstractController
{

    /**
  	 * configRepository
  	 *
  	 * @var \Phi\PhiNewsletter\Domain\Repository\ConfigRepository  $configRepository
  	 * @Inject
  	 */
  	protected $configRepository = NULL;

  	/**
  	 * configRepository
  	 *
  	 * @param \Phi\PhiNewsletter\Domain\Repository\ConfigRepository $configRepository
  	 */
  	public function injectConfigRepository(\Phi\PhiNewsletter\Domain\Repository\ConfigRepository $configRepository){
  		$this->configRepository = $configRepository;
  	}

    /**
     * action edit
     *
     * @return void
     */
    public function listAction()
    {

        $configs = $this->configRepository->findBySelected(1);
        $this->view->assign('configs', $configs);
        $draftConfigs = $this->configRepository->findByIssent(0);
        $this->view->assign('draftConfigs', $draftConfigs);
        $this->view->assign('lastconfigs', $this->configRepository->findLast());


        $imagePath = $this->getConfVal('imagePath','/typo3conf/ext/phi_newsletter/Resources/Public/Image');
        $this->view->assign('imagePath', $imagePath);

    }

    /**
     * action load
     *
     * @return void
     */
    public function loadAction()
    {
        //EXTBASE'S AUTO PARAM PARADIGM DOESN'T WORK FOR BE-MOD'S
        //SO GET IT YOURSELF FROM THE GET'S
        if(isset($_POST['tx_phinewsletter_web_phinewsletterphinewsletter']['dontDuplicate']) &&
        $_POST['tx_phinewsletter_web_phinewsletterphinewsletter']['dontDuplicate']){
          $newuid = $_POST['tx_phinewsletter_web_phinewsletterphinewsletter']['templateConfig'];
        }else{
            $newuid = $this->configRepository->duplicateThis($_POST['tx_phinewsletter_web_phinewsletterphinewsletter']['templateConfig'], $this->settings);

        }

        $getArgs = ["route"=>"/web/2FPhiNewsletterPhinewsletter/"];
        $uri = str_replace($_POST['tx_phinewsletter_web_phinewsletterphinewsletter']['templateConfig'],$newuid,$_POST["tx_phinewsletter_web_phinewsletterphinewsletter"]["editlink"]);
        //$uri = $_POST["tx_phinewsletter_web_phinewsletterphinewsletter"]["editlink"];
        $returnUriBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        $uriParameters =
            [
              'tx_phinewsletter_web_phinewsletterphinewsletter'=>[
                'action' => 'selectitems',
                'controller' => 'Emails',
                'config'=>$newuid]
            ];
        $returnUrlLink = $returnUriBuilder->buildUriFromRoutePath('/module/web/PhiNewsletterPhinewsletter', $uriParameters);

        $backendUriBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        $uriParameters =
            [
                'edit' =>
                    [
                        'tx_phinewsletter_domain_model_config' =>
                            [
                                $newuid => 'edit'
                            ]
                    ],
                'returnUrl' =>$returnUrlLink->__toString()
              ];

        $editPagesDoktypeLink = $backendUriBuilder->buildUriFromRoute('record_edit', $uriParameters);

        $this->redirectToUri($editPagesDoktypeLink, 0, 404);
      //  $this->redirect('list', 'Config', NULL, array('config' => $newuid));
    }


    /**
     * action update
     *
     * @param \Phi\PhiNewsletter\Domain\Model\Config $config
     * @return void
     */
    public function updateAction(\Phi\PhiNewsletter\Domain\Model\Config $config)
    {

    		if(strlen($config->getTosendtime())){
    			$parts = explode(" ",$config->getTosendtime());
    			if(count($parts) == 2){
    				$time = explode(":",$parts[0]);
    				$date = explode(".",$parts[1]);
    				$config->setTosendtime(mktime($time[0],$time[1],0,$date[1],$date[0],$date[2]));
    			}
    		}
        $this->configRepository->update($config);
        if ($_POST['tx_phinewsletter_web_phinewsletterphinewsletter']['continueNewsletter'] == '1') {
            $this->redirect('list', 'Emails');
        }
        $this->redirect('edit', 'Config', NULL, array('config' => $config->getUid()));
    }


}
