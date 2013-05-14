<?php
	return array(
		'allow_override' => true,
        'abstract_factories' => array(
            // Valid values include names of classes implementing
            // AbstractFactoryInterface, instances of classes implementing
            // AbstractFactoryInterface, or any PHP callbacks
            //'SomeModule\Service\FallbackFactory',
        ),
        'aliases' => array(
            // Aliasing a FQCN to a service name
            //'SomeModule\Model\User' => 'User',
            // Aliasing a name to a known service name
            //'AdminUser' => 'User',
            // Aliasing to an alias
            //'SuperUser' => 'AdminUser',
			//'package_manager' => 'PackageManager'
        ),
        'factories' => array(
            // Keys are the service names.
            // Valid values include names of classes implementing
            // FactoryInterface, instances of classes implementing
            // FactoryInterface, or any PHP callbacks
			'EventManager'		=> 'Comvi\\Core\\EventManagerFactory',
			'SessionManager'	=> 'Comvi\\Core\\SessionManagerFactory',
			'CurrentURL'		=> 'Comvi\\Core\\CurrentURLFactory',
			'Router'			=> 'Comvi\\Core\\RouterFactory',
			'Request'			=> 'Comvi\\Core\\RequestFactory',
            'Document'			=> 'Comvi\\Core\\DocumentFactory',
			'Translator'		=> 'Comvi\\Core\\TranslatorFactory',
			'PackageManager'	=> 'Comvi\\Core\\PackageManagerFactory',
			'Profiler'			=> 'Comvi\\Core\\ProfilerFactory'
            /*'UserForm' => function ($serviceManager) {
                $form = new SomeModule\Form\User();

                // Retrieve a dependency from the service manager and inject it!
                $form->setInputFilter($serviceManager->get('UserInputFilter'));
                return $form;
            },*/
        ),
        'invokables' => array(
            // Keys are the service names
            // Values are valid class names to instantiate.
            //'UserInputFiler' => 'SomeModule\InputFilter\User',
        ),
        'services' => array(
            // Keys are the service names
            // Values are objects
			//'Auth' => new SomeModule\Authentication\AuthenticationService(),
        ),
        'shared' => array(
            // Usually, you'll only indicate services that should **NOT** be
            // shared -- i.e., ones where you want a different instance
            // every time.
            //'UserForm' => false,
        ),
	);
?>