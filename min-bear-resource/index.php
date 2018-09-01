<?php

namespace MyVendor\MinApp\Resource\Page {

    require __DIR__ . '/vendor/autoload.php';

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

    use BEAR\Resource\Module\ResourceModule;
    use BEAR\Resource\{ResourceInterface, ResourceObject, TransferInterface};
    use Ray\Di\Injector;

    $resource = (new Injector(new ResourceModule('MyVendor\MinApp')))->getInstance(ResourceInterface::class);
    /** @var ResourceInterface $resource */
    $ro = $resource->get('page://self/index', ['name' => 'BEAR']);
    $ro->transfer(new class implements TransferInterface {
        public function __invoke(ResourceObject $ro, array $server)
        {
            echo $ro->code . PHP_EOL;
            foreach ($ro->headers as $key => $header) {
                echo "{$key}: {$header}" . PHP_EOL;
            }
            echo (string) $ro;
        }

    }, $_SERVER);
}