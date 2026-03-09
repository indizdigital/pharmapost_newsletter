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
use \Phi\PhiNewsletter\Domain\Model\Config;
 use TYPO3\CMS\Core\Utility\GeneralUtility;
 use TYPO3\CMS\Core\Database\ConnectionPool;
 use TYPO3\CMS\Core\Database\Connection;

 use Phi\PhiMiscAmedisch\Service\JWT;
/**
 * The repository for Emails
 */
class EmailsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    protected $defaultOrderings = array('senttime' => 'DESC');

    protected $hash_base = ":`OknkA.tfTXq4[P'Wb>u/@##^Hz}r";

    protected $passphrase = "IK=rnM?faiYKm9I672&d@nTUG!w8n{";

    /**
     * function loadUserGroup
     *
     * @param \array $config
     * @param \array $contents
     * @return \array
     */
    public function loadUserGroup($config,$contents = [])
    {
      $contentUids = [];
      foreach($contents as $uid=>$sorting){
        if($sorting > 0){
          $contentUids[] = $uid;
        }
      }
      $allowedUserGroups = [];
      $table = trim(Config::getConfigurationValueFromArray($config["configuration"],"databaseTable"));
      if(strlen($table)){

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilder
         ->select('*')
         ->from($table)
         ->where(
            $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($contentUids,Connection::PARAM_INT_ARRAY))
         )->execute();


         while($entity = $statement->fetch()){
           if(strlen($entity["fe_group"])){
             $contentGroups = explode(",",$entity["fe_group"]);
             if(empty($allowedUserGroups)){
               $allowedUserGroups = $contentGroups;
             }else{
               $allowedUserGroups = array_intersect($allowedUserGroups,$contentGroups);
             }
           }
         }
      }
      $groupsTable = "fe_groups";
      $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($groupsTable);
      $queryBuilder = $queryBuilder
       ->select('*')
       ->from($groupsTable);

       if(!empty($allowedUserGroups)){
         $queryBuilder = $queryBuilder->where(
           $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($allowedUserGroups,Connection::PARAM_INT_ARRAY))
         );
       }
       $statement = $queryBuilder->execute();

       return $statement->fetchAll();
    }
    /**
     * function updateSenttime
     *
     * @param \int $uid
     * @param \int $time
     * @return \void
     */
    public function updateSenttime($uid,$time = 0)
    {
        $table = "tx_phinewsletter_domain_model_emails";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        if($time == 0){
          $time = time();
        }
        $queryBuilder->update($table)->where(
          $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
        )->set('senttime',$time)->execute();

    }

    /**
     * function loadContents
     *

     * @param \Phi\PhiNewsletter\Domain\Model\Config $config
     * @return \array
     */
    public function loadContents($config)
    {
		    $table = trim(Config::getConfigurationValueFromArray($config["configuration"],"databaseTable"));
        if(strlen($table) == 0){
          return [];
        }
	      $orderField = Config::getConfigurationValueFromArray($config["configuration"],"databaseOrderField");
    		$orderField = strlen($orderField)?$orderField:"uid";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
	      $categoryTable = trim(Config::getConfigurationValueFromArray($config["configuration"],"databaseTableCategory"));
	      $itemCategoryField = Config::getConfigurationValueFromArray($config["configuration"],"categoryFieldInItemTable");

        if(isset($itemCategoryField) && isset($categoryTable)){
            $res =
              $queryBuilder->select($table .'.*')->
              from($table)->
              //where($queryBuilder->expr()->in($table .'.sys_language_uid', [0,-1]))->
              orderBy($table .".uid","DESC")->
              execute();
            $pageNews = [];

            $queryBuilderCategory = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($categoryTable);

            $resCat =
              $queryBuilderCategory->select($categoryTable .'.*')->
              from($categoryTable)->
              where($queryBuilderCategory->expr()->in($categoryTable .'.sys_language_uid', [0,-1]))->
              execute();

            $categories = [];
            while($row = $resCat->fetch()){
              $categories[$row["uid"]] = $row;
            }
            while($row = $res->fetch()){
              $rowCategories = explode(",",$row["category"]);
              $row["pagetitle"] = isset($categories[$rowCategories[0]]["name"])?$categories[$rowCategories[0]]["name"]:$categories[$rowCategories[0]]["title"];
              $pageNews[$rowCategories[0]][] = $row;
            }
        }else{
            $res =
              $queryBuilder->select($table .'.*','p.title AS pagetitle')->
              from($table)->
              leftJoin($table,"pages","p",$queryBuilder->expr()->eq('p.uid', $queryBuilder->quoteIdentifier($table . '.pid')))->
              where($queryBuilder->expr()->in($table .'.sys_language_uid', [0,-1]))->
              orderBy($table .".pid","ASC")->
              orderBy($table .".uid","DESC")->
              execute();
            $pageNews = [];

            while($row = $res->fetch()){
              $categories = explode(",",$row["category"]);
              $pageNews[$categories[0]][] = $row;
           }
        }
        return $pageNews;
    }

    /**
     * function addEmails
     *
     * @param \array $groups
     * @param \string $newsid
     * @param \int $config
     * @param \int $tosendtime
     * @param \int $pid
     * @return \void
     */
    public function addEmails($groups, $newsid, $config,$tosendtime, $pid = 0)
    {

            //CONTENTS
            $group = array_shift($groups);
            //just load one group
            $userResult = $this->loadUserOfGroup($group);
            while ($user = $userResult->fetch()) {
                $groupValues[] = [(string)$newsid,(int)$user['uid'],(int)$group,(int)$config,(int)$tosendtime,0];
            }


            if(count($groupValues)){

              $table = 'tx_phinewsletter_domain_model_emails';
              $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);
              $connection->bulkInsert(
                $table,
                $groupValues,
                [
                    'newsids',
                    'userid',
                    'groupid',
                    'config',
                    'tosendtime',
                    'senttime'
                 ],
                 [
                    Connection::PARAM_STR,
                    Connection::PARAM_INT,
                    Connection::PARAM_INT,
                    Connection::PARAM_INT,
                    Connection::PARAM_INT,
                    Connection::PARAM_INT
                 ]);
            }
    }

    /**
     * function addEmail
     *
     * @param \array $groups
     * @param \array $contents
     * @param \int $config
     * @param \int $tosendtime
     * @param \array $additionals
     * @param \int $pid
     * @return \void
     */
    public function addEmail($user, $contents, $config,$tosendtime,$groupid, $additionals = [],$pid = 0)
    {
            $newsids = implode(',', $contents);



              $table = 'tx_phinewsletter_domain_model_emails';
              $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
              $queryBuilder->insert($table)->values([
                  'newsids'=>$newsids,
                  'userid'=>$user,
                  'groupid'=>$groupid,
                  'config'=>$config,
                  'tosendtime'=>$tosendtime,
                  'additionals'=>$additionals,
                  'senttime'=>0
                ])->execute();
    }

    /**
     * function findDefaultUids
     *
     * @param \array $contents
     * @return \void
     */
    public function findDefaultUids($contents)
    {
             
          $table = 'tx_indiznews_domain_model_news';
          $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
          
          $res = $queryBuilder->select("*")->from($table)->where(
            $queryBuilder->expr()->in(
              'uid',
              $queryBuilder->createNamedParameter(
                  $contents,
                  Connection::PARAM_INT_ARRAY
                )
              )
          )->execute();

          $uids = []; 
          while($row = $res->fetch()){
            $uid = $row["l10n_parent"]?$row["l10n_parent"]:$row["uid"];

            $uids[$row["uid"]] = $uid;
            
          }

          $contentUids = [];
          foreach($contents as $uid){
            $origUid = $uids[$uid];
            if(!in_array($origUid,$contentUids)){
              $contentUids[] = $origUid;
            }
          }
          
          return $contentUids;
    }

    /**
     * function loadUserOfGroup
     *
     * @param \int $group
     * @return \array
     */
    public function loadUserOfGroup($group)
    {


        $table = "fe_users";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

            $where[] = $queryBuilder->expr()->like('usergroup', $queryBuilder->createNamedParameter($group. ',%'));
            $where[] = $queryBuilder->expr()->like('usergroup', $queryBuilder->createNamedParameter('%,' .$group. ',%'));
            $where[] = $queryBuilder->expr()->like('usergroup', $queryBuilder->createNamedParameter('%,' .$group));
            $where[] = $queryBuilder->expr()->eq('usergroup', $queryBuilder->createNamedParameter($group));


        $res = $queryBuilder->select('uid')->from($table)->orWhere(
          ...$where
        )->orderBy("uid","ASC")->execute();

        return $res;
    }

    /**
     * function findAll //todo
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findAll()
    {

        $table = "tx_phinewsletter_domain_model_emails";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $stats = [];
        $lastyear = time() - 3600 * 24 * 365;
        $res = $queryBuilder->select($table .'.*','cc.subject0 AS subject0','cc.subject1 AS subject1','cc.subject2 AS subject2')->
          from($table)->
          leftJoin($table,"tx_phinewsletter_domain_model_config","cc",$queryBuilder->expr()->eq('cc.uid', $queryBuilder->quoteIdentifier($table . '.config')))->
          orWhere(
            $queryBuilder->expr()->gt('senttime', $queryBuilder->createNamedParameter($lastyear)),
            $queryBuilder->expr()->eq('senttime', 0)
          )->
          orderBy("uid","DESC")->
          execute();

        while($row = $res->fetch()){
          if(isset($stats[$row["config"]])){
            $stats[$row["config"]]["amount"]++;
          }else{
            $row["amount"] = 1;
            $stats[$row["config"]] = $row;
          }

        }

        return $stats;
    }


    /**
     * function findByConfig
     *
     * @param \int $config
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findByConfig($config)
    {

        $table = "tx_phinewsletter_domain_model_emails";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $res = $queryBuilder->select($table .'.*','cc.subject0 AS subject0','cc.subject1 AS subject1','cc.subject2 AS subject2','fe.email AS usermail')->
          from($table)->
          leftJoin($table,"tx_phinewsletter_domain_model_config","cc",$queryBuilder->expr()->eq('cc.uid', $queryBuilder->quoteIdentifier($table . '.config')))->
          leftJoin($table,"fe_users","fe",$queryBuilder->expr()->eq('fe.uid', $queryBuilder->quoteIdentifier($table . '.userid')))->
          where(
            $queryBuilder->expr()->eq('config', $queryBuilder->createNamedParameter($config))
          )->
          orderBy("uid","DESC")->
          execute();
        return $res;
    }

    /**
     * function findAllNewsletters //todo
     *
	 * @param \array $settings
     * @return \array
     */
    public function findAllNewsletters($settings = array())
    {
      echo __function__;
      exit;
		$threshold = isset($settings["amountOfSentEmailsToRecognizeAsNonTestMail"])?$settings["amountOfSentEmailsToRecognizeAsNonTestMail"]:0;
		$limit = !empty($settings["flexform"]["displayLimit"])?' LIMIT 0, ' .$settings["flexform"]["displayLimit"]:"";

        $query = $this->createQuery();
        $query->statement('SELECT tx_phinewsletter_domain_model_emails.*,COUNT(tx_phinewsletter_domain_model_emails.edition) AS amount  FROM tx_phinewsletter_domain_model_emails  GROUP BY tx_phinewsletter_domain_model_emails.edition HAVING amount > '.$threshold.' ORDER BY uid DESC' . $limit);

        return $query->execute(true);
    }

    /**
     * function loadEmailsToSend
     *
	    * @param \string $taskid
     * @return \mysqli
     */
    public function loadEmailsToSend($taskid)
    {


        $table = "tx_phinewsletter_domain_model_emails";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);



        $res = $queryBuilder->select('uid')->from($table)->where(
          $queryBuilder->expr()->eq('uniqueid', $queryBuilder->createNamedParameter("")),
          $queryBuilder->expr()->eq('senttime', $queryBuilder->createNamedParameter(0)),
          $queryBuilder->expr()->lt('tosendtime', $queryBuilder->createNamedParameter(time()))
        )->orderBy("uid","ASC")->setMaxResults(20)->execute();

        while($row = $res->fetch()){
          $queryBuilderUpdate = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
          $queryBuilderUpdate->update($table,'e')->where(
            $queryBuilderUpdate->expr()->eq('uid', $queryBuilderUpdate->createNamedParameter($row["uid"]))
            )->set('e.uniqueid',$taskid)->execute();
        }

        $tableFeUsers = 'fe_users';

        $queryBuilderData = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $statement = $queryBuilderData
           ->select('tx_phinewsletter_domain_model_emails.*', 'fe.uid AS user','fe.last_name','fe.first_name','fe.language','fe.email','fe.gender','fe.usergroup','fe.username')
           ->from($table)
           ->join(
              $table,
              $tableFeUsers,
              'fe',
              $queryBuilderData->expr()->eq('fe.uid', $queryBuilderData->quoteIdentifier('tx_phinewsletter_domain_model_emails.userid'))
           )
           ->where(
              $queryBuilderData->expr()->eq('tx_phinewsletter_domain_model_emails.uniqueid', $queryBuilderData->createNamedParameter($taskid))
           )->execute();
      return $statement;
    }

    	/**
    	* getItems
    	*
    	* @param array $config
    	* @param string $actualids
    	* @param string $user
    	* @param string $readmore
    	* @param object $uriBuilder
    	* @param array $stdMarkers
    	* @param int $pid
    	* @return void
    	*/
        public function getItems($config,$actualids,$user,$readmore,$uriBuilder,$stdMarkers,$pid) {

      		$table = trim($config["configuration"]["databaseTable"]);
          if(strlen($table) == 0){
            return [];
          }
      		$edition = $stdMarkers["###EDITION###"];
          

          $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

            //  $where[] = $queryBuilder->expr()->like('usergroup', $queryBuilder->createNamedParameter($group. ',%'));

            $orWhere[] = $queryBuilder->expr()->in(
              'uid',
              $queryBuilder->createNamedParameter(
                  GeneralUtility::intExplode(',', $actualids, true),
                  Connection::PARAM_INT_ARRAY
               )
             );
             $orWhere[] = $queryBuilder->expr()->in(
               'l10n_parent',
               $queryBuilder->createNamedParameter(
                   GeneralUtility::intExplode(',', $actualids, true),
                   Connection::PARAM_INT_ARRAY
                )
              );


          $res = $queryBuilder->select('*')->from($table)->orWhere(
            ...$orWhere
          )->orderBy("uid","ASC")->execute();
            echo $res->num_rows;
          $newsletters = [];

            $teaser = "";
            $index = 0;
            while ($row = $res->fetch()) {
                $item_html = "";
    			       $replace = array();
        			foreach($row as $key=>$val){
        				if($key == "datum"){
        					$val = date("d.m.Y",$val);
        				}
        				if($key == "bodytext" || $key == "sidetext"){
        					//$val = strip_tags($val, ['br','a']);
        				}
        				$replace["###".strtoupper($key)."###"] = $val;
        			}
        			foreach($config["configuration"] as $key=>$val){
        				if(strpos($key,"marker") !== false){
        					$keyArray = explode(".",$key);
        					if(count($keyArray) > 2){
        						$condition = explode(":",$keyArray[2]);
        						if($row[$condition[0]] == $condition[1]){
        							$replace["###".strtoupper($keyArray[1])."###"] = $val;
        						}
        					}else{
        						$replace["###".strtoupper($keyArray[1])."###"] = $val;
        					}
        				}
        			}
              /*strtoupper is missing: bug in this if condition!!*/
      			if(strlen($config["configuration"]["altTeaserField"]) && strlen($replace["###".strtoupper($config["configuration"]["teaserField"])."###"]) == 0){

    			    $croppedteaser = (strip_tags($row[$config["configuration"]["altTeaserField"]]));
      				$croppedteaser = str_replace(array("<br />","<br>"),array("<br/>","<br/>"),nl2br($croppedteaser));
      				$croppedteaser = str_replace(array("<br/>","&nbsp;"),array(" <br/>"," "),($croppedteaser));
      				//$croppedteaserArray = explode(" ",$croppedteaser);
              $croppedteaserArray = preg_split("/[\s]+/", $croppedteaser);
      				$index = 0;
      				$croppedteaser = '';
              $altTeaserLength = isset($config["altTeaserLength"])?$config["altTeaserLength"]:256;
      				while(strlen($croppedteaser) < $altTeaserLength && $index < count($croppedteaserArray)){
      					$croppedteaser .= $croppedteaserArray[$index]  . " ";
      					$index++;
      				}
              if(strlen($croppedteaser) < strlen($croppedteaser)){
                $croppedteaser.= "...";
              }
              $replace["###".strtoupper($config["configuration"]["teaserField"])."###"] = trim($croppedteaser);

      			}

            //loadimage
            $image = $this->getSysImage($config,$row['uid'],'image',$table,680,$row['l10n_parent']);

            
      			$itemid = $row['sys_language_uid'] == 0 ? $row['uid'] : $row['l10n_parent'];
       			$link_content = $config['url'];
            $singlePageId = $row["pid"];
            if(isset($config["configuration"]["uidMapping." . $row["pid"]])){
              $singlePageId = trim($config["configuration"]["uidMapping." . $row["pid"]]);
            }



            $newsParams[$config["configuration"]["getParamScope"]."[".$config["configuration"]["redirectEntity"]."]"] = $row['uid'];
            $newsParams[$config["configuration"]["getParamScope"]."[controller]"] = $config["configuration"]["redirectController"];
              $newsParams[$config["configuration"]["getParamScope"]."[action]"] = $config["configuration"]["redirectAction"];

              $newsParams["L"] = $row['sys_language_uid'];
              //$newsParams[$config["configuration"]["getParamScope"]."[access_token]"] = $this->getAccessToken(["news_uid"=>$itemid]);
            $uri = $uriBuilder->reset()->setTargetPageUid($singlePageId)->setArguments(
              $newsParams
            );
            $itemurl = urlencode($uri->build());

          //  $link_content = $config['url'] . $itemurl;

            $link_content = $config['url'] . '/?newsletter_tracker[crc]='.hash('sha1',$this->hash_base).'&amp;newsletter_tracker[language]=' . $row['sys_language_uid'] . '&amp;no_cache=1&amp;newsletter_tracker[newsid]='.$itemid.'&amp;newsletter_tracker[user]='.$user.'&amp;newsletter_tracker[config]='.$config['uid'].'&amp;newsletter_tracker[pid]='.$pid.'&amp;newsletter_tracker[newslink]='.$itemurl;

      			/*if(strlen($config["configuration"]["additionalParams"])){
      				$params = explode(",",$config["configuration"]["additionalParams"]);
      				foreach($params as $p){
      					$link_content .= (isset($row[$p]) && strlen($row[$p]))?'&amp;newsletter_tracker[additionalParams:'.$p.']='.$row[$p]:'';
      				}
      			}*/

            $replace = array_merge($replace,$image);



            $imagetable = 'sys_file';
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($imagetable);
            $pdfRes = $queryBuilder->select($imagetable .'.identifier',$imagetable .'.name',$imagetable .'.storage','sfs.configuration')->from($imagetable)->leftJoin(
              $imagetable,"sys_file_reference","sfr",$queryBuilder->expr()->eq('sfr.uid_local', $queryBuilder->quoteIdentifier($imagetable . '.uid'))
              )->leftJoin(
                $imagetable,"sys_file_storage","sfs",$queryBuilder->expr()->eq('sys_file.storage', $queryBuilder->quoteIdentifier('sfs.uid'))
              )->where(
                $queryBuilder->expr()->eq('sfr.tablenames', $queryBuilder->createNamedParameter($table)),
                $queryBuilder->expr()->eq('sfr.fieldname', $queryBuilder->createNamedParameter('pdf')),
                $queryBuilder->expr()->eq('sfr.uid_foreign', $queryBuilder->createNamedParameter($row['uid']))
              )->orderBy("sfr.sorting_foreign","ASC")->setMaxResults(1)->execute();
              if($pdfRow = $pdfRes->fetch()){
                $xml = simplexml_load_string($pdfRow["configuration"]);
                foreach($xml->data->sheet->language->field as $afield){
                  if("basePath" == (string)($afield["index"])){
                    $replace["###PDF_SRC###"] = $config['url'] . "/" . $afield->value. $pdfRow["identifier"];;
                  }
                }

              }

                $replace["###MORE_LINK###"] = $link_content;
  			        $replace["###READ_MORE###"] = $readmore[$row['sys_language_uid']];

                if(!$row["pdf"]){
                  $replace["###INLINESTYLE###"] = 'display:none';
                }else{
                  $replace["###INLINESTYLE###"] = '';
                }

                //$content = addslashes($content);

                $orderid = in_array($row['sys_language_uid'],array(0,-1)) ? $row['uid'] : $row['l10n_parent'];

			         $languageId = $row['sys_language_uid'] > -1?$row['sys_language_uid']:"ALL";
                //$newsletters[$orderid][$languageId]['content'] = ($content);
                $newsletters[$orderid][$languageId]['content'] = ($replace);
                $index++;
            } 
            return $newsletters;
        }
    	/**
    	* getItems
    	*
    	* @param array $config
    	* @param string $actualids
    	* @param string $user
    	* @param string $readmore
    	* @param object $uriBuilder
    	* @param array $stdMarkers
    	* @param int $pid
    	* @return void
    	*/
        public function getFluidItems($config,$actualids,$user,$readmore,$uriBuilder,$stdMarkers,$pid) {

      		$table = trim($config["configuration"]["databaseTable"]);
          if(strlen($table) == 0){
            return [];
          }
      		$edition = $stdMarkers["EDITION"];


          $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

            //  $where[] = $queryBuilder->expr()->like('usergroup', $queryBuilder->createNamedParameter($group. ',%'));

            $orWhere[] = $queryBuilder->expr()->in(
              'uid',
              $queryBuilder->createNamedParameter(
                  GeneralUtility::intExplode(',', $actualids, true),
                  Connection::PARAM_INT_ARRAY
               )
             );
             $orWhere[] = $queryBuilder->expr()->in(
               'l10n_parent',
               $queryBuilder->createNamedParameter(
                   GeneralUtility::intExplode(',', $actualids, true),
                   Connection::PARAM_INT_ARRAY
                )
              );


          $res = $queryBuilder->select('*')->from($table)->orWhere(
            ...$orWhere
          )->orderBy("uid","ASC")->execute();

          $newsletters = [];

            $teaser = "";
            $index = 0;
            while ($row = $res->fetch()) {
                $item_html = "";
    			       $replace = array();
        			foreach($row as $key=>$val){
        				if($key == "datum"){
        					$val = date("d.m.Y",$val);
        				}
        				if($key == "bodytext" || $key == "sidetext"){
        					//$val = strip_tags($val, ['br','a']);
        				}
        				$replace["".strtoupper($key).""] = $val;
        			}
        			foreach($config["configuration"] as $key=>$val){
        				if(strpos($key,"marker") !== false){
        					$keyArray = explode(".",$key);
        					if(count($keyArray) > 2){
        						$condition = explode(":",$keyArray[2]);
        						if($row[$condition[0]] == $condition[1]){
        							$replace["".strtoupper($keyArray[1]).""] = $val;
        						}
        					}else{
        						$replace["".strtoupper($keyArray[1]).""] = $val;
        					}
        				}
        			}
              /*strtoupper is missing: bug in this if condition!!*/
      			if(strlen($config["configuration"]["altTeaserField"]) && strlen($replace["".strtoupper($config["configuration"]["teaserField"]).""]) == 0){

    			    $croppedteaser = (strip_tags($row[$config["configuration"]["altTeaserField"]]));
      				$croppedteaser = str_replace(array("<br />","<br>"),array("<br/>","<br/>"),nl2br($croppedteaser));
      				$croppedteaser = str_replace(array("<br/>","&nbsp;"),array(" <br/>"," "),($croppedteaser));
      				//$croppedteaserArray = explode(" ",$croppedteaser);
              $croppedteaserArray = preg_split("/[\s]+/", $croppedteaser);
      				$index = 0;
      				$croppedteaser = '';
              $altTeaserLength = isset($config["altTeaserLength"])?$config["altTeaserLength"]:256;
      				while(strlen($croppedteaser) < $altTeaserLength && $index < count($croppedteaserArray)){
      					$croppedteaser .= $croppedteaserArray[$index]  . " ";
      					$index++;
      				}
              if(strlen($croppedteaser) < strlen($croppedteaser)){
                $croppedteaser.= "...";
              }
              $replace["".strtoupper($config["configuration"]["teaserField"]).""] = trim($croppedteaser);

      			}

            //loadimage
            $image = $this->getFluidImage($config,$row['uid'],'image',$table,680,$row['l10n_parent']);


      			$itemid = $row['sys_language_uid'] == 0 ? $row['uid'] : $row['l10n_parent'];
       			$link_content = $config['url'];
            $singlePageId = $row["pid"];
            if(isset($config["configuration"]["uidMapping." . $row["pid"]])){
              $singlePageId = trim($config["configuration"]["uidMapping." . $row["pid"]]);
            }



            $newsParams[$config["configuration"]["getParamScope"]."[".$config["configuration"]["redirectEntity"]."]"] = $row['uid'];
            $newsParams[$config["configuration"]["getParamScope"]."[controller]"] = $config["configuration"]["redirectController"];
              $newsParams[$config["configuration"]["getParamScope"]."[action]"] = $config["configuration"]["redirectAction"];

              $newsParams["L"] = $row['sys_language_uid'];
              //$newsParams[$config["configuration"]["getParamScope"]."[access_token]"] = $this->getAccessToken(["news_uid"=>$itemid]);
            $uri = $uriBuilder->reset()->setTargetPageUid($singlePageId)->setArguments(
              $newsParams
            );
            $itemurl = urlencode($uri->build());


            $link_content = $config['url'] . '/?newsletter_tracker[crc]='.hash('sha1',$this->hash_base).'&amp;newsletter_tracker[language]=' . $row['sys_language_uid'] . '&amp;no_cache=1&amp;newsletter_tracker[newsid]='.$itemid.'&amp;newsletter_tracker[user]='.$user.'&amp;newsletter_tracker[config]='.$config['uid'].'&amp;newsletter_tracker[pid]='.$pid.'&amp;newsletter_tracker[newslink]='.$itemurl;


            $replace = array_merge($replace,$image);



            $imagetable = 'sys_file';
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($imagetable);
            $pdfRes = $queryBuilder->select($imagetable .'.identifier',$imagetable .'.name',$imagetable .'.storage','sfs.configuration')->from($imagetable)->leftJoin(
              $imagetable,"sys_file_reference","sfr",$queryBuilder->expr()->eq('sfr.uid_local', $queryBuilder->quoteIdentifier($imagetable . '.uid'))
              )->leftJoin(
                $imagetable,"sys_file_storage","sfs",$queryBuilder->expr()->eq('sys_file.storage', $queryBuilder->quoteIdentifier('sfs.uid'))
              )->where(
                $queryBuilder->expr()->eq('sfr.tablenames', $queryBuilder->createNamedParameter($table)),
                $queryBuilder->expr()->eq('sfr.fieldname', $queryBuilder->createNamedParameter('pdf')),
                $queryBuilder->expr()->eq('sfr.uid_foreign', $queryBuilder->createNamedParameter($row['uid']))
              )->orderBy("sfr.sorting_foreign","ASC")->setMaxResults(1)->execute();
              if($pdfRow = $pdfRes->fetch()){
                $xml = simplexml_load_string($pdfRow["configuration"]);
                foreach($xml->data->sheet->language->field as $afield){
                  if("basePath" == (string)($afield["index"])){
                    $replace["PDF_SRC"] = $config['url'] . "/" . $afield->value. $pdfRow["identifier"];;
                  }
                }

              }
                $replace["MORE_LINK"] = $link_content;
  			        $replace["READ_MORE"] = $readmore[$row['sys_language_uid']];


                //$content = addslashes($content);

                $orderid = in_array($row['sys_language_uid'],array(0,-1)) ? $row['uid'] : $row['l10n_parent'];

			         $languageId = $row['sys_language_uid'] > -1?$row['sys_language_uid']:"ALL";
                //$newsletters[$orderid][$languageId]['content'] = ($content);
                $newsletters[$orderid][$languageId]['content'] = ($replace);
                $index++;
            }
            return $newsletters;
        }

    	/**
    	* copyResizedImage
    	*
    	* @param string $base
      * @param string $path
      * @param string $src
    	* @param string $dstWidth
    	* @param string $src
    	* @return string
    	*/
        function copyResizedImage($base,$path,$src,$dstWidth) {

    		$imagesize = getimagesize($path . $src);
    		$sourceWidth = $imagesize[0];
            $sourceHeight = $imagesize[1];

            $scale = $sourceWidth /$dstWidth;
    		$dstHeight = $sourceHeight / $scale;
            $srcName = $src;
            $dstName = "newsimage_" . substr($src,strrpos($src,"/") + 1); //new image name

    		$dstPath = "uploads/phi_newsletter";


            if (!is_dir($base . $dstPath)) {
                //return $dstName;
                die($base . $dstPath. " does not exist");
            }
            if (file_exists($base  . $dstPath . "/" . $dstName)) {
                //return $dstName;
                unlink($base . $dstPath . "/" . $dstName);

            }
            if (count(gd_info()) > 0) {
                switch (substr($src, strlen($src) - 3)) {
                    case "JPG":
                    case "PEG":
                    case "jpg":
                    case "peg":
                        $src = imagecreatefromjpeg($path . $src);
                        break;
                    case "gif":
                        $src = imagecreatefromgif($path . $src);
                        break;
                    case "png":
                    case "PNG":
                        $src = imagecreatefrompng($path . $src);
                        break;
                    default:
                        return "";
                }
                $dst = imagecreatetruecolor($dstWidth, $dstHeight);

                imagealphablending($dst, false);
                imagesavealpha($dst,1);
                $rClip = $dstHeight / $dstWidth;


                $k = ($rClip < $rSource) ? $sourceWidth / $dstWidth : $sourceHeight / $dstHeight;

                $w = $k * $dstWidth;
                $h = $k * $dstHeight;

                $x = ($sourceWidth - $w) / 2;
                $y = ($sourceHeight - $h) / 2;

                if ($newImage = imagecopyresampled($dst, $src, 0, 0, $x, $y, $dstWidth, $dstHeight, $w, $h)) {// imagecopyresampled($dst,$src,0,0,$x,$y,$dstWidth,$dstHeight
                    switch (substr($srcName, strlen($srcName) - 3)) {
                        case "JPG":
                        case "jpg":
                        case "PEG":
                        case "peg":
                            if (!imagejpeg($dst, $base . $dstPath . "/" . $dstName)) {
                                return "";
                            }
                            break;
                        case "gif":
                            if (!imagegif($dst, $base . $dstPath . "/" . $dstName)) {
                                return "";
                            }
                            break;
                          case "png":
                          case "PNG":
                            if (!imagepng($dst, $base . $dstPath . "/" . $dstName)) {
                                return "";
                            }
                            break;
                        default:
                            return "";
                    }
                }
            } else {
                return "";
            }
            imagedestroy($dst);
            imagedestroy($src);
            return  $dstPath . "/" . $dstName;
        }

        public function getSysImage($config,$itemid,$fieldname,$table,$width = 1600,$parentL10nItemId = 0){


          $resizedImage = "";
          $img = [];

          $path = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/fileadmin';

          $imageRow = $this->loadImage($config,$itemid,$fieldname,$table,$width);
          if(!$imageRow && $parentL10nItemId){
            $imageRow = $this->loadImage($config,$parentL10nItemId,$fieldname,$table,$width);
          }
          if($imageRow){
            $base = dirname($path)."/";
            $resizedImage = $this->copyResizedImage($base,$path,$imageRow["identifier"],$width);
            $img["###IMAGE_NAME###"] = $imageRow["name"];
            $img["###IMAGE_SRC###"] = $config['url'] . "/" . $resizedImage."?random=" . rand();
            //$replace["###IMAGE_TAG###"] = '<img src="'.$replace["###IMAGE_SRC###"].'" alt="'.$replace["###IMAGE_NAME##"].'" class="fullwidth" width="580"/>';
          }
          return $img;
      }

      public function getFluidImage($config,$itemid,$fieldname,$table,$width = 1600,$parentL10nItemId = 0){


          $resizedImage = "";
          $img = [];

          $path = \TYPO3\CMS\Core\Core\Environment::getPublicPath() . '/fileadmin';

          $imageRow = $this->loadImage($config,$itemid,$fieldname,$table,$width);
          if(!$imageRow && $parentL10nItemId){
            $imageRow = $this->loadImage($config,$parentL10nItemId,$fieldname,$table,$width);
          }
          if($imageRow){
            $base = dirname($path)."/";
            $resizedImage = $this->copyResizedImage($base,$path,$imageRow["identifier"],$width);
            $img["IMAGE_NAME"] = $imageRow["name"];
            $img["IMAGE_SRC"] = $config['url'] . "/" . $resizedImage."?random=" . rand();
            //$replace["###IMAGE_TAG###"] = '<img src="'.$replace["###IMAGE_SRC###"].'" alt="'.$replace["###IMAGE_NAME##"].'" class="fullwidth" width="580"/>';
          }
          return $img;
        }

        public function loadImage($config,$itemid,$fieldname,$table,$width = 1600){
          $imagetable = 'sys_file';
          $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($imagetable);
          $imageRes = $queryBuilder->select($imagetable .'.identifier',$imagetable .'.name',$imagetable .'.storage')->from($imagetable)->leftJoin(
            $imagetable,"sys_file_reference","sfr",$queryBuilder->expr()->eq('sfr.uid_local', $queryBuilder->quoteIdentifier($imagetable . '.uid'))
            )->where(
              $queryBuilder->expr()->eq('sfr.tablenames', $queryBuilder->createNamedParameter($table)),
              $queryBuilder->expr()->eq('sfr.fieldname', $queryBuilder->createNamedParameter($fieldname)),
              $queryBuilder->expr()->eq('sfr.uid_foreign', $queryBuilder->createNamedParameter($itemid))
            )->orderBy("sfr.sorting_foreign","ASC")->setMaxResults(1)->execute();

          return $imageRes->fetch();
        }
      	/**
      	* generateEmailContents
      	*
      	* @param array $newsletter
      	* @param array $id_order
      	* @param array $config
      	* @param string $item,
      	* @param string $item2,
      	* @param string $subheadertemplate,
      	* @param string $spacertemplate,
      	* @param array $config
      	* @param array $stdMarkers
      	* @param string $lastItemOdd,
      	* @return void
      	*/
        function generateEmailContents($newsletter,$id_order,$config,$item,$item2,$subheadertemplate,$spacertemplate,$stdMarkers,$lastItemOdd) {
            $mailcontent = "";
            $mailaltcontent = "";
            if (!is_array($id_order)) {
                return array();
            }
            $subheader = [];
            foreach($config as $key=>$title){
              if(strpos($key,"categoryname") !== false){
                $s = explode(".",$key);
                $subheader[$s[1]][$s[2]] = $title;
              }
            }
          	$contents = array();
          	$contentCounter = array();
            $index = 0;
            foreach ($id_order as $id) {
              if(isset($newsletter[$id])){
                  $l18n_entries = $newsletter[$id];
            			foreach ($l18n_entries as $lang => $entry) {

                    $replace = $entry['content'];
                    $catUids = explode(",",$entry["content"]["###CATEGORY###"]);

                    if(isset($subheader[$catUids[0]][$lang])){


                			$contents[$lang]["content"] .= str_replace("###SUBHEADER###",$subheader[$catUids[0]][$lang],$subheadertemplate);
                			$replace["###SUBHEADER###"] = $subheader[$lang][$index];
                      if(isset($config["showSubheaderOnce"]) && $config["showSubheaderOnce"] == "1"){
                        unset($subheader[$catUids[0]][$lang]);
                      }
                  }else{
                      $contents[$lang]["content"] .= $spacertemplate;
                  }


                    $itemfile = ($index % 2 == 0)?$item:$item2;

                    $content = str_replace(array_keys($replace), array_values($replace), $itemfile);
                    //replace lang markers afterwards!
                    $content = str_replace(array_keys($stdMarkers), array_values($stdMarkers), $content);
                    //do it twice to use markers in language files
                    $content = str_replace(array_keys($stdMarkers), array_values($stdMarkers), $content);


            				$contents[$lang]["content"] .= $content;
                    $contentCounter[$lang] = isset($contentCounter[$lang])?$contentCounter[$lang]+1:1;
                  }
                  $index++;
              }
            }
            foreach($contentCounter as $lang=>$count){
              if($count % 2 == 1 && strlen($lastItemOdd)){
                $contents[$lang]["content"] .= $lastItemOdd;
              }
            }
            return $contents;
        }
      	/**
      	* generateEmailContentMarkers
      	*
      	* @param array $newsletter
      	* @param array $id_order
      	* @param array $config
      	* @param string $item,
      	* @param string $item2,
      	* @param string $subheadertemplate,
      	* @param string $spacertemplate,
      	* @param array $config
      	* @param array $stdMarkers
      	* @param string $lastItemOdd,
      	* @return void
      	*/
        function generateEmailContentMarkers($newsletter,$id_order,$config,$item,$item2,$subheadertemplate,$spacertemplate,$stdMarkers,$lastItemOdd) {

            if (!is_array($id_order)) {
                return array();
            }
            $subheader = [];
            foreach($config as $key=>$title){
              if(strpos($key,"categoryname") !== false){
                $s = explode(".",$key);
                $subheader[$s[1]][$s[2]] = $title;
              }
            }
          	$contents = array();
          	$contentCounter = array();
            $index = 0;
            foreach ($id_order as $id) {
              if(isset($newsletter[$id])){
                  $l18n_entries = $newsletter[$id];
    			foreach ($l18n_entries as $lang => $entry) {

                    $replace = $entry['content'];
                    $catUids = explode(",",$entry["content"]["###CATEGORY###"]);

                    /*if(isset($subheader[$catUids[0]][$lang])){


                			$contents[$lang]["content"] .= str_replace("###SUBHEADER###",$subheader[$catUids[0]][$lang],$subheadertemplate);
                			$replace["###SUBHEADER###"] = $subheader[$lang][$index];
                      if(isset($config["showSubheaderOnce"]) && $config["showSubheaderOnce"] == "1"){
                        unset($subheader[$catUids[0]][$lang]);
                      }
                  }else{
                      $contents[$lang]["content"] .= $spacertemplate;
                  }*/
 
                    $itemfile = ($index % 2 == 0)?$item:$item2;

                    $content = str_replace(array_keys($replace), array_values($replace), $itemfile);
                    //replace lang markers afterwards!
                    $content = str_replace(array_keys($stdMarkers), array_values($stdMarkers), $content);
                    //do it twice to use markers in language files
                    $content = str_replace(array_keys($stdMarkers), array_values($stdMarkers), $content);


    				$contents[$lang][] = $replace;
                  }
                  $index++;
              }
            }
            return $contents;
        }

        /**
         * function isNewslettergroup //todo
         *
    	 * @param \int $groupuid
       * @param \int $grouppid
         * @return \bool
         */
        public function isNewslettergroup($groupuid,$grouppid)
        {
          $table = "fe_groups";
          $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);


          $res = $queryBuilder->select('*')->
            from($table)->
          where(
            $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($groupuid)),
              $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($grouppid))
            )->execute();

            return $res->rowCount() > 0;
        }


	  /**
	   * function compareSingleAvailabilites
	   * load em from DB
	   *
	   * @return \array
	   */
	   public function getNewsContents($cat,$table,$lang)
	  {

			    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);


			    $statement = $queryBuilder->select('*')->from($table)->where(
							$queryBuilder->expr()->like('sys_language_uid', $queryBuilder->createNamedParameter($lang)),
							$queryBuilder->expr()->orX(
					       $queryBuilder->expr()->like('category', $queryBuilder->createNamedParameter("%," . $cat.",%")),
					        $queryBuilder->expr()->like('category', $queryBuilder->createNamedParameter("%," . $cat)),
					        $queryBuilder->expr()->like('category', $queryBuilder->createNamedParameter($cat . ",%")),
					        $queryBuilder->expr()->eq('category', $queryBuilder->createNamedParameter($cat))
					    )
						)->execute();

				$allnews = [];
				while($news = $statement->fetch()){
					$allnews[$news["uid"]] = $news;
				}
		    return $allnews;
	  }
}
