<?php

namespace Phi\PhiNewsletter\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \Phi\PhiNewsletter\Domain\Model\Stats.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class StatsTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \Phi\PhiNewsletter\Domain\Model\Stats
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \Phi\PhiNewsletter\Domain\Model\Stats();
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getUseridReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getUserid()
		);
	}

	/**
	 * @test
	 */
	public function setUseridForStringSetsUserid()
	{
		$this->subject->setUserid('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'userid',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getLanguageuidReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getLanguageuid()
		);
	}

	/**
	 * @test
	 */
	public function setLanguageuidForStringSetsLanguageuid()
	{
		$this->subject->setLanguageuid('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'languageuid',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getNewsidReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getNewsid()
		);
	}

	/**
	 * @test
	 */
	public function setNewsidForStringSetsNewsid()
	{
		$this->subject->setNewsid('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'newsid',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getIpReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getIp()
		);
	}

	/**
	 * @test
	 */
	public function setIpForStringSetsIp()
	{
		$this->subject->setIp('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'ip',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getEditionReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getEdition()
		);
	}

	/**
	 * @test
	 */
	public function setEditionForStringSetsEdition()
	{
		$this->subject->setEdition('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'edition',
			$this->subject
		);
	}
}
