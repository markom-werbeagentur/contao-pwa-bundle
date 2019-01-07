require ('@babel/polyfill');
import PushSubscriptionButtons from './PushSubscriptionButtons';
import PushNotificationSubscription from './PushNotificationSubscription';

let PwaButtons = new PushSubscriptionButtons();
let PushSubscription = new PushNotificationSubscription();
let debug = false;

if (!('serviceWorker' in navigator)) {
    if (debug) console.log('[SW Registration] Service Worker not supported');
    document.dispatchEvent(new Event('huh_pwa_sw_not_supported'));
}
else {
    if (debug) console.log("[SW Registration] Register service worker");
    navigator.serviceWorker.register(HuhContaoPwaBundle.serviceWorker.path, {
        scope: '/'
    });
    if (HuhContaoPwaBundle.pushNotifications.support)
    {
        if (('PushManager' in window)) {
            if (debug) console.log("[SW Registration] Push supported");
            navigator.serviceWorker.ready
            .then(function(registration) {
                if (debug)console.log("[SW Registration] Service worker registered");
                return registration.pushManager.getSubscription();
            }).then(function(subscription) {
                if (subscription)
                {
                    if (debug)console.log('[SW Registration] Already subscribed');
                    PushSubscription.setIsSubscribed();
                }
                else
                {
                    if (debug)console.log('[SW Registration] Not subscribed');
                    PushSubscription.setIsUnsubscribed();
                }
            });
        }
        else {
            document.dispatchEvent(new Event('huh_pwa_push_not_supported'));
            if (debug)console.log('[SW Registration] Browser don\'t support push. Hide subscription button.');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    debug = HuhContaoPwaBundle.debug;
    PushSubscription.onLoaded();
    PushSubscription.subscribePath = HuhContaoPwaBundle.pushNotifications.subscribePath;
    PushSubscription.unsubscribePath = HuhContaoPwaBundle.pushNotifications.unsubscribePath;
    PwaButtons.onReady();
});