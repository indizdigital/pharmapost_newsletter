<?php

defined('TYPO3') || die();


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
	'PhiNewsletter',
	'web',	 // Make module a submodule of 'web'
	'phinewsletter',	// Submodule key
	'',						// Position
	array(
		\Phi\PhiNewsletter\Controller\ConfigController::class => 'list,edit, show, update,proceed,load,new,create,delete',
		\Phi\PhiNewsletter\Controller\EmailsController::class => 'list,sentlist, new, create,show,selectitems,selectgroups',
		\Phi\PhiNewsletter\Controller\StatsController::class => 'list, csv,show',
		\Phi\PhiNewsletter\Controller\ImportController::class=>'loadcsv'
	),
	array(
		'access' => 'user,group',
		'icon'   => 'EXT:phi_newsletter/ext_icon.png',
		'labels' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_phinewsletter.xlf',
	)
);



(static function() {
				\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
					'PhiNewsletter',
					'Newsletter',
					'Newsletter Frontend Fn'
				);
				\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
					'PhiNewsletter',
					'NewsletterSender',
					'Newsletter Sender'
				);
				\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
					'PhiNewsletter',
					'NewsletterStatus',
					'Newsletter Status'
				);

				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('phi_newsletter', 'Configuration/TypoScript', 'Newsletter');

				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_phinewsletter_domain_model_config', 'EXT:phi_newsletter/Resources/Private/Language/locallang_csh_tx_phinewsletter_domain_model_config.xlf');
				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_phinewsletter_domain_model_config');

				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_phinewsletter_domain_model_emails', 'EXT:phi_newsletter/Resources/Private/Language/locallang_csh_tx_phinewsletter_domain_model_emails.xlf');
				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_phinewsletter_domain_model_emails');

				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_phinewsletter_domain_model_stats', 'EXT:phi_newsletter/Resources/Private/Language/locallang_csh_tx_phinewsletter_domain_model_stats.xlf');
				\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_phinewsletter_domain_model_stats');
})();



$pluginSignature = 'phinewsletter_newsletter';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:phi_newsletter/Configuration/Flexform/flexform.xml');
