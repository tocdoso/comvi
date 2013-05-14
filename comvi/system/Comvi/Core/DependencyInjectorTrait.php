<?php
namespace Comvi\Core;
use ReflectionClass;
use ReflectionMethod;

/**
 * Declare Dependency Injector trait.
 *
 * @package		Comvi.Core
 */
trait DependencyInjectorTrait
{
	public function newInstance()
	{
		$args = func_get_args();
		$class = array_shift($args);

		// If the class doesn't exist then 404.
		if (!class_exists($class)) {
			throw new Exception("DEPENDENCY_INJECTOR_ERROR_NEW_OBJECT_CLASS_NOT_FOUND|$class", 404);
		}

		// Load the controller using reflection.
		$class = new ReflectionClass($class);

		// Create a new instance of the controller.
		$instance = $class->newInstanceArgs($args);

		// Create array of setter injection methods.
		$setters = array();
		foreach ($class->getInterfaces() as $interface) {
			foreach ($interface->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
				if (strpos($method->getName(), 'set') === 0) {
					$setters[] = $class->getMethod($method->getName());
				}
			}
		}

		// Do setter injections
		foreach ($setters as $setter) {
			$params = array();
			foreach ($setter->getParameters() as $param) {
				$params[] = $this->get($param->getName());
			}

			$setter->invokeArgs($instance, $params);
		}

		return $instance;
	}
}
