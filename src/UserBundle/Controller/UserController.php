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
     * @QueryParam(name="profile", nullable=true, description="Quantity of information returned about the user.")
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
        $profile = explode(',', $paramFetcher->get('profile'));

        $em = $this->getDoctrine()->getManager();

        $groups = ['accesses', 'id', 'content'];
        if($profile == 'full') {
            $groups = ['full'];
        }

        if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') and in_array('email', $profile) == true) {
            $users = $em->getRepository('UserBundle:User')->findAll();
            /** @var $users User */
            $groups = ['userEmail'];
        } elseif($token != "") {
            $users = $em->getRepository('UserBundle:AccessToken')->findOneByToken($token)->getUser();
            /** @var $users User */
            $groups[] = 'privateMessages';
            $groups[] = 'userPreferences';
        } elseif($username != "") {
            $users = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));
            /** @var $users User */
        } else {
            $users = $em->getRepository('UserBundle:User')->findAll();
            /** @var $users User[] */
            if(!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $groups = ['id','name'];
            }
        }


        $view = $this->view($users, 200);
        $context = new Context();
        $context->setGroups($groups);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/users/{id}")
     * @QueryParam(name="profile", nullable=true, description="Quantity of information returned about the user.")
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
        $profile = explode(',', $paramFetcher->get('profile'));

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */

        if(empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if($paramFetcher->get('profile') == '') {
            $groups = ['preferences', 'id', 'accesses', 'content'];
        } else {
            $groups = explode(',', $paramFetcher->get('profile'));
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
     * @Security("is_granted('ROLE_USER')")
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
     * @Security("is_granted('ROLE_USER')")
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
     * @QueryParam(name="silent", nullable=true, description="Do you want to send a notification to the deleted user?")
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
     * @Security("is_granted('ROLE_USER')")
     */
    public function removeUserAction(Request $request, ParamFetcher $paramFetcher)
    {
        $silent = filter_var($paramFetcher->get('silent'), FILTER_VALIDATE_BOOLEAN);;
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($request->get('id'));
        /* @var $user User */

        if($user AND ($this->get('security.token_storage')->getToken()->getUser() == $user OR $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))) {
            $proviUser = $user;

            foreach($em->getRepository('UserBundle:Preference')->findBy(array('user' => $user)) as $item) {$em->remove($item);}
            foreach($em->getRepository('UserBundle:Access')->findBy(array('user' => $user)) as $item) {$em->remove($item);}
            foreach($em->getRepository('UserBundle:AccessToken')->findBy(array('user' => $user)) as $item) {$em->remove($item);}
            foreach($em->getRepository('UserBundle:RefreshToken')->findBy(array('user' => $user)) as $item) {$em->remove($item);}

            foreach($em->getRepository('AppBundle:Content')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:Content')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:Comment\Comment')->findBy(array('author' => $user)) as $item) {$item->setAuthor(null);}
            foreach($em->getRepository('AppBundle:AppPreference')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:CommentLog')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:CommentLog')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:Entity')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:Entity')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:HostingOrganization')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:HostingOrganization')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:ManuscriptReference')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:ManuscriptReference')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:MilitaryUnit')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:MilitaryUnit')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:Note')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:Note')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:Place')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:Place')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:PlaceName')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:PlaceName')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:PrintedReference')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:PrintedReference')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:ReferenceItem')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:ReferenceItem')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:Resource')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:Resource')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:TaxonomyVersion')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:TaxonomyVersion')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:TaxonomyVersion')->findBy(array('reviewBy' => $user)) as $item) {$item->setReviewBy(null);}
            foreach($em->getRepository('AppBundle:Testator')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:Testator')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:TrainingContent')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:TrainingContent')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:TrainingResult')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:TrainingResult')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:Transcript')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:Transcript')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:Transcript')->findBy(array('submitUser' => $user)) as $item) {$item->setSubmitUser(null);}
            foreach($em->getRepository('AppBundle:TranscriptLog')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:TranscriptLog')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:Will')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:Will')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}
            foreach($em->getRepository('AppBundle:WillType')->findBy(array('createUser' => $user)) as $item) {$item->setCreateUser(null);}
            foreach($em->getRepository('AppBundle:WillType')->findBy(array('updateUser' => $user)) as $item) {$item->setUpdateUser(null);}

            foreach($em->getRepository('AppBundle:TrainingContent')->findAll() as $item) {
                foreach($item->getEditorialResponsibility() as $iUser) {
                    if($iUser === $user) {
                        $item->removeEditorialResponsibility($user);
                    }
                }
            }
            foreach($em->getRepository('AppBundle:Content')->findAll() as $item) {
                foreach($item->getEditorialResponsibility() as $iUser) {
                    if($iUser === $user) {
                        $item->removeEditorialResponsibility($user);
                    }
                }
            }

            $em->remove($user);
            $em->flush();

            if($silent == false) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Suppression de compte - Testaments de Poilus')
                    ->setFrom('testaments-de-poilus@huma-num.fr')
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView(
                        'UserBundle:Remove:email.txt.twig',
                        array('user' => $proviUser)));
                $this->get('mailer')->send($message);
            }
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
     *         },
     *         {
     *             "name"="roles",
     *             "dataType"="array",
     *             "description"="The list of roles.",
     *         },
     *         {
     *             "name"="action",
     *             "dataType"="string",
     *             "requirements"="promote|demote|set",
     *             "description"="The list of roles.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_USER')")
     */
    public function setRoleAction(Request $request)
    {
        $user_id = $request->get('id');
        $roles = $request->get('roles');
        $action = $request->get('action');

        $em = $this->getDoctrine()->getManager();
        $iUser = $em->getRepository('UserBundle:User')->find($user_id);
        /* @var $iUser User */
        if($iUser === null) {return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);}
        /** @var $access Access */
        $access = $em->getRepository('UserBundle:Access')->findOneBy(array('user' => $iUser));
        $setTaxoTrue = false;
        $emailNotification = false;

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_MODO') and $this->get('security.token_storage')->getToken()->getUser() != $iUser) {
            throw $this->createAccessDeniedException('Unable to access this page!');
        }

        $allowedRoles = ['ROLE_USER', 'ROLE_MODO', 'ROLE_TAXONOMY_EDIT'];
        if($action == "set") {
            $iUser->setRoles($roles);

            if(in_array("ROLE_TAXONOMY_EDIT", $roles)) {
                $setTaxoTrue = true;
            }

            $emailNotification = true;
        } else {
            foreach ($roles as $role) {
                if (in_array($role, $allowedRoles)) {
                    if ($action == "promote") {
                        $iUser->addRole($role);
                        $emailNotification = true;
                        if ($role == 'ROLE_TAXONOMY_EDIT') {$setTaxoTrue = true;}
                    } elseif ($action == "demote") {
                        $iUser->removeRole($role);

                        if ($role == 'ROLE_TAXONOMY_EDIT') {
                            $access->setIsTaxonomyAccess(false);
                        }
                    }

                }
            }
        }

        if($setTaxoTrue == true) {
            $access->setTaxonomyRequest(null);
            $access->setIsTaxonomyAccess(true);
        }

        if($emailNotification == true) {
            $message = \Swift_Message::newInstance()
                ->setSubject('Votre compte dispose d\'un nouveau role - Testaments de Poilus')
                ->setFrom('testaments-de-poilus@huma-num.fr')
                ->setTo($iUser->getEmail())
                ->setBody($this->renderView(
                    'UserBundle:SetRole:emailPromote.html.twig',
                    array('user' => $iUser, 'roles' => $roles)));
            $this->get('mailer')->send($message);
        }

        $em->flush();

        return $iUser;
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
     * @Security("is_granted('ROLE_USER')")
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

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_MODO') and $this->get('security.token_storage')->getToken()->getUser() != $user) {
            throw $this->createAccessDeniedException('Unable to access this page!');
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

        return $user;
    }
}
