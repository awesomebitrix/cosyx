<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package mvc
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package mvc
 */
class CSX_Mvc_Controller extends CSX_Controller {
    public function run($params) {
        array_shift($params['matches']);

        $controller = array_shift($params['matches']);
        $action = array_shift($params['matches']);
        
        $parameters = $params['matches'];
        
        $className = 'Controller_' . CSX_String::ucWords($controller);

        if (class_exists($className)) {
            CHTTP::SetStatus("200 OK");
            @define("ERROR_404","N");

            $cls = new ReflectionClass($className);
            $methodName = CSX_Compat::resolveMethodName($action);

            if (!$cls->hasMethod($methodName)) $methodName = 'index';
            
            if($cls->hasMethod($methodName)) {
                CSX_Server::getSession()->start();

                $obj = $cls->newInstance();
    
                $result = null;
                if ($cls->hasMethod('beforeaction')) {
                    $result = call_user_func_array(
                        array($obj, 'beforeaction'), array( $action )
                    );
                }

                if (!$result) {
                    $result = call_user_func_array(
                        array($obj, $methodName), $parameters
                    );
                }

                if ($result instanceOf CSX_Mvc_ActionResult) {
                    CSX_Server::getResponse()->append($result->getResult());
                }
                else {
                    throw new CSX_Exception("Invalid result value from controller: {$className}, action: {$action}");
                }
            }
            else {
                throw new CSX_Server_HttpNotFoundException();
            }

            return true;
        }
    }
}