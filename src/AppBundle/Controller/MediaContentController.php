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
     * @QueryParam(name="type", nullable=false, requirements="TrainingContent", description="Type of entity to rely to the media")
     * @QueryParam(name="id", nullable=false, description="Identifier of the entity to rely to the media")
     * @QueryParam(name="field", nullable=false, requirements="illustration|exerciseImageToTranscribe", description="Field of the entity where to store the media")
     *
     * @Doc\ApiDoc(
     *     section="Media",
     *     resource=true,
     *     description="Upload an image for a content",
     *     statusCodes={
     *         201="Returned when created",
     *         400="Returned when a violation is raised by validation"
     *     }
     * )
     *
     * WARNING > Each entity using this method need an agnostic SETTER in its methods
     */
    public function postMediaContentAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $type = $paramFetcher->get('type');
        $id = intval($paramFetcher->get('id'));
        $field = $paramFetcher->get('field');

        $entity = $em->getRepository('AppBundle:'.$type)->findOneById($id);
        if($entity === null) {throw new AccessDeniedException('This query does not have access to this section.');}

        /* Upload logic */
        $uploadedFile = $request->files->get('media');
        $directory = __DIR__.'/../../../web/uploads/';
        $uploadedFile->move($directory, $uploadedFile->getClientOriginalName());

        $file = fopen($directory.$uploadedFile->getClientOriginalName(), 'r');
        $fileName = uniqid();
        $info = pathinfo($directory.$uploadedFile->getClientOriginalName());
        rename($directory.$uploadedFile->getClientOriginalName(), $directory.$fileName.'.'.$info['extension']);
        fclose($file);

        /* User edition */
        $entity->set($field, $fileName.'.'.$info['extension']);
        $em->flush();

        return $entity;
    }
}
