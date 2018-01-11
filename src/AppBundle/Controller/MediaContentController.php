<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\AppPreference;

use AppBundle\Form\AppPreferenceType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MediaContentController extends FOSRestController
{
    /**
     * @Rest\Post("/media-contents")
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     *
     * @QueryParam(name="type", nullable=false, requirements="Testator|Content|TrainingContent|HostingOrganization", description="Type of entity to rely to the media")
     * @QueryParam(name="id", nullable=true, description="Identifier of the entity to rely to the media")
     * @QueryParam(name="field", nullable=false, requirements="picture|illustration|exerciseImageToTranscribe|logo", description="Field of the entity where to store the media")
     *
     * @Doc\ApiDoc(
     *     section="Media",
     *     resource=true,
     *     description="Upload an image for a content",
     *     parameters={
     *         { "name"="type", "dataType"="string", "description"="Type of entity to rely to the media", "required"=true },
     *         { "name"="id", "dataType"="integer", "description"="Identifier of the entity to rely to the media", "required"=false },
     *         { "name"="field", "dataType"="string", "description"="Field of the entity where to store the media", "required"=true },
     *     },
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     *
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @return mixed
     *
     * WARNING > Each entity using this method need an agnostic SETTER in its methods
     */
    public function postMediaContentAction(Request $request, ParamFetcher $paramFetcher)
    {
        if($paramFetcher->get('id') != '') {
            return $this->postOnExistingEntity($paramFetcher->get('type'), intval($paramFetcher->get('id')), $paramFetcher->get('field'), $request);
        } else {
            return new JsonResponse($this->postOnNonExistingEntity($request));
        }
    }

    /**
     * @param $type
     * @param $id
     * @param $field
     * @param Request $request
     * @return mixed
     */
    public function postOnExistingEntity($type, $id, $field, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:'.$type)->findOneById($id);
        if($entity === null) {throw new AccessDeniedException('This query does not have access to this section.');}

        $file = $this->postOnNonExistingEntity($request);

        /* User edition */
        $entity->set($field, $file);
        $em->flush();

        return $entity;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function postOnNonExistingEntity(Request $request)
    {
        /* Upload logic */
        $uploadedFile = $request->files->get('media');
        $directory = __DIR__.'/../../../web/uploads/';
        $uploadedFile->move($directory, $uploadedFile->getClientOriginalName());

        $file = fopen($directory.$uploadedFile->getClientOriginalName(), 'r');
        $fileName = uniqid();
        $info = pathinfo($directory.$uploadedFile->getClientOriginalName());
        rename($directory.$uploadedFile->getClientOriginalName(), $directory.$fileName.'.'.$info['extension']);
        fclose($file);

        return $fileName.'.'.$info['extension'];
    }
}
