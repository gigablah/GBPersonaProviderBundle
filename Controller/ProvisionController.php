<?php

namespace Gigablah\PersonaProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Renders the provisioning page.
 *
 * @author Chris Heng <hengkuanyen@gmail.com>
 */
class ProvisionController extends Controller
{
    public function provisionAction()
    {
        return $this->render('GBPersonaProviderBundle:Provision:provision.html.twig');
    }
}
