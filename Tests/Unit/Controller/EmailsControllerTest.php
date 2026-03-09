<?php
namespace Phi\PhiNewsletter\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016
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
 * Test case for class Phi\PhiNewsletter\Controller\EmailsController.
 *
 */
class EmailsControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @var \Phi\PhiNewsletter\Controller\EmailsController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('Phi\\PhiNewsletter\\Controller\\EmailsController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllEmailssFromRepositoryAndAssignsThemToView()
	{

		$allEmailss = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$emailsRepository = $this->getMock('Phi\\PhiNewsletter\\Domain\\Repository\\EmailsRepository', array('findAll'), array(), '', FALSE);
		$emailsRepository->expects($this->once())->method('findAll')->will($this->returnValue($allEmailss));
		$this->inject($this->subject, 'emailsRepository', $emailsRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('emailss', $allEmailss);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenEmailsToEmailsRepository()
	{
		$emails = new \Phi\PhiNewsletter\Domain\Model\Emails();

		$emailsRepository = $this->getMock('Phi\\PhiNewsletter\\Domain\\Repository\\EmailsRepository', array('add'), array(), '', FALSE);
		$emailsRepository->expects($this->once())->method('add')->with($emails);
		$this->inject($this->subject, 'emailsRepository', $emailsRepository);

		$this->subject->createAction($emails);
	}
}
