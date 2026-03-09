<?php
namespace Phi\PhiNewsletter\Domain\Model;

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

/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
//class User extends \Tx_Extbase_Domain_Model_FrontendUser {
class User extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUser {


	/**
	 * language
	 *
	 * @var \int
	 */
	protected $language;


	/**
	 * gender
	 *
	 * @var \string
	 */
	protected $gender;


	/**
	 * birthday
	 *
	 * @var \int
	 */
	protected $birthday;


	/**
	 * doubleopthash
	 *
	 * @var \string
	 */
	protected $doubleopthash;

  /**
   * custombranchid
   *
   * @var \int
   */
  protected $custombranchid = 0;
  /**
   * customuserid
   *
   * @var \int
   */
  protected $customuserid = 0;

  /**
   * Returns the custombranchid
   * @param $custombranchid
   * @return \void
   */
  public function setCustombranchid($custombranchid)
  {
      $this->custombranchid = $custombranchid;
  }

  /**
   * Returns the custombranchid
   *
   * @return int $custombranchid
   */
  public function getCustombranchid()
  {
      return $this->custombranchid;
  }

  /**
   * Returns the customuserid
   * @param $customuserid
   * @return \void
   */
  public function setCustomuserid($customuserid)
  {
      $this->customuserid = $customuserid;
  }

  /**
   * Returns the customuserid
   *
   * @return int $customuserid
   */
  public function getCustomuserid()
  {
      return $this->customuserid;
  }

	/**
	 * Returns the language
	 *
	 * @return \string $language
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * Sets the language
	 *
	 * @param \string $language
	 * @return void
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}

	/**
	 * Returns the gender
	 *
	 * @return \string $gender
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * Sets the gender
	 *
	 * @param \string $gender
	 * @return void
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}

	/**
	 * Returns the birthday
	 *
	 * @return \string $birthday
	 */
	public function getBirthday() {
		return $this->birthday;
	}

	/**
	 * Sets the birthday
	 *
	 * @param \string $birthday
	 * @return void
	 */
	public function setBirthday($birthday) {
		$this->birthday = $birthday;
	}

	/**
	 * Returns the doubleopthash
	 *
	 * @return \string $doubleopthash
	 */
	public function getDoubleopthash() {
		return $this->doubleopthash;
	}

	/**
	 * Sets the doubleopthash
	 *
	 * @param \string $doubleopthash
	 * @return void
	 */
	public function setDoubleopthash($doubleopthash) {
		$this->doubleopthash = $doubleopthash;
	}
}
?>
