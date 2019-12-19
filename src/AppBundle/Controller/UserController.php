<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Exception\ResourceValidationException;
use AppBundle\Representation\Users;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Response;
use Hateoas\Representation\PaginatedRepresentation;
use Nelmio\ApiDocBundle\Annotation as Doc;
use AppBundle\Form\UserType;
use Requests;


class UserController  extends FOSRestController
{

    /**
     * @Rest\Post("/user")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Ajouter un nouveau utilisateur.",
     *     input={"class"=UserType::class, "name"=""}
     * )
     * @throws ResourceValidationException
     */
    public function newAction(User $user, ConstraintViolationList $violations, Request $request){
        $em = $this->getDoctrine()->getManager();

        $user->setCreationdate(new \DateTime());
        $user->setUpdatedate(new \DateTime());
        $em->persist($user);
        $em->flush();

        return $user;
    }


    /**
     * @Rest\Get("/user", name="user_index")
     * @Rest\QueryParam(
     *     name="lastname",
     *     requirements="\w+",
     *     nullable=true,
     *     description="Le nom de l'utilisateur pour le filtre."
     * )
     * @Rest\QueryParam(
     *     name="firstname",
     *     requirements="\w+",
     *     nullable=true,
     *     description="Le prénom de l'utilisateur pour le filtre."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="L'ordre de tri de la liste"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Le nombre maximum  d'utilisateur par page"
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="Le numero par lequel commencer la pagination "
     * )
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Récupérer la liste des utilisateur suivant des paramètres."
     * )
     * @Rest\View()
     */

    public function listAction(ParamFetcherInterface $paramFetcher, Request $request)
    {

        $pager = $this->getDoctrine()->getRepository(User::class)->search(
            $paramFetcher->get('lastname'),
            $paramFetcher->get('firstname'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Users($pager);
    }


    /**
     * @Rest\Get(
     *     path = "/user/{id}",
     *     name = "user_show",
     *     requirements = {"id"="\d+"}
     * )
     *
     *  @Doc\ApiDoc(
     *     resource=true,
     *     description="Afficher un utilisateur à partir de son ID."
     * )
     * @Rest\View
     */
    public function showAction(User $user, Request $request){
        return $user;
    }


    /**
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "/user/{id}",
     *     name = "user_update",
     *     requirements = {"id"="\d+"}
     * )
     *
     * @ParamConverter("newUser", converter="fos_rest.request_body")
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Mettre à jour un utilisateur.",
     *     input={"class"=UserType::class, "name"=""}
     * )
     * @throws ResourceValidationException
     */
    public function updateAction(User $user, User $newUser, ConstraintViolationList $violations, Request $request){
        $user->setFirstname($newUser->getFirstname());
        $user->setLastname($newUser->getLastname());
        $user->setUpdatedate(new \DateTime('now'));
        $this->getDoctrine()->getManager()->flush();

        return $user;
    }


    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/user/{id}",
     *     name = "user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Supprimer un utilisateur."
     * )
     */
    public function deleteAction(User $user) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return;
    }

    /**
     * @Rest\Get(
     *     path="/",
     *     name="index"
     * )
     */
    public function indexAction() {
        die('Accès denied..');
    }

}