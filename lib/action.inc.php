<?php
class CSX_Action
{
    public static function handle($action, $fn)
    {
        if (CSX_Server::getRequest()->get('action') == $action) {
            call_user_func_array($fn, array($action));
        }
    }
}