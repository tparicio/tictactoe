<?php
// src/EventListener/HomeController.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocalListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * LocalListener constructor
     *
     * @param  string $defaultLocale locale ISO code
     */
    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * onKernelRequest event
     *
     * @param  GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $request->setLocale('es');

        if (!$request->hasPreviousSession()) {
            return;
        }

        // check locale on route URI
        if ($locale = $request->attributes->get('_locale')) {

            $request->getSession()->set('_locale', $locale);
        } else {
            // if no locale then use defaultLocale as fallback
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    /**
     * get subscribed event into an array
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
