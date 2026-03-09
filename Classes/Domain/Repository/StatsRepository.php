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
class StatsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * function findAllEditions
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findAll()
    {

          $table = "tx_phinewsletter_domain_model_openrate";
          $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
          $st = $queryBuilder->select("*")->from($table)->orderBy("config","DESC")->setMaxResults(100)->execute();
          $all = [];
          while($row = $st->fetch()){
            if(isset($all[$row["config"]])){
              $all[$row["config"]]["openingrate"] ++;
            }else{
              $row["openingrate"] = 1;
              $all[$row["config"]] = $row;
            }
          }
		      return $all;

    }

    /**
     * function findByEdition
     *
	 * @param \int $edition
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findClickrateByEdition($edition)
    {

          $table = "tx_phinewsletter_domain_model_clickrate";
          $st = $this->findByEdition($edition,$table);

          while($row = $st->fetch()){
            if(isset($all[$row["itemid"]])){
              $all[$row["itemid"]]["clickrate"] ++;
            }else{
              $all[$row["itemid"]]["clickrate"] = 1;
            }
            $all[$row["itemid"]]["items"][] = $row;
          }
		      return $all;
    }

    /**
     * function findOpenrateByEdition
     *
	 * @param \int $edition
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findOpenrateByEdition($edition)
    {

          $table = "tx_phinewsletter_domain_model_openrate";
          $st = $this->findByEdition($edition,$table);

          while($row = $st->fetch()){
            if(isset($all[$row["itemid"]])){
              $all[$row["itemid"]]["openingrate"] ++;
            }else{
              $all[$row["itemid"]]["openingrate"] = 1;
            }
            $all[$row["itemid"]]["items"][] = $row;
          }
		      return $all;
    }

    /**
     * function findByEdition
     *
	    * @param \int $edition
 	    * @param \string $table
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResult
     */
    public function findByEdition($edition,$table)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $st = $queryBuilder->select($table . ".*","fe.email")->from($table)
         ->join(
            $table,
            'fe_users',
            'fe',
            $queryBuilder->expr()->eq('fe.uid', $queryBuilder->quoteIdentifier($table . '.user'))
         )->where(
          $queryBuilder->expr()->eq($table . '.edition', $queryBuilder->createNamedParameter($edition))
        )->execute();

        return $st;

    }


}
