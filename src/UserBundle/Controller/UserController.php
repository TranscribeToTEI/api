<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Context\Context;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use UserBundle\Entity\Access;
use UserBundle\Entity\Preference;
use UserBundle\Entity\User;

use UserBundle\Form\UserType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/users")
     * @QueryParam(name="token", description="User's token")
     * @QueryParam(name="username", nullable=true, description="Username of an user")
     * @QueryParam(name="profile", requirements="short|full", nullable=true, description="Quantity of information returned about the user.")
     *
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Get the list of all users",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getUsersAction(Request $request, ParamFetcher $paramFetcher)
    {
        $token = $paramFetcher->get('token');
        $username = $paramFetcher->get('username');
        $profile = $paramFetcher->get('profile');

        $em = $this->getDoctrine()->getManager();

        if($token != "") {
            $users = $em->getRepository('UserBundle:AccessToken')->findOneByToken($token)->getUser();
            /* @var $users User */
        } elseif($username != "") {
            $users = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));
            /* @var $users User */
        } else {
            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $users = $em->getRepository('UserBundle:User')->findAll();
                /* @var $users User[] */
            } else {
                throw $this->createAccessDeniedException('Unable to access this page!');
            }
        }

        $groups = ['preferences', 'accesses', 'id', 'content'];
        if($profile == 'full') {
            $groups = ['full'];
        }

        $view = $this->view($users, 200);
        $context = new Context();
        $context->setGroups($groups);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/users/{id}")
     * @QueryParam(name="profile", requirements="short|full", nullable=true, description="Quantity of information returned about the user.")
     *
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Return one user",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The user unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getUserAction(Request $request, ParamFetcher $paramFetcher)
    {
        $profile = $paramFetcher->get('profile');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */

        if(empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $groups = ['preferences', 'id', 'accesses', 'content'];
        if($profile == 'full') {
            $groups = ['full'];
        }

        $view = $this->view($user, 200);
        $context = new Context();
        $context->setGroups($groups);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/users")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Create a new user",
     *     requirements={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Name of the user."
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="email",
     *             "requirement"="",
     *             "description"="The email of the user."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postUsersAction(Request $request)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new \FOS\UserBundle\Event\GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new \FOS\UserBundle\Event\FormEvent($form, $request);
            $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new \Symfony\Component\HttpFoundation\RedirectResponse($url);
            }

            $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_COMPLETED, new \FOS\UserBundle\Event\FilterUserResponseEvent($user, $request, $response));

            $view = $this->view($user, Response::HTTP_CREATED);

            return $this->handleView($view);
        }

        $view = $this->view($form, Response::HTTP_BAD_REQUEST);
        return $this->handleView($view);
    }

    /**
     * @Rest\View()
     * @Rest\Put("/users/{id}")
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Update an existing user",
     *     requirements={
     *         {
     *             "name"="Name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Name of the user."
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="email",
     *             "requirement"="",
     *             "description"="The email of the user."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function updateUserAction(Request $request)
    {
        return $this->updateUser($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/users/{id}")
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Update an existing user",
     *     requirements={
     *         {
     *             "name"="Name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Name of the user."
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="email",
     *             "requirement"="",
     *             "description"="The email of the user."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function patchUserAction(Request $request)
    {
        return $this->updateUser($request, false);
    }

    private function updateUser(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */
        if (empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        if($this->get('security.token_storage')->getToken()->getUser() == $user OR $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if ($clearMissing) {
                $options = ['validation_groups'=>['Default', 'FullUpdate']];
            } else {
                $options = [];
            }

            $form = $this->createForm(UserType::class, $user);
            $form->submit($request->request->all(), $clearMissing);
            if ($form->isValid()) {
                if (!empty($user->getPlainPassword())) {
                    $encoder = $this->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                    $user->setPassword($encoded);
                }

                $em->merge($user);
                $em->flush();
                return $user;
            } else {
                return $form;
            }
        } else {
            // User need to be the user requested or an admin
            throw $this->createAccessDeniedException('Unable to access this page!');
        }
    }

    /**
     * @Rest\Delete("/users/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Remove a user",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The user unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("has_role('IS_AUTHENTICATED_FULLY')")
     */
    public function removeUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */

        if($user AND ($this->get('security.token_storage')->getToken()->getUser() == $user OR $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))) {
            $em->remove($user);
            $em->flush();
        } else {
            // User need to be the user requested or an admin
            throw $this->createAccessDeniedException('Unable to access this page!');
        }
    }

    /**
     * @Rest\Get("users/confirmation/{token}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Confirm user",
     *     requirements={
     *         {
     *             "name"="token",
     *             "description"="The token of the confirmation.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            //$url = $this->generateUrl('fos_user_registration_confirmed');
            //$response = new RedirectResponse($url);
            $response = new JsonResponse(true);
        }

        //$dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * @Rest\Get("users/resetting/send/{username}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Resetting password",
     *     requirements={
     *         {
     *             "name"="email",
     *             "description"="The email of the user.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function sendEmailAction(Request $request, $username)
    {
        #$username = $request->request->get('username');

        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        /* Dispatch init event */
        $event = new GetResponseNullableUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $ttl = $this->container->getParameter('fos_user.resetting.retry_ttl');

        if (null !== $user && !$user->isPasswordRequestNonExpired($ttl)) {
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator TokenGeneratorInterface */
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            /* Dispatch confirm event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('fos_user.user_manager')->updateUser($user);

            /* Dispatch completed event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }

        return new JsonResponse(true);
    }

    /**
     * @Rest\Post("users/resetting/reset/{token}")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Resetting password",
     *     requirements={
     *         {
     *             "name"="email",
     *             "description"="The email of the user.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                /*$url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);*/
                $response = new JsonResponse(true);
            }

            $dispatcher->dispatch(
                FOSUserEvents::RESETTING_RESET_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $response;
        } else {
            return new JsonResponse(false);
        }

        /*return $this->render('@FOSUser/Resetting/reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
        ));*/
    }

    /**
     * @Rest\Post("users/password/change")
     * @Rest\View()
     * @Doc\ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Change password",
     *     requirements={
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                /*$url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);*/
                $response = new JsonResponse(true);
            }

            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        } else {
            return new JsonResponse(false);
        }

        /*return $this->render('@FOSUser/ChangePassword/change_password.html.twig', array(
            'form' => $form->createView(),
        ));*/
    }

    /**
     * @Rest\Post("/users/{id}/roles")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Add a role to user",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The user unique identifier.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function setRoleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */

        if($user == null) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $allowedRoles = ['ROLE_MODO', 'ROLE_TAXONOMY_EDIT'];
        if(in_array($request->get('role'), $allowedRoles)) {
            $user->addRole($request->get('role'));

            if($request->get('role') == 'ROLE_TAXONOMY_EDIT') {
                /** @var $access Access */
                $access = $em->getRepository('UserBundle:Access')->findOneBy(array('user' => $user));
                $access->setTaxonomyRequest(null);
                $access->setIsTaxonomyAccess(true);
            }
            $em->flush();

            // Email notification :
            $message = \Swift_Message::newInstance()
                ->setSubject('Votre compte dispose d\'un nouveau role - Testaments de Poilus')
                ->setFrom('testaments-de-poilus@huma-num.fr')
                ->setTo($user->getEmail())
                ->setBody($this->renderView(
                    'UserBundle:SetRole:emailPromote.html.twig',
                    array('user'=> $user, 'role' => $request->get('role'))))
            ;
            $this->get('mailer')->send($message);
        }

        return $user;
    }

    /**
     * @Rest\Post("/users-avatar")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @QueryParam(name="id", nullable=false, description="Identifier of the user")
     *
     * @Doc\ApiDoc(
     *     section="Users",
     *     resource=true,
     *     description="Upload an avatar for a user",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function postAvatarsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $id = $paramFetcher->get('id');

        /** @var $user User */
        $user = $em->getRepository('UserBundle:User')->findOneById($id);
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /* Upload logic */
        $uploadedFile = $request->files->get('picture');
        $directory = __DIR__.'/../../../web/uploads/';
        $uploadedFile->move($directory, $uploadedFile->getClientOriginalName());

        $file = fopen($directory.$uploadedFile->getClientOriginalName(), 'r');
        $fileName = uniqid();
        $info = pathinfo($directory.$uploadedFile->getClientOriginalName());
        rename($directory.$uploadedFile->getClientOriginalName(), $directory.$fileName.'.'.$info['extension']);
        fclose($file);

        /* User edition */
        $user->setPicture($fileName.'.'.$info['extension']);
        $em->flush();

        return new JsonResponse($user);
    }
}
