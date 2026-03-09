<?php

$tempColumn = array (
	'language' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
		'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'special' => 'languages',
				'items' => [
						[
								'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
								-1,
								'flags-multiple'
						]
				],
				'default' => 0,
		],
	),
	'gender' => array(
		'exclude' => 1,
        'label' => 'Gender',
        'config' => array (
            'type' => 'radio',
            'items' => array (
                array('Mr', 'm'),
                array('Ms', 'w'),
            ),
        ),
	),
	'birthday' => array(
		'exclude' => 1,
        'label' => 'Birthday',
        'config' => array (
			'type' => 'input',
			'size' => 30,
			'max' => 255,
        ),
	),
	'doubleopthash' => array(
		'exclude' => 1,
        'label' => 'Double opt hash',
        'config' => array (
			'type' => 'input',
			'size' => 30,
			'max' => 255,
        ),
	)
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("fe_users",$tempColumn,1);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("fe_users","language");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("fe_users","gender");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("fe_users","birthday");
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("fe_users","doubleopthash");
