<?php

namespace DataBundle\Controller;

use DataBundle\Entity\Testator;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation as Doc;

class TestatorController extends FOSRestController
{
    /**
     * @Rest\Get(
     *     path = "/testators/{id}",
     *     name = "data_testator_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 200)
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
    public function showAction(Testator $testator)
    {
        return $testator;
    }

    /**
     * @Rest\Get(
     *    path = "/testators",
     *    name = "data_testator_list"
     * )
     * @Rest\View(StatusCode = 200)
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
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository("DataBundle:Testator")->findAll();
    }

    /**
     * @Rest\Post(
     *    path = "/testators",
     *    name = "data_testator_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("testator", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Create a new testator",
     *     requirements={
     *         {
     *             "name"="full_name",
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
     */
    public function createAction(Testator $testator)
    {
        //dump($testator); die;
        $em = $this->getDoctrine()->getManager();
        $em->persist($testator);
        $em->flush();

        return $this->view($testator, Response::HTTP_CREATED, ['Location' => $this->generateUrl('data_testator_show', ['id' => $testator->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]);
    }

    /**
     * @Rest\Put(
     *    path = "/testators/{id}",
     *    name = "data_testator_update",
     *    requirements = {"id"="\d+"}
     * )
     * @Rest\View(StatusCode = 200)
     * @ParamConverter("newTestator", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     section="Testators",
     *     resource=true,
     *     description="Update an existing testator",
     *     requirements={
     *         {
     *             "name"="full_name",
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
     */
    public function updateAction(Testator $testator, Testator $newTestator)
    {
        $testator->setFullName($newTestator->getFullName());
        $testator->setSurname($newTestator->getSurname());
        $testator->setFirstnames($newTestator->getFirstnames());
        $testator->setProfession($newTestator->getProfession());
        $testator->setDateOfBirth($newTestator->getDateOfBirth());
        $testator->setPlaceOfBirth($newTestator->getPlaceOfBirth());
        $testator->setDateOfDeath($newTestator->getDateOfDeath());
        $testator->setPlaceOfDeath($newTestator->getPlaceOfDeath());
        $testator->setDeathMention($newTestator->getDeathMention());
        $testator->setMemoireDesHommes($newTestator->getMemoireDesHommes());
        $testator->setRegiment($newTestator->getRegiment());
        $testator->setRank($newTestator->getRank());

        $this->getDoctrine()->getManager()->flush();

        return $testator;
    }

    /**
     * @Rest\Delete(
     *     path = "/testators/{id}",
     *     name = "data_testator_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode = 204)
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
     */
    public function deleteAction(Testator $testator)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($testator);
        $em->flush();

        return;
    }
}
