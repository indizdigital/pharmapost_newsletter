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
 * Test case for class Phi\PhiNewsletter\Controller\ConfigController.
 *
 */
class ConfigControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @var \Phi\PhiNewsletter\Controller\ConfigController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('Phi\\PhiNewsletter\\Controller\\ConfigController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllConfigsFromRepositoryAndAssignsThemToView()
	{

		$allConfigs = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$configRepository = $this->getMock('Phi\\PhiNewsletter\\Domain\\Repository\\ConfigRepository', array('findAll'), array(), '', FALSE);
		$configRepository->expects($this->once())->method('findAll')->will($this->returnValue($allConfigs));
		$this->inject($this->subject, 'configRepository', $configRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('configs', $allConfigs);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenConfigToView()
	{
		$config = new \Phi\PhiNewsletter\Domain\Model\Config();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('config', $config);

		$this->subject->showAction($config);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenConfigToConfigRepository()
	{
		$config = new \Phi\PhiNewsletter\Domain\Model\Config();

		$configRepository = $this->getMock('Phi\\PhiNewsletter\\Domain\\Repository\\ConfigRepository', array('add'), array(), '', FALSE);
		$configRepository->expects($this->once())->method('add')->with($config);
		$this->inject($this->subject, 'configRepository', $configRepository);

		$this->subject->createAction($config);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenConfigToView()
	{
		$config = new \Phi\PhiNewsletter\Domain\Model\Config();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('config', $config);

		$this->subject->editAction($config);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenConfigInConfigRepository()
	{
		$config = new \Phi\PhiNewsletter\Domain\Model\Config();

		$configRepository = $this->getMock('Phi\\PhiNewsletter\\Domain\\Repository\\ConfigRepository', array('update'), array(), '', FALSE);
		$configRepository->expects($this->once())->method('update')->with($config);
		$this->inject($this->subject, 'configRepository', $configRepository);

		$this->subject->updateAction($config);
	}
}
