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
 * Test case for class \Phi\PhiNewsletter\Domain\Model\Config.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ConfigTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
	/**
	 * @var \Phi\PhiNewsletter\Domain\Model\Config
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = new \Phi\PhiNewsletter\Domain\Model\Config();
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getEmailfromReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getEmailfrom()
		);
	}

	/**
	 * @test
	 */
	public function setEmailfromForStringSetsEmailfrom()
	{
		$this->subject->setEmailfrom('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'emailfrom',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getNamefromReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getNamefrom()
		);
	}

	/**
	 * @test
	 */
	public function setNamefromForStringSetsNamefrom()
	{
		$this->subject->setNamefrom('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'namefrom',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getSubjectReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getSubject()
		);
	}

	/**
	 * @test
	 */
	public function setSubjectForStringSetsSubject()
	{
		$this->subject->setSubject('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'subject',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getReplytoemailReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getReplytoemail()
		);
	}

	/**
	 * @test
	 */
	public function setReplytoemailForStringSetsReplytoemail()
	{
		$this->subject->setReplytoemail('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'replytoemail',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getReplytonameReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getReplytoname()
		);
	}

	/**
	 * @test
	 */
	public function setReplytonameForStringSetsReplytoname()
	{
		$this->subject->setReplytoname('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'replytoname',
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
	public function getBackpageidReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getBackpageid()
		);
	}

	/**
	 * @test
	 */
	public function setBackpageidForStringSetsBackpageid()
	{
		$this->subject->setBackpageid('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'backpageid',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getUnsubscribepageidReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getUnsubscribepageid()
		);
	}

	/**
	 * @test
	 */
	public function setUnsubscribepageidForStringSetsUnsubscribepageid()
	{
		$this->subject->setUnsubscribepageid('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'unsubscribepageid',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getTemplateReturnsInitialValueForInt()
	{	}

	/**
	 * @test
	 */
	public function setTemplateForIntSetsTemplate()
	{	}

	/**
	 * @test
	 */
	public function getPrefixdefaultReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPrefixdefault()
		);
	}

	/**
	 * @test
	 */
	public function setPrefixdefaultForStringSetsPrefixdefault()
	{
		$this->subject->setPrefixdefault('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'prefixdefault',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPrefixenReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPrefixen()
		);
	}

	/**
	 * @test
	 */
	public function setPrefixenForStringSetsPrefixen()
	{
		$this->subject->setPrefixen('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'prefixen',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPrefixfrReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getPrefixfr()
		);
	}

	/**
	 * @test
	 */
	public function setPrefixfrForStringSetsPrefixfr()
	{
		$this->subject->setPrefixfr('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'prefixfr',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getUrlReturnsInitialValueForString()
	{
		$this->assertSame(
			'',
			$this->subject->getUrl()
		);
	}

	/**
	 * @test
	 */
	public function setUrlForStringSetsUrl()
	{
		$this->subject->setUrl('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'url',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getSelectedReturnsInitialValueForBool()
	{
		$this->assertSame(
			FALSE,
			$this->subject->getSelected()
		);
	}

	/**
	 * @test
	 */
	public function setSelectedForBoolSetsSelected()
	{
		$this->subject->setSelected(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'selected',
			$this->subject
		);
	}
}
