<?php
class CSX_SessionRedirect extends CSX_Singleton {
    protected $redirectParams = array();

    protected function __construct($args = array())
    {
        if (CSX_Server::getSession()->has('csx_redirect_params')) {
            $this->redirectParams = CSX_Server::getSession()->get('csx_redirect_params');
            CSX_Server::getSession()->remove('csx_redirect_params');
        }
    }

    /**
     * @return CSX_SessionRedirect
     */
    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }

    public function redirect($url, $params = array())
    {
        CSX_Server::getSession()->set('csx_redirect_params', $params);
        LocalRedirect($url);
    }

    public function get($key) {
        return isset($this->redirectParams[$key]) ? $this->redirectParams[$key] : null;
    }

    public function has($key) {
        return isset($this->redirectParams[$key]);
    }

    public function getParams() {
        return $this->redirectParams;
    }
}