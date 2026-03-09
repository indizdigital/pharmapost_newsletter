<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config',
		'label' => 'subject0',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'issent,emailfrom,namefrom,subject0,subject1,subject2,image0,image1,image2,replytoemail,replytoname,backpageid,unsubscribepageid,template,prefix0,prefix1,prefix2,url,selected,',
		'iconfile' => 'EXT:phi_newsletter/Resources/Public/Icons/tx_phinewsletter_domain_model_config.gif'
	),
	'interface' => array(
		'showRecordFieldList' => ' hidden,issent, emailfrom, namefrom, subject0,subject1,subject2,image0,image1,image2, replytoemail, replytoname, statuspageid, configuration,prefix0, prefix1, prefix2, url, selected,tosendtime,filestorage',
	),
	'types' => array(
		'1' => array('showitem' => 'hidden;;1, emailfrom, namefrom, subject0,subject1,subject2,image0,image1,image2, replytoemail, replytoname, statuspageid, configuration,prefix0, prefix1, prefix2, url, selected,tosendtime,filestorage --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(


		'hidden' => array(
			'exclude' => 1,
			'label' => 'Hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'issent' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.issent',
			'config' => array(
				'type' => 'check',
			),
		),

		'emailfrom' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.emailfrom',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'namefrom' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.namefrom',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'subject0' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.subject0',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'subject1' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.subject1',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'subject2' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.subject2',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'image0' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.image0',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
					'image0',
					[
							'appearance' => [
									'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
							],
							'maxitems' => 1
					],
					$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
			),
		),
		'image1' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.image1',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
					'image1',
					[
							'appearance' => [
									'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
							],
							'maxitems' => 1
					],
					$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
			),
		),
		'image2' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.image2',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
					'image2',
					[
							'appearance' => [
									'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
							],
							'maxitems' => 1
					],
					$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
			),
		),
		'filestorage' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.filestorage',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'replytoemail' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.replytoemail',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'replytoname' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.replytoname',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'statuspageid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.statuspageid',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'prefix0' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.prefix0',
			'config' => array(

						'type' => 'text',
						'enableRichtext' => true,
						'richtextConfiguration' => 'PharmapostNewsletter',
						'fieldControl' => [
								'fullScreenRichtext' => [
										'disabled' => false,
								],
						],
						'cols' => 40,
						'rows' => 15,
						'eval' => 'trim',
			)
		),
		'prefix1' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.prefix1',
			'config' => array(

						'type' => 'text',
						'enableRichtext' => true,
						'richtextConfiguration' => 'PharmapostNewsletter',
						'fieldControl' => [
								'fullScreenRichtext' => [
										'disabled' => false,
								],
						],
						'cols' => 40,
						'rows' => 15,
						'eval' => 'trim',
			)
		),
		'prefix2' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.prefix2',
			'config' => array(

						'type' => 'text',
						'enableRichtext' => true,
						'richtextConfiguration' => 'PharmapostNewsletter',
						'fieldControl' => [
								'fullScreenRichtext' => [
										'disabled' => false,
								],
						],
						'cols' => 40,
						'rows' => 15,
						'eval' => 'trim',
			)
		),
		'configuration' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.configuration',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			)
		),
		'url' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'tosendtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.tosendtime',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'selected' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_config.selected',
			'config' => array(
				'type' => 'check',
				'default' => 0
			)
		),

	),
);
