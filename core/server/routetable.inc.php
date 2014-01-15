<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package core
 */
class CSX_Server_RouteTable
{
	protected $routes = array();

	public function addRoute($name, $route)
	{
		$this->routes[$name] = $route;
	}

	public function addRouteFirst($name, $route)
	{
		$this->routes = array_merge(array($name => $route), $this->routes);
	}

	public function addRoutes($routes)
	{
		$this->routes = array_merge($this->routes, $routes);
	}

	function getRoute($key)
	{
		return isset($this->routes[$key]) ? $this->routes[$key] : false;
	}

	public function find($uri, $after = null)
	{
		$process = $after == null;

		$csx_root_url = str_replace('/', '\/', CSX_ROOT_URL);

		foreach ($this->routes as $name => $route) {
			$pattern = $route["pattern"];

			if ($pattern[0] == '^') {
				$pattern = '^' . $csx_root_url . CSX_String::substr($pattern, 1);
			}
			else {
				$pattern = $csx_root_url . $pattern;
			}

			if ($process) {
				$matches = array();
				$n = preg_match('/' . $pattern . '/i', $uri, $matches);

				if ($n > 0) {
					$route['matches'] = $matches;
					return array(CSX_Compat::resolveClassName($route['controller']), $route, $name);
				}
			}

			if ($after == null || ($after != null && $name == $after)) {
				$process = true;
			}
		}

		return false;
	}
}