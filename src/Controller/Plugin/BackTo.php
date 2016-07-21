<?php

namespace AtAdmin\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

class BackTo extends AbstractPlugin
{
    /**
     * @var string
     */
    protected $paramName = 'backto';

    /**
     * @var Container
     */
    protected $sessionContainer;

    /**
     * @param $name
     */
    public function setParamName($name)
    {
        $this->paramName = (string) $name;
    }

    /**
     * @param Container $container
     * @return $this
     */
    public function setSessionContainer(Container $container)
    {
        $this->sessionContainer = $container;
        return $this;
    }

    /**
     * @return Container
     */
    public function getSessionContainer()
    {
        if (!$this->sessionContainer) {
            $this->sessionContainer = new Container('at_base');
        }

        return $this->sessionContainer;
    }

    /**
     * @param null $url
     */
    public function setBackUrl($url = null)
    {
        if ($url) {
            $url = (string) $url;
        } else {
            $url = $this->getController()->getRequest()->getRequestUri();
        }

        $session = $this->getSessionContainer();
        $session->{$this->paramName} = $url;
    }

    /**
     * @param bool $flush
     * @return string
     */
    public function getBackUrl($flush = true)
    {
        $backUrl = '/';

        $session = $this->getSessionContainer();
        if (isset($session->{$this->paramName})) {
            $backUrl = $session->{$this->paramName};
        }

        if ($flush) {
            $this->flush();
        }

        return $backUrl;
    }

    /**
     * @return $this
     */
    public function flush()
    {
        $session = $this->getSessionContainer();
        unset($session->{$this->paramName});
        return $this;
    }

    /**
     * @param null $message
     */
    public function previous($message = null)
    {
        // Return if it is ajax request
        if ($this->getController()->getRequest()->isXmlHttpRequest()) {
            return;
        }

        if ($message) {
            $this->getController()->flashMessenger()->addMessage($message);
        }

        $this->getController()->redirect()->toUrl($this->getBackUrl());
    }
}