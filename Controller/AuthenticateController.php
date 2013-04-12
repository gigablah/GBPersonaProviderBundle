<?php

namespace Gigablah\PersonaProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Displays the login form if the user does not have an active session.
 *
 * @author Chris Heng <hengkuanyen@gmail.com>
 */
class AuthenticateController extends Controller
{
    public function authenticateAction()
    {
        $csrfToken = $this->container->has('form.csrf_provider')
            ? $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;

        return $this->render('GBPersonaProviderBundle:Authenticate:authenticate.html.twig', array(
            'csrf_token' => $csrfToken
        ));
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
}
