<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class TwigListener
{
    protected $twig;

    function __construct(\Twig_Environment $twig, TokenStorage $security)
    {
        $this->twig = $twig;
        $this->security = $security;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $user = $this->getUser();
        if ($user) {
            /** @var \Twig_Extension_Core $core */
            $core = $this->twig->getExtension(\Twig_Extension_Core::class);
            $core->setTimezone($user->getTimezone());
        }
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        $token = $this->security->getToken();
        if (!$token) {
            return null;
        }

        $user = $token->getUser();
        if ($user instanceof User) {
            return $user;
        } else {
            return null;
        }
    }

}