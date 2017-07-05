<?php
namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use UserBundle\Form\CredentialsType;
use UserBundle\Entity\AuthToken;
use UserBundle\Entity\Credentials;
use UserBundle\Repository\AuthTokenRepository;

use Nelmio\ApiDocBundle\Annotation as Doc;

class AuthTokenController extends Controller
{
    /**
     * @Rest\Get("/auth-token")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="AuthTokens",
     *     resource=true,
     *     description="Get one token info",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getAuthTokenAction(Request $request)
    {
        if($request->headers->get("x-auth-token")) {
            $token = $this->getDoctrine()->getManager()->getRepository('UserBundle:AuthToken')->findOneBy(array("value" => $request->headers->get("x-auth-token")));
            /* @var $token AuthToken */

            return $token->getUser();
        } else {
            return $this->invalidCredentials();
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/auth-tokens")
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);
        $form->submit($request->request->all());

        if (!$form->isValid()) {return $form;}

        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('UserBundle:User')->findOneByEmail($credentials->getLogin());
        if (!$user) { return $this->invalidCredentials(); }

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());
        if (!$isPasswordValid) { return $this->invalidCredentials();}

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return $authToken;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/auth-tokens/{id}")
     */
    public function removeAuthTokenAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $authToken = $em->getRepository('UserBundle:AuthToken')->find($request->get('id'));
        /* @var $authToken AuthToken */

        $connectedUser = $this->get('security.token_storage')->getToken()->getUser();

        if ($authToken && $authToken->getUser()->getId() === $connectedUser->getId()) {
            $em->remove($authToken);
            $em->flush();
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException();
        }
    }

    private function invalidCredentials()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
    }
}