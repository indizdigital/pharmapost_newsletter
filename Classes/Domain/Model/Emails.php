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
 * Emails
 */
class Emails extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * user
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $user = '';

    /**
     * newsids
     *
     * @var string
     */
    protected $newsids = '';

    /**
     * senttime
     *
     * @var int
     */
    protected $senttime = 0;

    /**
     * edition
     *
     * @var string
     */
    protected $edition = '';

    /**
     * config
     *
     * @var \Phi\PhiNewsletter\Domain\Model\Config
     */
    protected $config = NULL;

    /**
     * userid
     *
     * @var string
     */
    protected $userid = '';


    /**
     * additionals
     *
     * @var string
     */
    protected $additionals = '';

    /**
     * Returns the newsids
     *
     * @return string $newsids
     */
    public function getNewsids()
    {
        return $this->newsids;
    }

    /**
     * Sets the newsids
     *
     * @param string $newsids
     * @return void
     */
    public function setNewsids($newsids)
    {
        $this->newsids = $newsids;
    }

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
     * Returns the userid
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Sets the user
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     * @return void
     */
    public function setUser(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user)
    {
        $this->user = $user;
    }

    /**
     * Returns the senttime
     *
     * @return int $senttime
     */
    public function getSenttime()
    {
        return $this->senttime;
    }

    /**
     * Sets the senttime
     *
     * @param int $senttime
     * @return void
     */
    public function setSenttime($senttime)
    {
        $this->senttime = $senttime;
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
     * Returns the config
     *
     * @return \Phi\PhiNewsletter\Domain\Model\Config $config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the config
     *
     * @param \Phi\PhiNewsletter\Domain\Model\Config $config
     * @return void
     */
    public function setConfig(\Phi\PhiNewsletter\Domain\Model\Config $config)
    {
        $this->config = $config;
    }


    /**
     * gets the additionals
     *
     * @return \array
     */
    public function getAdditionals($additionals)
    {
        if(strlen($this->additionals)){
		return json_decode($this->additionals,true);
	}
	return [];
    }

    /**
     * Sets the additionals
     *
     * @param \array $additionals
     * @return void
     */
    public function setAdditionals($additionals)
    {
        $this->additionals = empty($additionals)?"":json_encode($additionals);
    }
}
