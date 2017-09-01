<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Testator;

use AppBundle\Form\TestatorType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TestatorController extends FOSRestController
{
    /**
     * @Rest\Get("/testators")
     * @Rest\View()
     *
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Get the list of all testators",
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTestatorsAction(Request $request)
    {
        $testators = $this->getDoctrine()->getManager()->getRepository('AppBundle:Testator')->findAll();
        /* @var $testators Testator[] */

        return $testators;
    }

    /**
     * @Rest\Get("/testators/{id}")
     * @Rest\View
     *
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Return one testator",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the testator.",
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when fetched",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     */
    public function getTestatorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $testator = $em->getRepository('AppBundle:Testator')->find($request->get('id'));
        /* @var $testator Testator */

        if (empty($testator)) {
            return new JsonResponse(['message' => 'Testator not found'], Response::HTTP_NOT_FOUND);
        }

        return $testator;
    }

    /**
     * @Rest\Post("/testators")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Create a new testator",
     *     requirements={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The name of the testator in natural language."
     *         },
     *         {
     *             "name"="surname",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The surname of the testator. If the person is noble, reject the particle at the end of the name."
     *         },
     *         {
     *             "name"="firstnames",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The list of the firstnames of the testator."
     *         },
     *         {
     *             "name"="profession",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The profession of the testator."
     *         },
     *         {
     *             "name"="address",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The address of the testator."
     *         },
     *         {
     *             "name"="date_of_birth",
     *             "dataType"="date",
     *             "requirement"="",
     *             "description"="The date of birth of the testator."
     *         },
     *         {
     *             "name"="place_of_birth",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The place of birth of the testator."
     *         },
     *         {
     *             "name"="date_of_death",
     *             "dataType"="date",
     *             "requirement"="",
     *             "description"="The date of death of the testator."
     *         },
     *         {
     *             "name"="place_of_death",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The place of death of the testator."
     *         },
     *         {
     *             "name"="death_mention",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Like 'Mort pour la France'."
     *         },
     *         {
     *             "name"="memoire_des_hommes",
     *             "dataType"="url",
     *             "requirement"="\S{0,255}",
     *             "description"="The link to the soldier notice in Mémoire des Hommes of the testator."
     *         },
     *         {
     *             "name"="regiment",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The name of the regiment of the testator."
     *         },
     *         {
     *             "name"="rank",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The rank in the army of the testator."
     *         }
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
     */
    public function postTestatorsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $testator = new Testator();
        $form = $this->createForm(TestatorType::class, $testator);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->persist($testator);
            $em->flush();
            return $testator;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/testators/{id}")
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Update an existing testator",
     *     requirements={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The name of the testator in natural language."
     *         },
     *         {
     *             "name"="surname",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The surname of the testator. If the person is noble, reject the particle at the end of the name."
     *         },
     *         {
     *             "name"="firstnames",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The list of the firstnames of the testator."
     *         },
     *         {
     *             "name"="profession",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The profession of the testator."
     *         },
     *         {
     *             "name"="address",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The address of the testator."
     *         },
     *         {
     *             "name"="date_of_birth",
     *             "dataType"="date",
     *             "requirement"="",
     *             "description"="The date of birth of the testator."
     *         },
     *         {
     *             "name"="place_of_birth",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The place of birth of the testator."
     *         },
     *         {
     *             "name"="date_of_death",
     *             "dataType"="date",
     *             "requirement"="",
     *             "description"="The date of death of the testator."
     *         },
     *         {
     *             "name"="place_of_death",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The place of death of the testator."
     *         },
     *         {
     *             "name"="death_mention",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Like 'Mort pour la France'."
     *         },
     *         {
     *             "name"="memoire_des_hommes",
     *             "dataType"="url",
     *             "requirement"="\S{0,255}",
     *             "description"="The link to the soldier notice in Mémoire des Hommes of the testator."
     *         },
     *         {
     *             "name"="regiment",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The name of the regiment of the testator."
     *         },
     *         {
     *             "name"="rank",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The rank in the army of the testator."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
     */
    public function updateTestatorAction(Request $request)
    {
        return $this->updateTestator($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/testators/{id}")
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Update an existing testator",
     *     requirements={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The name of the testator in natural language."
     *         },
     *         {
     *             "name"="surname",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The surname of the testator. If the person is noble, reject the particle at the end of the name."
     *         },
     *         {
     *             "name"="firstnames",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The list of the firstnames of the testator."
     *         },
     *         {
     *             "name"="profession",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The profession of the testator."
     *         },
     *         {
     *             "name"="address",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The address of the testator."
     *         },
     *         {
     *             "name"="date_of_birth",
     *             "dataType"="date",
     *             "requirement"="",
     *             "description"="The date of birth of the testator."
     *         },
     *         {
     *             "name"="place_of_birth",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The place of birth of the testator."
     *         },
     *         {
     *             "name"="date_of_death",
     *             "dataType"="date",
     *             "requirement"="",
     *             "description"="The date of death of the testator."
     *         },
     *         {
     *             "name"="place_of_death",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The place of death of the testator."
     *         },
     *         {
     *             "name"="death_mention",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="Like 'Mort pour la France'."
     *         },
     *         {
     *             "name"="memoire_des_hommes",
     *             "dataType"="url",
     *             "requirement"="\S{0,255}",
     *             "description"="The link to the soldier notice in Mémoire des Hommes of the testator."
     *         },
     *         {
     *             "name"="regiment",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The name of the regiment of the testator."
     *         },
     *         {
     *             "name"="rank",
     *             "dataType"="string",
     *             "requirement"="\S{0,255}",
     *             "description"="The rank in the army of the testator."
     *         }
     *     },
     *     statusCodes={
     *         200="Returned when updated",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_TAXONOMY_EDIT')")
     */
    public function patchTestatorAction(Request $request)
    {
        return $this->updateTestator($request, false);
    }

    private function updateTestator(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $testator = $em->getRepository('AppBundle:Testator')
            ->find($request->get('id'));
        /* @var $testator Testator */
        if (empty($testator)) {
            return new JsonResponse(['message' => 'Testator not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm(TestatorType::class, $testator);
        $form->submit($request->request->all(), $clearMissing);
        if ($form->isValid()) {
            $em->merge($testator);
            $em->flush();
            return $testator;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\Delete("/testators/{id}")
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     *
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Remove a testator",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirement"="\d+",
     *             "description"="The unique identifier of the testator.",
     *         }
     *     },
     *     statusCodes={
     *         204="Returned when deleted",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     * @Security("is_granted('ROLE_MODO')")
     */
    public function removeTestatorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $testator = $em->getRepository('AppBundle:Testator')->find($request->get('id'));
        /* @var $testator Testator */

        if ($testator) {
            $em->remove($testator);
            $em->flush();
        }
    }
}
