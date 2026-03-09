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
 * Test case for class \Phi\PhiNewsletter\Domain\Model\Emails.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class EmailsTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \Phi\PhiNewsletter\Domain\Model\Emails
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \Phi\PhiNewsletter\Domain\Model\Emails();
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getNewsidsReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getNewsids()
		);
	}

	/**
	 * @test
	 */
	public function setNewsidsForStringSetsNewsids()
	{
		$this->subject->setNewsids('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'newsids',
			$this->subject
		);
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
	public function getSenttimeReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getSenttime()
		);
	}

	/**
	 * @test
	 */
	public function setSenttimeForStringSetsSenttime()
	{
		$this->subject->setSenttime('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'senttime',
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

	/**
	 * @test
	 */
	public function getConfigReturnsInitialValueForConfig()
	{
		$this->assertEquals(
			NULL,
			$this->subject->getConfig()
		);
	}

	/**
	 * @test
	 */
	public function setConfigForConfigSetsConfig()
	{
		$configFixture = new \Phi\PhiNewsletter\Domain\Model\Config();
		$this->subject->setConfig($configFixture);

		$this->assertAttributeEquals(
			$configFixture,
			'config',
			$this->subject
		);
	}
}
