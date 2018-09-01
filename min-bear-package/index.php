<?php
namespace MyVendor\MinApp\Module {

    use BEAR\Package\{AbstractAppModule,PackageModule};
    use BEAR\Sunday\Extension\Application\AbstractApp;

    require __DIR__ . '/vendor/autoload.php';

    class App extends AbstractApp
    {
    }

    class AppModule extends AbstractAppModule
    {
        protected function configure()
        {
            $this->install(new PackageModule);
        }
    }
}

namespace MyVendor\MinApp\Resource\Page {

    use BEAR\Resource\{RenderInterface, ResourceObject, TransferInterface};

    class Index extends ResourceObject
    {
        public $code = 200;

        public $headers = [
            'access-control-allow-origin' => '*'
        ];

        public $body = [];

        /**
         * @var RenderInterface
         */
        protected $renderer;

        public function __construct(RenderInterface $render)
        {
            $this->renderer = $render;
        }

        public function onGet(string $name) : ResourceObject
        {
            $this->body = [
                'greeting' => 'Hello ' . $name
            ];

            return $this;
        }

        public function __toString()
        {
            return $this->renderer->render($this);
        }

        public function transfer(TransferInterface $responder, array $server)
        {
            $responder($this, $server);
        }
    }
}

namespace MyVendor\MinApp\Bootstrap {

    use BEAR\Package\{AppInjector, Provide\Transfer\CliResponder};
    use BEAR\Resource\ResourceInterface;

    $resource = (new AppInjector('MyVendor\MinApp', 'app'))->getInstance(ResourceInterface::class);
    /** @var ResourceInterface $resource */
    $ro = $resource->get('page://self/index', ['name' => 'BEAR']);
    $ro->transfer(new CliResponder, $_SERVER);
}