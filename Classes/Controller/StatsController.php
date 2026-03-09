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
 * StatsController
 */
class StatsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
  	 * statsRepository
  	 *
  	 * @var \Phi\PhiNewsletter\Domain\Repository\StatsRepository
  	 * @TYPO3\CMS\Extbase\Annotation\Inject
  	 */
  	protected $statsRepository = NULL;

  	/**
  	 * statsRepository
  	 *
  	 * @param \Phi\PhiNewsletter\Domain\Repository\StatsRepository
  	 */
  	public function injectStatsRepository(\Phi\PhiNewsletter\Domain\Repository\StatsRepository $statsRepository){
  		$this->statsRepository = $statsRepository;
  	}

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $stats = $this->statsRepository->findAll();
        $this->view->assign('stats', $stats);
    }

    /**
     * action show
     *
     * @return void
     */
    public function showAction()
    {
        $args = $this->request->getArguments();
        $edition = $args['edition'];

        $stats = $this->statsRepository->findClickrateByEdition($edition);
		      $this->view->assign('stats', $stats);
        $this->view->assign('edition', $edition);
    }

    /**
     * action csv
     *
     * @return void
     */
    public function csvAction()
    {
      	header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="newsletter-clickedlink-statistics.csv"');
        header('Content-Type: application/octet-stream');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');/**/
        $csv = '';
        $csv .= '"' . $this->encodeString(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_phinewsletter_domain_model_stats.user', $this->extensionName)) . '";';
        $csv .= '"' . $this->encodeString(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_phinewsletter_domain_model_stats.languageuid', $this->extensionName)) . '";';
        $csv .= '"' . $this->encodeString(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_phinewsletter_domain_model_stats.newsid', $this->extensionName)) . '";';
        $csv .= '"' . $this->encodeString(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_phinewsletter_domain_model_stats.ip', $this->extensionName)) . '";';
        $csv .= '"' . $this->encodeString(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_phinewsletter_domain_model_stats.edition', $this->extensionName)) . '";';
        $csv .= '"' . $this->encodeString(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_phinewsletter_domain_model_stats.crdate', $this->extensionName)) . '"' . PHP_EOL;
        $index = 0;
        $args = $this->request->getArguments();
        if (isset($args['edition'])) {
            $edition = $args['edition'];
            $stats = $this->statsRepository->findByEdition($edition);
        } else {
            $stats = $this->statsRepository->findAll();
        }
        while ($index < $stats->count()) {
            $stat = $stats->current();
            //echo is_object($item->getModelname())?$this->encodeString($item->getModelname()->getName()):'';
            $csv .= '"' . ($stat->getUser() > 0?$this->encodeString($stat->getUser()->getEmail()):"Via HTML view (anonym)") . '";';
            $csv .= '"' . (strlen($stat->getSyslanguageuid()) == 0?"0":$this->encodeString($stat->getSyslanguageuid())) . '";';
            $csv .= '"' . ($stat->getNewsid() == 0?"'View Html'-Link":$this->encodeString($stat->getNewsid())) . '";';
            $csv .= '"' . $this->encodeString($stat->getIp()) . '";';
            $csv .= '"' . $this->encodeString($stat->getEdition()) . '";';
            $csv .= '"' . date('d.m.y h:m', $stat->getCrdate()) . '"' . PHP_EOL;
            $stats->next();
            $index++;
        }
        echo $csv;
        die;
    }

    /**
     * action encodeString
     *
     * @param string $str
     * @return string
     */
    public function encodeString($str)
    {
        return $str;
    }

}
