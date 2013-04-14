<?php

namespace Gigablah\PersonaProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Checks if the user has an active session.
 *
 * @author Chris Heng <hengkuanyen@gmail.com>
 */
class IdentifyController extends Controller
{
    public function identifyAction(Request $request)
    {
        $email = $request->request->get('email');

        try {
            $success = $this->isSessionActive($email);
        } catch (\Exception $e) {
            return new JsonResponse(array('success' => false));
        }

        return new JsonResponse(array('success' => $success));
    }

    protected function isSessionActive($email)
    {
        if (!strlen($email)) {
            throw new \InvalidArgumentException('email argument is required and must be a string');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return false;
        }

        $host = $this->container->getParameter('gb_persona_provider.host') ?: $request->getHost();
        if ($email != $user->getUsername().'@'.$host) {
            return false;
        }

        return true;
    }
}
