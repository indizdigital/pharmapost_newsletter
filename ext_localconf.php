<?php
defined('TYPO3') || die();

(static function() {

				\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
						'PhiNewsletter',
						'Newsletter',
						[
							Phi\PhiNewsletter\Controller\UserController::class => 'new,create,activate,status',
							Phi\PhiNewsletter\Controller\EmailsController::class => 'send,archived',
						],
						// non-cacheable actions
						[
							Phi\PhiNewsletter\Controller\UserController::class => 'new,create,activate,status',
							Phi\PhiNewsletter\Controller\EmailsController::class => 'send,archived',
						]
				);

				\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
						'PhiNewsletter',
						'NewsletterSender',
						[
							Phi\PhiNewsletter\Controller\EmailsController::class => 'sendall',
						],
						// non-cacheable actions
						[
							Phi\PhiNewsletter\Controller\EmailsController::class => 'sendall',
						]
				);

				\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
						'PhiNewsletter',
						'NewsletterStatus',
						[
							Phi\PhiNewsletter\Controller\UserController::class => 'status',
						],
						// non-cacheable actions
						[
							Phi\PhiNewsletter\Controller\UserController::class => 'status',
						]
				);

		    // wizards
		    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
		        'mod {
		            wizards.newContentElement.wizardItems.plugins {
		                elements {
		                    jobs {
		                        iconIdentifier = phi_newsletter-plugin-newsletter
		                        title = LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_newsletter.title
		                        description = LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_newsletter.description
		                        tt_content_defValues {
		                            CType = list
		                            list_type = phinewsletter_newsletter
		                        }
		                    }
		                }
		                show = *
		            }
		       }'
		    );
				$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

				$iconRegistry->registerIcon(
					'phi_newsletter-plugin-newsletter',
					\TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
					['source' => 'EXT:phi_newsletter/Resources/Public/Icons/phi_newsletter-plugin-newsletter.svg']
				);


})();


/*
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['newsletter_unsubscribe'] = 'EXT:phi_newsletter/Resources/PMX/util/newsletter_unsubscribe.php';
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['phinewsletter_subscribe'] = 'EXT:phi_newsletter/Resources/PMX/util/newsletter_subscribe.php';
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['newsletter_tracker'] = 'EXT:phi_newsletter/Resources/PMX/util/newsletter_tracker.php';*/
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['newsletter_openrate'] = 'EXT:phi_newsletter/Resources/PMX/util/newsletter_openrate.php';


?>
