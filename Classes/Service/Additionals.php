<?php
namespace Phi\PhiNewsletter\Service;

use Phi\PhiMiscAmedisch\Service\JWT;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/***
 *
 * This file is part of the "Phi HCI Information" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018
 *
 ***/

/**
 * Additionals
 */
class Additionals implements \TYPO3\CMS\Core\SingletonInterface
{
    protected $passphrase = "IK=rnM?faiYKm9I672&d@nTUG!w8n{";

	/**
	 * function getAdditionals
	 *
	 * @param \array $params
	 * @param \int $pageuid
	 * @return \void
	 */
	public function getAdditionals($params,$pageuid){

		$payload = ["idf"=>$params["idf"],"username"=>$params["username"],"frequency_timespan"=>$params["automailfrequency"]];

		$token = $this->genereateJWTToken($payload);
		$ret = [
			"links"=>[
				"bag_link"=>[
					"uid"=>$pageuid,
					"additionalParams"=>[
						"tx_indizextendedarticledata_articledata[token]"=>$token
					],
					"hash"=>"tabContentItemBag"
				],
				"oos_link"=>[
					"uid"=>$pageuid,
					"additionalParams"=>[
						"tx_indizextendedarticledata_articledata[token]"=>$token
					],
					"hash"=>"tabContentItemOos"
				],
				"wfp_link"=>[
					"uid"=>$pageuid,
					"additionalParams"=>[
						"tx_indizextendedarticledata_articledata[token]"=>$token
					],
					"hash"=>"tabContentItemWvb"
				],
				"nla_link"=>[
					"uid"=>$pageuid,
					"additionalParams"=>[
						"tx_indizextendedarticledata_articledata[token]"=>$token
					],
					"hash"=>"tabContentItemNal"
				]
			]
		];

		return $ret;
	}

	/**
	* getJWTToken
	*
	* @param \array $payload
	* @return \string
	*/
	private function genereateJWTToken($payload)
	{
		$secret = "file:///home/www-data/openssl/jwt/private.pem";
		$oneday = 3600 * 24;
		$livetime = isset($payload["frequency_timespan"]) && $payload["frequency_timespan"]?$payload["frequency_timespan"] * $oneday:$oneday;

		$jwt = new JWT($secret, 'RS256', $livetime, 10,$this->passphrase);
    
		return $jwt->encode($payload);//token
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

	  /**
	   * function compareSingleAvailabilites
	   * load em from DB
	   *
	   * @return \void
	   */
	   public function parseConfig(&$config,$configstring)
	  {

				$conf = $configstring;
				unset($config['configuration']);
				$confArray = explode(PHP_EOL,$conf);
				foreach($confArray as $line){
					$lineArray = explode("=",$line);
					$key = trim($lineArray[0]);
					$config['configuration'][$key] = trim($lineArray[1]);
				}
	  }
}
