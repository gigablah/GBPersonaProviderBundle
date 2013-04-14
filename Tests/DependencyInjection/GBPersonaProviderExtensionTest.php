<?php

namespace Gigablah\PersonaProviderBundle\Tests\DependencyInjection;

use Gigablah\PersonaProviderBundle\DependencyInjection\GBPersonaProviderExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Scope;

class GBPersonaProviderExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $kernel;
    private $container;

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

        $this->container->set('kernel', $this->kernel);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->container = null;
        $this->kernel = null;
    }

    public function testDefaultConfig()
    {
        $extension = new GBPersonaProviderExtension();
        $extension->load(array(array()), $this->container);

        $this->assertNull($this->container->getParameter('gb_persona_provider.host'));
        $this->assertSame($this->container->getParameter('gb_persona_provider.key_path'), '%kernel.root_dir%/Resources/data');
    }

    public function testCustomConfig()
    {
        $extension = new GBPersonaProviderExtension();
        $extension->load(array(array('host' => 'example.org', 'key_path' => '/tmp/keys')), $this->container);

        $this->assertSame($this->container->getParameter('gb_persona_provider.host'), 'example.org');
        $this->assertSame($this->container->getParameter('gb_persona_provider.key_path'), '/tmp/keys');
    }
}
