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

/**
 * ImportController
 */
class ImportController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * importRepository
	 *
	 * @var \Phi\PhiNewsletter\Domain\Repository\ImportRepository
	 * @TYPO3\CMS\Extbase\Annotation\Inject
	 */
	protected $importRepository = NULL;

	/**
	 * importRepository
	 *
	 * @param \Phi\PhiNewsletter\Domain\Repository\ImportRepository
	 */
	public function injectImportRepository(\Phi\PhiNewsletter\Domain\Repository\ImportRepository $importRepository){
		$this->importRepository = $importRepository;
	}

	/**
	 * action loadcsv
	 *
	 * @return void
	 */
	public function loadcsvAction() {

		if(!isset($this->settings['pidForImport']) || $this->settings['pidForImport'] == "0"){
			$this->addFlashMessage("Please set the pidForImport in the Module's settings!");
		}
		$args = $this->request->getArguments();

		if($args["usergroup"] == "0"){
			$this->addFlashMessage("Please select a Usergroup to import to!","",\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
		}
		$this->view->assign("settings",$this->settings);
		$this->view->assign("usergroups",$this->importRepository->loadUsergroups());

		if(count($_FILES) && $args["usergroup"] != "0"){
			$tmp_name = $_FILES["tx_phinewsletter_web_phinewsletterphinewsletter"]["tmp_name"]["file"];
			$filename = "../user_data.csv";
			if(is_file($filename)){
				unlink($filename);
			}

			if(move_uploaded_file($tmp_name, $filename)){
				$csvPath = $filename;
			}else{
				die("Error: File not uploaded!");
			}

			$csvDelimiter = $this->settings["csvDelimiter"];
			$index == 0;

			if (($handle = fopen($csvPath, "r")) !== FALSE) {
				$titles = array();
				while (($data = fgetcsv($handle,0,$csvDelimiter)) !== FALSE) {
					//echo count($data);exit;
					if($index){
						$this->importRepository->addFeUser($this->settings,$args["usergroup"],$data);
					}
					$index++;
				}
				fclose($handle);
				$this->addFlashMessage($this->importRepository->countAddedUsers . " users have been added!");
				$this->addFlashMessage($this->importRepository->countEditedUsers . " users have been edited!");

			}
		}

	}


}
