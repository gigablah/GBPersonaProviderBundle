<?php

namespace Gigablah\PersonaProviderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Generates a public and private keypair.
 *
 * @author Chris Heng <hengkuanyen@gmail.com>
 */
class PersonaKeypairCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('gb:persona:keypair')
            ->setDescription('Generates a public and private keypair')
            ->setHelp(<<<EOF
The <info>gb:persona:keypair</info> command generates a RSA256 keypair.</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rsa = new \Crypt_RSA();
        $rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS1);
        $keys = $rsa->createKey(2048);

        $targetDir = $this->getContainer()->getParameter('gb_persona_provider.key_path');
        $filesystem = $this->getContainer()->get('filesystem');
        $filesystem->mkdir($targetDir, 0777);

        if (false === @file_put_contents($targetDir.'/private.pem', $keys['privatekey'])) {
            throw new \RuntimeException('Unable to write private key.');
        }

        if (false === @file_put_contents($targetDir.'/public.pem', $keys['publickey'])) {
            throw new \RuntimeException('Unable to write public key.');
        }

        $output->writeln('Keypair generated.');
        $output->writeln(sprintf(
            "\n%s\n%s\n\n%s\n%s",
            $targetDir.'/private.pem',
            $keys['privatekey'],
            $targetDir.'/public.pem',
            $keys['publickey']
        ), OutputInterface::VERBOSITY_VERBOSE);
    }
}
