<?php

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['system']['huh_pwa'] = [
	'tables' => ['tl_pwa_pushsubscriber']
];


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['generatePage']['huh.pwa'] = ['huh.pwa.listener.hook', 'onGeneratePage'];
$GLOBALS['TL_HOOKS']['getUserNavigation'][]     = ['huh.pwa.listener.usernavigation', 'onGetUserNavigation'];


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_pwa_pushsubscriber'] = \HeimrichHannot\ContaoPwaBundle\Model\PushSubscriberModel::class;

/**
 * Content Elements
 */
$GLOBALS['TL_CTE']['links'][\HeimrichHannot\ContaoPwaBundle\ContentElement\SubscribeButtonElement::TYPE] = \HeimrichHannot\ContaoPwaBundle\ContentElement\SubscribeButtonElement::class;