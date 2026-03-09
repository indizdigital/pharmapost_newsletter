<?php
namespace Phi\PhiNewsletter\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013
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
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class UserRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

  protected $hash_base = ":`OknkA.tfTXq4[P'Wb>u/@##^Hz}r";

    /**
     * removeFromGroup
  	 *
  	 * @param \string $user
  	 * @param \int $group
  	 * @param \string $hash
  	 * @return \void
    */
    public function removeFromGroup($user,$group,$hash) {
        $table = "fe_users";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);


        $res = $queryBuilder->select('*')->from($table)->orWhere(
          $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($user))
        )->execute();

        while($row = $res->fetch()){
            $usergroup = explode(",",$row["usergroup"]);
            $newgroup = [];
            foreach($usergroup as $oldgroup){
              if($oldgroup != $group){
                $newgroup[] = $oldgroup;
              }
            }
            $newGroupString = implode(",",$newgroup);
            $queryUpdateBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $queryUpdateBuilder->update($table)->where(
              $queryBuilder->expr()->eq('uid', $queryUpdateBuilder->createNamedParameter($row["uid"]))
              )->set('usergroup',$newGroupString)->execute();
          } 

      }

      /**
       * removeFromGroup
    	 *
    	 * @param \string $email
    	 * @param \array $groups
    	 * @param \string $hash
    	 * @return \void
      */
      public function removeFromAllGroupsButNotFromThese($email,$groups,$hash) {
          $table = "fe_users";
          $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

          $res = $queryBuilder->select('*')->from($table)->orWhere(
            $queryBuilder->expr()->eq('email', $queryBuilder->createNamedParameter($email))
          )->orderBy("uid","ASC")->execute();

          while($row = $res->fetch()){
            if(hash("sha256",$row["uid"] . $this->hash_base) == $hash){
              $usergroup = explode(",",$row["usergroup"]);
              $newgroup = [];
              foreach($usergroup as $oldgroup){
                if(in_array($oldgroup,$groups)){
                  $newgroup[] = $oldgroup;
                }
              }
              $newGroupString = implode(",",$newgroup);
              $queryUpdateBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
              $queryUpdateBuilder->update($table)->where(
                $queryBuilder->expr()->eq('uid', $queryUpdateBuilder->createNamedParameter($row["uid"]))
                )->set('usergroup',$newGroupString)->execute();
            }
          }

        }
      /**
       * findByUid
  	 *
  	 * @param \int $uid
  	 * @return \Phi\PhiNewsletter\Domain\Model\User
      */
      public function findByUid($uid) {

        $table = "fe_users";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $row = [];
        $res = $queryBuilder->select('*')->from($table)->where(
          $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
        )->execute();

        if($row = $res->fetch()){

        }
        return $row;



      }
    /**
     * updateByUid
	 *
	 * @param \int $uid
	 * @param \array $user
	 * @return \Phi\PhiNewsletter\Domain\Model\User
    */
    public function updateByUid($uid,$user) {
      $table = "fe_users";
      $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

      $row = [];
      $res = $queryBuilder->update($table)->where(
        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
      )->set('usergroup',$user["usergroup"])->set('doubleopthash',"")->execute();

  		/*$values = array();
  		foreach($user as $f=>$val){
  			$values[] = $f ." = " .$GLOBALS['TYPO3_DB']->fullQuoteStr($val,"fe_users") . "";
  		}
  		$res = $GLOBALS['TYPO3_DB']->sql_query("UPDATE fe_users SET ".implode(",",$values)." WHERE uid = " . $uid);
  		echo $GLOBALS['TYPO3_DB']->sql_error();*/

    }

    /**
     * mergeDuplicates
	 * @deprecated
	 * @return \void
    */
    public function mergeDuplicates() {
      exit;
          /*
    		$sql = 'SELECT Count( * ) AS nu,username,usergroup FROM `fe_users` WHERE deleted =0 AND disable =0 AND username > "" GROUP BY username HAVING nu > 1 ORDER BY nu DESC';

    		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
    		$duplicates = array();

    		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
    			$duplicates[] = $row["username"];
    		}

    		$sql = "SELECT username,usergroup,uid FROM `fe_users` WHERE username IN ('".implode("','",$duplicates) ."') ORDER BY username,uid";

    		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
    		$duplicates = array();

    		$current = "";
    		$groups = array();
    		$lastuid = 0;
    		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
    			if(strlen($current) > 0 && $current != $row["username"]){

    				//echo $stmt;exit;
    				$merged = array();
    				foreach($groups as $group){
    					$groupUids = explode(",",$group);
    					foreach($groupUids as $uid){
    						if(!in_array($uid,$merged)){
    							$merged[] = $uid;
    						}
    					}
    				}
    				$sql2 = "UPDATE `fe_users` SET usergroup = '" . implode(",",$merged) . "' WHERE uid = " . $lastuid;
    				$GLOBALS['TYPO3_DB']->sql_query($sql2);
    				$groups = array();
    			}else{
    				$sql2 = "UPDATE `fe_users` SET deleted = 1 WHERE uid = " . $lastuid;
    				$GLOBALS['TYPO3_DB']->sql_query($sql2);
    			}
    			$lastuid = $row["uid"];
    			$current = $row["username"];
    			$groups[] = $row["usergroup"];
    		}
    */

    }
}
?>
