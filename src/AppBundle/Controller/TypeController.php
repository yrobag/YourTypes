<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Type;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Type controller.
 *
 */
class TypeController extends Controller
{
    /**
     * @Route("/myTypes/{page}", defaults={"page" = 1})
     * @Template(":type:my_types.html.twig")
     */
    public function myTypesAction($page)
    {
        $dql = 'SELECT g FROM AppBundle:Game g WHERE g.data > CURRENT_TIMESTAMP() ORDER BY g.data ASC';
        $query = $this->getDoctrine()->getEntityManager()->createQuery($dql);
        $games = new Paginator($query);
        $totalItems = count($games);
        $pageCount = ceil($totalItems/10);

        if (!$page<=$pageCount && !is_numeric($page)){
            $page =1;
        }


        $games
            ->getQuery()
            ->setFirstResult(10 * ($page-1))
            ->setMaxResults(10);


        $em = $this->getDoctrine()->getManager();

        $forms = [];
        $user = $this->getUser();
        foreach ($games as $key => $game) {
            $type = $em->getRepository('AppBundle:Type')->findMyType($user, $game);
            if (!$type) {
                $type = new Type();
                $type->setUser($user);
                $type->setGame($game);
            }

            $form = $this->createForm("AppBundle\Form\TypeType", $type);
            $form->get('gameId')->setData($game->getId());
            $forms[] = ['form' => $form->createView(), 'game' => $game];
        }


        return ['games' => $games, 'forms' => $forms, 'page'=>['currentPage'=>$page, 'pageCount'=>$pageCount]];
    }

    /**
     * @Route("/setType")
     * @Method("POST")
     */
    public function setTypeAction(Request $request)
    {
        $form = $this->createForm('AppBundle\Form\TypeType');
        $form->handleRequest($request);

        $game = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game')->find($form->get('gameId')->getData());
        if (!$game) {
            throw $this->createNotFoundException('There is no game with id=');
        }
        $user = $this->getUser();

        $type = $this->getDoctrine()->getRepository('AppBundle:Type')->findMyType($user, $game);
        if (!$type) {
            $type = new Type();

        }

        $form = $this->createForm('AppBundle\Form\TypeType', $type);
        $form->handleRequest($request);


        $type->setUser($user);
        $type->setGame($game);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($type);
            $em->flush();
        }

        return $this->redirectToRoute('app_type_mytypes');
    }

    /**
     * @Route("/previousTypes/{username}", defaults={"username" = null})
     * @Template(":type:previous_types.html.twig")
     */
    public function previousTypesAction(Request $request, $username)
    {

        if($username === null){
            $user = $this->getUser();
        }else{
            $user = $username === null ? $this->getUser()
                : $this->container->get('fos_user.user_manager')->findUserByUsername($username);
        }


        $dql = 'SELECT t FROM AppBundle:Type t JOIN t.game g 
                WHERE (g.data < CURRENT_TIMESTAMP() AND t.user = :user) ORDER BY g.data ASC';
        $query = $this->getDoctrine()->getEntityManager()
            ->createQuery($dql)
            ->setParameter('user', $user);
        $types = new Paginator($query);
        $totalItems = count($types);
        $pageCount = ceil($totalItems/10);
        $page = $request->query->get('page');

        if (!$page<=$pageCount && !is_numeric($page)){
            $page =1;
        }

        $types
            ->getQuery()
            ->setFirstResult(10 * ($page-1))
            ->setMaxResults(10);


        return ['types' => $types, 'page'=>['currentPage'=>$page, 'pageCount'=>$pageCount], 'user'=>$user];
    }

    /**
     * @Route("/table/{page}", defaults={"page"=1})
     * @Template(":type:table.html.twig")
     */
    public function tableAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $types = $em->getRepository('AppBundle:Type')->findAllToTable();
        $users = [];
        foreach ($types as $type) {
            $user = $type->getUser()->getUsername();
            if(!isset($users[$user])){
                $users[$user]=0;
            }
            $users[$user]+=$type->getPoints();
        }
        arsort($users);

        $totalItems = count($users);
        $pageCount = ceil($totalItems/10);

        if (!$page<=$pageCount && !is_numeric($page)){
            $page =1;
        }
        $offset =10 * ($page-1);
        $users = array_slice($users, $offset, 10);
        return ['users'=>$users, 'offset'=>$offset, 'page'=>['currentPage'=>$page, 'pageCount'=>$pageCount]];
    }






}
