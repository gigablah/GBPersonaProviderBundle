<?php

namespace Gigablah\PersonaProviderBundle\Tests\DependencyInjection;

use Gigablah\PersonaProviderBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getOptions
     */
    public function testConfigTree($options, $results)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array($options));

        $this->assertEquals($results, $config);
    }

    public function testKeyPathCannotBeEmpty()
    {
        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array(array('key_path' => null)));
    }

    public function getOptions()
    {
        return array(
            array(array(), array('host' => null, 'key_path' => '%kernel.root_dir%/Resources/data')),
            array(array('host' => 'example.org', 'key_path' => '/tmp/keys'), array('host' => 'example.org', 'key_path' => '/tmp/keys'))
        );
    }
}
