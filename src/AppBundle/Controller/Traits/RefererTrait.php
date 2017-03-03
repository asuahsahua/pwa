<?php

namespace AppBundle\Controller\Traits;

trait RefererTrait
{
    protected function redirectReferrer($request)
    {
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    abstract function redirect($url, $status=302);
}

