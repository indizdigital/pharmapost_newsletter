<?php
namespace Phi\PhiNewsletter\Domain\Model;

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
 * Stats
 */
class Stats extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * user
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     */
    protected $user = 0;

    /**
     * crdate
     *
     * @var int
     */
    protected $crdate = 0;

    /**
     * sysLanguageUid
     *
     * @var \int
     */
    protected $sysLanguageUid = 0;

    /**
     * itemid
     *
     * @var string
     */
    protected $itemid = '';

    /**
     * ip
     *
     * @var string
     */
    protected $ip = '';

    /**
     * edition
     *
     * @var string
     */
    protected $edition = '';

    /**
     * userid
     *
     * @var string
     */
    protected $userid = '';

    /**
     * Returns the user
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the user
     *
     * @param string $user
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Returns the sysLanguageUid
     *
     * @return \string $sysLanguageUid
     */
    public function getSysLanguageUid()
    {
        return $this->sysLanguageUid;
    }

    /**
     * Sets the sysLanguageUid
     *
     * @param int $sysLanguageUid
     * @return void
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->sysLanguageUid = $sysLanguageUid;
    }

    /**
     * Returns the itemid
     *
     * @return string $itemid
     */
    public function getItemid()
    {
        return $this->itemid;
    }

    /**
     * Sets the itemid
     *
     * @param string $itemid
     * @return void
     */
    public function setItemid($itemid)
    {
        $this->newsid = $newsid;
    }

    /**
     * Returns the ip
     *
     * @return string $ip
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Sets the ip
     *
     * @param string $ip
     * @return void
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Returns the edition
     *
     * @return string $edition
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Sets the edition
     *
     * @param string $edition
     * @return void
     */
    public function setEdition($edition)
    {
        $this->edition = $edition;
    }

    /**
     * Returns the crdate
     *
     * @return int $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

}
