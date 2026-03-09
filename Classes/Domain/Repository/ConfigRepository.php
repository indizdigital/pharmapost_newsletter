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
 use Phi\PhiNewsletter\Domain\Model\Config;
 use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
 use TYPO3\CMS\Extbase\Object\ObjectManager;
/**
 * The repository for Configs
 */
class ConfigRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    protected  $table = 'tx_phinewsletter_domain_model_config';
    /**
    * @deprecated
     * @param $settings
     * @return int
     */
    public function duplicateLast($settings = array())
    {
        exit;
        $queryConnection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table);
        $row = $this->findLast();

        if (!empty($row)) {
            unset($row['uid']);
            unset($row['selected']);
            $queryConnection->insert($this->table,$row);

        } else {
            $queryConnection->insert($this->table,array('subject0' => 'First Config', 'pid' => $settings['storagePid']));
        }
        return $queryConnection->lastInsertId();
    }

    /**
     * @param $uid
     * @param $settings
     * @return int
     */
    public function duplicateThis($uid, $settings = array())
    {
        $config = $this->findByUid($uid);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $res = $queryBuilder->insert($this->table)->values([
          "pid"=>$config["pid"],
          "emailfrom"=>$config["emailfrom"],
          "namefrom"=>$config["namefrom"],
          "subject0"=>$config["subject0"],
          "subject1"=>$config["subject1"],
          "subject2"=>$config["subject2"],
          "image0"=>$config["image0"],
          "image1"=>$config["image1"],
          "image2"=>$config["image2"],
          "replytoemail"=>$config["replytoemail"],
          "replytoname"=>$config["replytoname"],
          "statuspageid"=>$config["statuspageid"],
          "filestorage"=>$config["filestorage"],
          "configuration"=>$config["configuration"],
          "prefix0"=>$config["prefix0"],
          "prefix1"=>$config["prefix1"],
          "prefix2"=>$config["prefix2"],
          "url"=>$config["url"]
        ])->execute();

        $newConfigUid = $queryBuilder->getConnection()->lastInsertId();

        $imagetable = "sys_file_reference";

        $queryBuilderSysFile = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($imagetable);
        $res = $queryBuilderSysFile->select('*')->from($imagetable)->where(
          $queryBuilderSysFile->expr()->eq('tablenames', $queryBuilderSysFile->createNamedParameter("tx_phinewsletter_domain_model_config")),
          $queryBuilderSysFile->expr()->eq('uid_foreign', $queryBuilderSysFile->createNamedParameter($uid))

          )->orderBy("uid","ASC")->execute()->fetchAll();

        foreach($res as $row){
          $queryBuilderSysFile->insert($imagetable)->values([
            "pid"=>$row["pid"],
            "crdate"=>time(),
            "tstamp"=>time(),
            "cruser_id"=>$row["cruser_id"],
            "uid_local"=>$row["uid_local"],
            "uid_foreign"=>$newConfigUid,
            "fieldname"=>$row["fieldname"],
            "tablenames"=>$row["tablenames"],
            "sorting_foreign"=>$row["sorting_foreign"],
            "table_local"=>$row["table_local"]
          ])->execute();
        }
        return $newConfigUid;
    }

    /**
     * @param \bool $raw
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findLast($raw = true)
    {
        
      $emailtable = 'tx_phinewsletter_domain_model_emails';
      $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($emailtable);
      $queryBuilder = $queryBuilder->count("config")->addSelect('tx_phinewsletter_domain_model_emails.config')->from($emailtable)->where(
        $queryBuilder->expr()->gt('senttime', 0)
      )->groupBy('config')->orderBy("config","DESC")->setMaxResults(100);

       $configs = [];
        $res = $queryBuilder->execute();
        

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);

        $configCounter = 0;
        //select only configs, which are have more than 5 receivers.. 
        while($row = $res->fetch()){
          if($row["COUNT(`config`)"] > 5 && $configCounter < 3){
            $configs[] = $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($row["config"]));
            $configCounter++;
          }//
        }
        
        $queryBuilder = $queryBuilder->select('*')->from($this->table)->orWhere(
            ...$configs
        )->orderBy("uid","DESC")->setMaxResults(3);

      $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);
      $row = $dataMapper->map(
          Config::class,
          $queryBuilder->execute()->fetchAll()
      );
       
      return $row;
    }

    /**
     * @param \int $uid
     * @return \array
     */
    public function findByUid($uid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $res = $queryBuilder->select('*')->from($this->table)->where(
          $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
          )->execute();
          $row = [];
        if($row = $res->fetch()){
        }
        return $row;
    }

    /**
     * @param \int $selected
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findBySelected($selected)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);

        $queryBuilder->select('*')->from($this->table)->where(
          $queryBuilder->expr()->eq('selected', $queryBuilder->createNamedParameter($selected))
          )->orderBy("uid","ASC");

        $res = $dataMapper->map(
            Config::class,
            $queryBuilder->execute()->fetchAll()
        );
        return $res;
    }

    /**
     * findByIssent
     * @param \int $issent
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findByIssent($issent)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $dataMapper = GeneralUtility::makeInstance(ObjectManager::class)->get(DataMapper::class);
        //exclude the config which is used on the automated
        $queryBuilder->select('*')->from($this->table)->where(
          $queryBuilder->expr()->eq('issent', $queryBuilder->createNamedParameter($issent)),
          $queryBuilder->expr()->eq('selected', $queryBuilder->createNamedParameter(0)),
          $queryBuilder->expr()->neq('uid', 490)
          )->orderBy("uid","DESC")->setMaxResults(1);

        $res = $dataMapper->map(
            Config::class,
            $queryBuilder->execute()->fetchAll()
        );
        return $res;
    }

    /**
     * @param \int $uid
    * @param \int $issent
     * @return \void
     */
    public function updateIssent($uid,$issent)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $queryBuilder->update($this->table)->where(
          $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
          )->set('issent',1)->execute();

    }

}
