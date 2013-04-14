<?php

namespace Gigablah\PersonaProviderBundle\Tests\Controller;

use Gigablah\PersonaProviderBundle\Controller\CertifyController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

class CertifyControllerTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;
    private $container;
    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->kernel = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');

        $this->container = new ContainerBuilder();
        $this->container->addScope(new Scope('request'));
        $this->container->register('request', 'Symfony\\Component\\HttpFoundation\\Request')->setScope('request');
        $this->container->setParameter('kernel.bundles', array());
        $this->container->setParameter('kernel.cache_dir', __DIR__);
        $this->container->setParameter('kernel.debug', false);
        $this->container->setParameter('kernel.root_dir', __DIR__);
        $this->container->setParameter('gb_persona_provider.key_path', __DIR__.'/Fixtures');
        $this->container->set('kernel', $this->kernel);

        $this->controller = new CertifyController();
        $this->controller->setContainer($this->container);

    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->controller = null;
        $this->container = null;
        $this->kernel = null;
    }

    public function testCertifyAction()
    {
        $this->container->setParameter('gb_persona_provider.host', 'mockmyid.com');
        $request = new Request(array(), array(
            'email' => 'warner@mockmyid.com',
            'pubkey' => '{"algorithm":"DS","g":"c52a4a0ff3b7e61fdf1867ce84138369a6154f4afa92966e3c827e25cfa6cf508b90e5de419e1337e07a2e9e2a3cd5dea704d175f8ebf6af397d69e110b96afb17c7a03259329e4829b0d03bbc7896b15b4ade53e130858cc34d96269aa89041f409136c7242a38895c9d5bccad4f389af1d7a4bd1398bd072dffa896233397a","p":"ff600483db6abfc5b45eab78594b3533d550d9f1bf2a992a7a8daa6dc34f8045ad4e6e0c429d334eeeaaefd7e23d4810be00e4cc1492cba325ba81ff2d5a5b305a8d17eb3bf4a06a349d392e00d329744a5179380344e82a18c47933438f891e22aeef812d69c8f75e326cb70ea000c3f776dfdbd604638c2ef717fc26d02e17","q":"e21e04f911d1ed7991008ecaab3bf775984309c3","y":"2b0b6f44f58ec4fd5043be6c68433bc839bb867276f90a9c7a68071097167d2cab2df53aa5ae928843d15a42412123ee24c4067d7b8587850d1f09fa39cc5bb52f8b8844c3132440f2e455aea8235535b28a8f01588209f145ee1f265257fe9999bc90547ba985052ad4fb320fb9153878164bf3572dc5c4fe493e66506f2b04"}',
            'duration' => 86400
        ));
        $response = $this->controller->certifyAction($request);

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\JsonResponse', $response);
        $this->assertSame(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('success', $content);
        $this->assertArrayHasKey('certificate', $content);
    }

    /**
     * @dataProvider getParameters
     */
    public function testCertifyActionParameters($host, $parameters, $message)
    {
        $this->container->setParameter('gb_persona_provider.host', $host);
        $request = new Request(array(), $parameters);
        $response = $this->controller->certifyAction($request);

        $this->assertSame(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($message, $content['reason']);
    }

    public function getParameters()
    {
        return array(
            array(null, array(), 'host argument is required and must be a string'),
            array('mockmyid.com', array(), 'email argument is required and must be a string'),
            array('mockmyid.com', array('email' => 'warner@mockmyid.com'), 'pubkey argument is required and must be a string'),
            array('mockmyid.com', array('email' => 'warner@mockmyid.com', 'pubkey' => '{}'), 'pubkey is not well-formed'),
            array('mockmyid.com', array('email' => 'warner@mockmyid.com', 'pubkey' => '{"algorithm":"DS"}', 'duration' => 'hello'), 'duration argument must be a number when present'),
            array('mockmyid.com', array('email' => 'warner@mockmyid.com', 'pubkey' => '{"algorithm":"DS"}', 'duration' => 0), 'duration must be a positive integer'),
            array('mockmyid.com', array('email' => 'warner@mockmyid.com', 'pubkey' => '{"algorithm":"DS"}', 'duration' => 86401), 'duration cannot be more than 86400 seconds')
        );
    }
}
