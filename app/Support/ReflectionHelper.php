<?php
/**
 * Better Reflection implementation for type-hint
 */
namespace App\Support;
use Closure;
use ArrayAccess;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;
use InvalidArgumentException;
class ReflectionHelper{
	private static $instance;
	/**
	 * Wrap the given closure such that its dependencies will be injected when executed.
	 *
	 * @param  \Closure  $callback
	 * @param  array  $parameters
	 * @return \Closure
	 */
	public function wrap(Closure $callback, array $parameters = [])
	{
		return function () use ($callback, $parameters) {
			return $this->call($callback, $parameters);
		};
	}
	/**
	 * Call the given Closure / class@method and inject its dependencies.
	 *
	 * @param  callable|string  $callback
	 * @param  array  $parameters
	 * @param  bool  $nullForMissing fill missing parameters with null value
	 * @return mixed
	 */
	public function call($callback, array $parameters = [], $nullForMissing = true)
	{
		if ($this->isCallableWithAtSign($callback)) {
			return $this->callClass($callback, $parameters, $nullForMissing);
		}

		$dependencies = $this->getMethodDependencies($callback, $parameters, $nullForMissing);

		return call_user_func_array($callback, $dependencies);
	}

	/**
	 * Determine if the given string is in Class@method syntax.
	 *
	 * @param  mixed  $callback
	 * @return bool
	 */
	protected function isCallableWithAtSign($callback)
	{
		return is_string($callback) && strpos($callback, '@') !== false;
	}

	/**
	 * Get all dependencies for a given method.
	 *
	 * @param  callable|string  $callback
	 * @param  array  $parameters
	 * @param bool $nullForMissing
	 * @return array
	 */
	protected function getMethodDependencies($callback, array $parameters = [], $nullForMissing=false)
	{
		$dependencies = [];
		$missing_dependencies=[];
		foreach ($this->getCallReflector($callback)->getParameters() as $parameter) {
			$this->addDependencyForCallParameter($parameter, $parameters, $dependencies, $missing_dependencies);
		}
		foreach ($missing_dependencies as $name){
			if(count($parameters)){
				$dependencies[$name]=array_shift($parameters);
			}elseif(!$nullForMissing){
				unset($dependencies[$name]);
			}
		}

		return $dependencies;
	}

	/**
	 * Get the proper reflection instance for the given callback.
	 *
	 * @param  callable|string  $callback
	 * @return \ReflectionFunctionAbstract
	 */
	protected function getCallReflector($callback)
	{
		if (is_string($callback) && strpos($callback, '::') !== false) {
			$callback = explode('::', $callback);
		}

		if (is_array($callback)) {
			return new ReflectionMethod($callback[0], $callback[1]);
		}

		return new ReflectionFunction($callback);
	}

	/**
	 * Get the dependency for the given call parameter.
	 *
	 * @param  \ReflectionParameter  $parameter
	 * @param  array  $parameters
	 * @param  array  $dependencies
	 * @param  array  $missing
	 * @return mixed
	 */
	protected function addDependencyForCallParameter(ReflectionParameter $parameter, array &$parameters, &$dependencies,&$missing)
	{
		if (array_key_exists($parameter->name, $parameters)) {
			$dependencies[$parameter->name] = $parameters[$parameter->name];

			unset($parameters[$parameter->name]);
		} elseif ($parameter->getClass()) {
			$dependencies[$parameter->name] = $this->make($parameter->getClass()->name);
		} elseif ($parameter->isDefaultValueAvailable()) {
			$dependencies[$parameter->name] = $parameter->getDefaultValue();
			$missing[]=$parameter->name;
		}else{
			$dependencies[$parameter->name]=null;
			$missing[]=$parameter->name;
		}
	}

	/**
	 * Call a string reference to a class using Class@method syntax.
	 *
	 * @param  string  $target
	 * @param  array  $parameters
	 * @param  string|null  $defaultMethod
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function callClass($target, array $parameters = [], $defaultMethod = null)
	{
		$segments = explode('@', $target);

		// If the listener has an @ sign, we will assume it is being used to delimit
		// the class name from the handle method name. This allows for handlers
		// to run multiple handler methods in a single class for convenience.
		$method = count($segments) == 2 ? $segments[1] : $defaultMethod;

		if (is_null($method)) {
			throw new InvalidArgumentException('Method not provided.');
		}

		return $this->call([$this->make($segments[0]), $method], $parameters);
	}
	public function make($abstract, array $parameters = []){
		return app()->make($abstract,$parameters);
	}

	/**
	 * @return ReflectionHelper
	 */
	static function instance(){
		if(!self::$instance){
			self::$instance=new self();
		}
		return self::$instance;
	}
}