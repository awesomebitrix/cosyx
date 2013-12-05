<?php
class CSX_Action
{
    public static function handle($action, $fn, $params = array())
    {
        if (CSX_Server::getRequest()->get('action') == $action) {
            array_unshift($params, $action);
            call_user_func_array($fn, $params);
        }
    }
}