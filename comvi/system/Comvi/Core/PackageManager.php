<?php
namespace Comvi\Core;

/**
 * The PackageManager class.
 *
 * @package		Comvi.Core
 */
class PackageManager implements ServiceManagerAwareInterface
{
	use ServiceManagerAwareTrait;


	public $packages = array();


	public function load($pkg)
	{
		return is_array($pkg) ? $this->loadPackages($pkg) : $this->loadPackage($pkg);
	}

	public function loadPackage($package)
	{
		if (!in_array($package, $this->packages)) {
			$class = str_replace('/', '\\', $package).'\\Index';

			try {
				$instance = new $class;
				$instance->setServiceManager($this->service_manager);
				$instance->index();
			}
			catch (Exception $e) {
				echo $e->getMessage();
				throw new Exception("LIB_PACKAGEMANAGER_ERROR_LOAD_PACKAGE_PACKAGE_NOT_FOUND|$package", 404);
			}

			$this->packages[] = $package;
		}

		return $this;
	}

	public function loadPackages($packages)
	{
		foreach ($packages as $package) {
			$this->loadPackage($package);
		}

		return $this;
	}
}
?>