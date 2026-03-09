<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_emails',
		'label' => 'newsids',
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
		'searchFields' => 'newsids,userid,senttime,edition,config,',
		'iconfile' => 'EXT:phi_newsletter/Resources/Public/Icons/tx_phinewsletter_domain_model_emails.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, hidden, newsids, userid, senttime, edition, config',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1,hidden;;1, newsids, userid, senttime, edition, config, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(

		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
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
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
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

		'newsids' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_emails.newsids',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'userid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_emails.userid',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'senttime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_emails.senttime',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'edition' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_emails.edition',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'config' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:phi_newsletter/Resources/Private/Language/locallang_db.xlf:tx_phinewsletter_domain_model_emails.config',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_phinewsletter_domain_model_config',
				'minitems' => 0,
				'maxitems' => 1,
				'appearance' => array(
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				),
			),
		),

	),
);
