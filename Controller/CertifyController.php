<?php

namespace Gigablah\PersonaProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Create a signed certificate which allows the user to log in using Persona for the requested duration.
 *
 * @author Chris Heng <hengkuanyen@gmail.com>
 */
class CertifyController extends Controller
{
    public function certifyAction()
    {
        $request = $this->getRequest();

        $email = $request->request->get('email');
        $pubkey = $request->request->get('pubkey');
        $duration = $request->request->get('duration');
        $host = $this->container->getParameter('gb_persona_provider.host') ?: $request->getHost();

        try {
            $privkey = file_get_contents($this->container->getParameter('gb_persona_provider.key_path').'/private.pem');
            $certificate = $this->certify($email, $pubkey, $privkey, $host, $duration);
        } catch (\Exception $e) {
            return new JsonResponse(array('success' => false, 'reason' => $e->getMessage()), 400);
        }

        return new JsonResponse(array('success' => true, 'certificate' => $certificate));
    }

    protected function certify($email, $pubkey, $privkey, $host, $duration = 86400)
    {
        if (!strlen($email)) {
            throw new \InvalidArgumentException('email argument is required and must be a string');
        }

        if (!strlen($pubkey)) {
            throw new \InvalidArgumentException('pubkey argument is required and must be a string');
        }

        if (!strlen($privkey)) {
            throw new \InvalidArgumentException('privkey argument is required and must be a string');
        }

        if (!strlen($host)) {
            throw new \InvalidArgumentException('host argument is required and must be a string');
        }

        if (!is_numeric($duration)) {
            throw new \InvalidArgumentException('duration argument must be a number when present');
        }

        if ($duration > 86400) {
            throw new \InvalidArgumentException('duration cannot be more than 86400 seconds');
        }

        $issuedAt = round(microtime(true) * 1000);

        $payload = array(
            'iss' => $host,
            'exp' => $issuedAt + $duration * 1000,
            'iat' => $issuedAt,
            'public-key' => $pubkey,
            'principal' => array(
                'email' => $email
            )
        );

        return \JWT::encode($payload, $privkey, 'RS256');
    }
}
