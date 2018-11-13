<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Controller;


use Contao\Model\Collection;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushSubscriberModel;
use HeimrichHannot\ContaoPwaBundle\Model\PwaConfigurationsModel;
use HeimrichHannot\ContaoPwaBundle\Notification\DefaultNotification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package HeimrichHannot\ContaoPwaBundle\Controller
 *
 * @Route("/api/notifications")
 */
class NotificationController extends Controller
{
	/**
	 * @Route("/subscribe/{config}", name="push_notification_subscription", methods={"POST"})
	 *
	 * @param Request $request
	 * @param int $config
	 * @return Response
	 */
	public function subscribeAction(Request $request, int $config)
	{
		$this->container->get('contao.framework')->initialize();

		/** @var PwaConfigurationsModel $pwaConfig */
		$pwaConfig = PwaConfigurationsModel::findByPk($config);
		if (!$pwaConfig)
		{
			return new Response("No valid subscription id!", 400);
		}


		$data = json_decode($request->getContent(), true);
		if (!isset($data['subscription']) || !isset($data['subscription']['endpoint']))
		{
			return new Response("Missing endpoint key.", 404);
		}
		$endpoint = $data['subscription']['endpoint'];

		if (!$user = PwaPushSubscriberModel::findByEndpoint($endpoint))
		{
			$user = new PwaPushSubscriberModel();
			$user->dateAdded = $user->tstamp = time();
			$user->endpoint = $data['subscription']['endpoint'];
			$user->publicKey = $data['subscription']['keys']['p256dh'];
			$user->authToken = $data['subscription']['keys']['auth'];
			$user->pid = $pwaConfig->id;
			$user->save();
			return new Response("Subscription successful!", 200);
		}
		return new Response("You already subscribed!", 200);
	}

	/**
	 * @Route("/unsubscribe/{config}", name="push_notification_unsubscription", methods={"POST"})
	 *
	 * @param Request $request
	 * @param int $config
	 * @return Response
	 */
	public function unsubscribeAction(Request $request, int $config)
	{
		$this->container->get('contao.framework')->initialize();

		/** @var PwaConfigurationsModel $pwaConfig */
		$pwaConfig = PwaConfigurationsModel::findByPk($config);
		if (!$pwaConfig)
		{
			return new Response("No valid subscription id!", 400);
		}

		$data = json_decode($request->getContent(), true);
		if (!isset($data['subscription']) || !isset($data['subscription']['endpoint']))
		{
			return new Response("Missing endpoint key.", 404);
		}
		$endpoint = $data['subscription']['endpoint'];

		/** @var PwaPushSubscriberModel|Collection|null $user */
		if ($user = PwaPushSubscriberModel::findBy(['endpoint=?','pid=?'],[$endpoint, $pwaConfig->id]))
		{
			if ($user instanceof Collection)
			{
				foreach ($user as $entry)
				{
					$entry->delete();
				}
			}
			else {
				$user->delete();
			}
			return new Response("User successful unsubscribed!", 200);
		}
		return new Response("User not found!", 404);
	}

	/**
	 * @Route("/send/{config}/{payload}", name="send_notification")
	 *
	 * @param Request $request
	 * @param string $payload
	 * @return Response
	 * @throws \ErrorException
	 */
	public function sendAction(Request $request, int $config, string $payload)
	{
		$this->get('contao.framework')->initialize();

		if (!$pwaConfig = PwaConfigurationsModel::findByPk($config))
		{
			return new Response("No configuration found. Could not send payload.", 404);
		}

		$notification = new DefaultNotification();
		$notification->setTitle('HuH Pwa Bundle');
		$notification->setBody($payload);
		$notification->setIcon('images/icons/icon-128x128.png');
		$result = $this->get('huh.pwa.sender.pushnotification')->send($notification, $pwaConfig);
		dump($result);
		die();
	}

	/**
	 * @Route("/publickey", name="huh.pwa.notification.publickey", methods={"GET"})
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function returnPublicKeyAction(Request $request)
	{
		if ($key = $this->getPublicKey())
		{
			return new Response($key);
		}
		return new Response("No public key available.", 400);
	}

	protected function getPublicKey()
	{
		$config = $this->getParameter("huh.pwa");
		if (!isset($config['vapid']) || !isset($config['vapid']['publicKey']))
		{
			return false;
		}
		return $config['vapid']['publicKey'];
	}
}