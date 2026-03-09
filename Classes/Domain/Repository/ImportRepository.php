<?php
namespace Phi\PhiNewsletter\Domain\Repository;


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

   use TYPO3\CMS\Core\Utility\GeneralUtility;
   use TYPO3\CMS\Core\Database\ConnectionPool;
/**
 * The repository for Stats
 */
class ImportRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	public $countAddedUsers = 0;
	public $countEditedUsers = 0;


	/**
	 * function addFeUser
	 *
	 * @param \array $settings
	 * @param \int $usergroup
	 * @param \array $data
	* @return \TYPO3\CMS\Extbase\Persistence\QueryResult
	 */
	public function addFeUser($settings,$usergroup,$data){

		$pid = $settings["pidForImport"];
		$groupId = $usergroup;
			//CONTENTS

			$firstNameIndex = intval($settings["indices"]["firstName"]) - 1;
			$lastNameIndex = intval($settings["indices"]["lastName"]) - 1;
			$addressIndex = intval($settings["indices"]["address"]) - 1;
			$zipIndex = intval($settings["indices"]["zip"]) - 1;
			$cityIndex = intval($settings["indices"]["city"]) - 1;
			$companyIndex = intval($settings["indices"]["company"]) - 1;
			$emailIndex = intval($settings["indices"]["email"]) - 1;
			$titleIndex = intval($settings["indices"]["title"]) - 1;
			$genderIndex = intval($settings["indices"]["gender"]) - 1;
			$languageIndex = intval($settings["indices"]["language"]) - 1;
			$idfIndex = intval($settings["indices"]["idf"]) - 1;


			$gender = '';
			if(strpos(strtolower($data[$genderIndex]),"frau") !== FALSE){
				$gender = "w";
			}
			if(strpos(strtolower($data[$genderIndex]),"herr") !== FALSE){
				$gender = "m";
			}
			$language = 0;
			if($languageIndex > 0){
				if(isset($data[$languageIndex])){
					$language = $data[$languageIndex];
				}
				foreach($settings["indices"]["languageMapping"] as $lang=>$id){
					if(strpos(strtolower($data[$languageIndex]),$lang) !== FALSE){
						$language = $id;
					}
				}
			}

      if(!isset($data[$emailIndex]) || !$emailIndex || strlen(trim($data[$emailIndex])) == 0){
        return;
      }

      $mail = preg_replace('/[^a-zA-Z0-9@\.]/',"",$data[$emailIndex]);
			$table = "fe_users";
			$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

			$stmt = $queryBuilder->select('*')->from($table)->where(
				$queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($mail))
			)->execute();

			if($stmt->rowCount() && strlen($mail )){
        while($row = $stmt->fetch()){
					$tempUserGroups = explode(",",$row['usergroup']);
					if(!in_array($groupId,$tempUserGroups)){
						$tempUserGroups[] = $groupId;
						$row['usergroup'] = implode(",",$tempUserGroups);
					}

					$table = "fe_users";
					$queryBuilder2 = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
					$queryBuilder2->update($table)
					   ->where(
					      $queryBuilder2->expr()->eq('uid', $queryBuilder2->createNamedParameter($row["uid"]))
					   );


 					$updateValues = array();
 					if($firstNameIndex >= 0){
            $queryBuilder2->set('first_name',$data[$firstNameIndex]);
 					}
 					if($lastNameIndex >= 0){
            $queryBuilder2->set('last_name',$data[$lastNameIndex]);
 					}
 					if($genderIndex >= 0){
            $queryBuilder2->set('gender',$gender);
 					}
 					if($titleIndex >= 0){
            $queryBuilder2->set('title',$data[$titleIndex]);
 					}
 					if($addressIndex >= 0){
            $queryBuilder2->set('address',$data[$addressIndex]);
 					}
 					if($zipIndex >= 0){
            $queryBuilder2->set('zip',$data[$zipIndex]);
 					}
 					if($cityIndex >= 0){
            $queryBuilder2->set('city',$data[$cityIndex]);
 					}
 					if($companyIndex >= 0){
            $queryBuilder2->set('company',$data[$companyIndex]);
 					}
         if($languageIndex >= 0){
          $queryBuilder2->set('language',$language);
         }
          if($idfIndex >= 0){
           $queryBuilder2->set('customuserid',$data[$idfIndex]);
          }
          $queryBuilder2->set('usergroup',$row['usergroup']);

					$queryBuilder2->set('tstamp',time());

          $queryBuilder2->execute();
					$this->countEditedUsers++;
        }
			}else{

				$trimmed = array();
				foreach($data as $val){
					$trimmed[] = trim($val);
				}
				$data = $trimmed;

			$insertValues = array('pid' => $pid);

			if($firstNameIndex > 0){
        $insertValues['first_name'] = $data[$firstNameIndex];
			}
			if($lastNameIndex > 0){
				$insertValues["last_name"] = $data[$lastNameIndex];
			}
			if($genderIndex > 0){
				$insertValues["gender"] = $gender;
			}
			if($titleIndex > 0){
				$insertValues["title"] = $data[$titleIndex];
			}
			if($addressIndex > 0){
				$insertValues["address"] = $data[$addressIndex];
			}
			if($zipIndex > 0){
				$insertValues["zip"] = $data[$zipIndex];
			}
			if($cityIndex > 0){
				$insertValues["city"] = $data[$cityIndex];
			}
			if($companyIndex > 0){
				$insertValues["company"] = $data[$companyIndex];
			}
      if($languageIndex > 0){
        $insertValues["language"] = $language;
      }

      $insertValues["email"] = $mail;
      $insertValues["username"] = $mail;

      $insertValues["usergroup"] = $usergroup;
      $insertValues["crdate"] = time();
      $insertValues["tstamp"] = time();

      if($idfIndex > 0){
        $insertValues["customuserid"] = $data[$idfIndex];

      }

       $customUserId = intval(str_replace(["ch-","-1"],"",$data[3]));
			 $table = "fe_users";
			 $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
       $res = $queryBuilder->insert($table)->values($insertValues)
			 ->execute();

				$this->countAddedUsers++;
			}
	}

	/**
	 * function loadUsergroups
	 *

	* @return \array
	 */
	public function loadUsergroups(){

		$groups = array(array("uid"=>0,"title"=>"---"));


		$table = "fe_groups";
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
		$res = $queryBuilder->select('*')->from($table)->execute();
		while($row = $res->fetch()){
			$groups[] = $row;
		}

		return $groups;
	}
}
