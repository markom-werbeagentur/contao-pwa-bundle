<?php

/**
 * Backend modules
 */

$GLOBALS['BE_MOD']['system']['huh_pwa_configurations'] = [
	'tables' => ['tl_pwa_configurations']
];



/**
 * Hooks
 */
\HeimrichHannot\UtilsBundle\Arrays\ArrayUtil::insertBeforeKey(
	$GLOBALS['TL_HOOKS']['generatePage'],
	'huh.head-bundle',
	'huh.pwa',
	['huh.pwa.listener.hook', 'onGeneratePage']
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_pwa_configurations'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel::class;
$GLOBALS['TL_MODELS']['tl_pwa_pushsubscriber'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaPushSubscriberModel::class;
$GLOBALS['TL_MODELS']['tl_pwa_pushnotifications'] = \HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel::class;

/**
 * Content Elements
 */
$GLOBALS['TL_CTE']['links'][\HeimrichHannot\ContaoPwaBundle\ContentElement\SubscribeButtonElement::TYPE] = \HeimrichHannot\ContaoPwaBundle\ContentElement\SubscribeButtonElement::class;

/**
 * Assets
 */
if (TL_MODE == 'BE') {
	$GLOBALS['TL_JAVASCRIPT']['huh.pwa.backend'] = 'bundles/heimrichhannotcontaopwa/js/huhPwaBackend.js';
	$GLOBALS['TL_CSS']['huh.pwa.backend'] = 'bundles/heimrichhannotcontaopwa/css/huhPwaBackend.css';
}

/**
 * Cronjobs
 */

$GLOBALS['TL_CRON']['monthly'][]    = ['huh.pwa.listener.commandscheduler', 'monthly'];
$GLOBALS['TL_CRON']['weekly'][]    = ['huh.pwa.listener.commandscheduler', 'weekly'];
$GLOBALS['TL_CRON']['daily'][]    = ['huh.pwa.listener.commandscheduler', 'daily'];
$GLOBALS['TL_CRON']['hourly'][]    = ['huh.pwa.listener.commandscheduler', 'hourly'];
$GLOBALS['TL_CRON']['minutely'][]    = ['huh.pwa.listener.commandscheduler', 'minutely'];
