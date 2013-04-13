<?php

namespace Gigablah\PersonaProviderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Creates the browserid support document.
 *
 * @author Chris Heng <hengkuanyen@gmail.com>
 */
class PersonaBrowserIdCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('gb:persona:browserid')
            ->setDescription('Creates the browserid support document')
            ->setDefinition(array(
                new InputArgument('dest', InputArgument::OPTIONAL, 'Output document', 'web/.well-known/browserid')
            ))
            ->setHelp(<<<EOF
The <info>gb:persona:browserid</info> command creates the browserid JSON support document.</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $src = $this->getContainer()->getParameter('gb_persona_provider.key_path').'/public.pem';
        if (!file_exists($src)) {
            throw new \RuntimeException(sprintf('Public key file %s not found. Please run the gb:persona:keypair command to generate a keypair.', $src));
        }

        $rsa = new \Crypt_RSA();
        $rsa->setPublicKey(file_get_contents($src), CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
        $key = $rsa->getPublicKey(CRYPT_RSA_PUBLIC_FORMAT_RAW);

        $router = $this->getContainer()->get('router');
        $document = array(
            'public-key' => array(
                'algorithm' => 'RS',
                'n' => $key['n']->toString(),
                'e' => $key['e']->toString()
            ),
            'authentication' => $router->generate('gb_persona_provider_authenticate'),
            'provisioning' => $router->generate('gb_persona_provider_provision')
        );

        $dest = dirname($this->getContainer()->get('kernel')->getRootDir()).'/'.$input->getArgument('dest');
        $filesystem = $this->getContainer()->get('filesystem');
        $filesystem->mkdir(dirname($dest), 0755);

        if (false === @file_put_contents($dest, json_encode($document))) {
            throw new \RuntimeException('Unable to write support document.');
        }

        $output->writeln(sprintf('Support document written to %s', $dest));
    }
}
