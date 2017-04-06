<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Type;
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
     * @Route("/myTypes")
     * @Template(":type:my_types.html.twig")
     */
    public function myTypesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $games = $em->getRepository('AppBundle:Game')->findNextGames();
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


        return ['games' => $games, 'forms' => $forms];
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
     * @Route("/showPreviousTypes/{username}", defaults={"username" = null})
     * @Template(":type:previous_types.html.twig")
     */
    public function showPreviousTypesAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        if($username === null){
            $user = $this->getUser();
        }else{
            $user = $username === null ? $this->getUser()
                : $this->container->get('fos_user.user_manager')->findUserByUsername($username);
        }


        $types = $em->getRepository('AppBundle:Type')->findPreviousTypes($user);
        return ['types' => $types];
    }

    /**
     * @Route("/table")
     * @Template(":type:table.html.twig")
     */
    public function tableAction()
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
        return ['users'=>$users];
    }


    /**
     * @Route("pointsCalculate")
     */
    public function pointsCalculateAction()
    {
        $em = $this->getDoctrine()->getManager();
        $types = $em->getRepository('AppBundle:Type')->findAllToTable();
        foreach ($types as $type) {
            $typeResult = $type->getTypePointsTeam1() - $type->getTypePointsTeam2();
            $gameResult = $type->getGame()->getPointsTeam1() - $type->getGame()->getPointsTeam2();

            if ($this->sign($typeResult) === $this->sign($gameResult)) {
                $points = $type->getTypePointsTeam1() === $type->getGame()->getPointsTeam1() &&
                $type->getTypePointsTeam2() === $type->getGame()->getPointsTeam2() ? 3 : 1;
            } else {
                $points = 0;
            }
            $type->setPoints($points);

        }
        $em->flush($types);
        return $this->redirectToRoute('app_type_table');
    }


    private function sign($n)
    {
        return ($n > 0) - ($n < 0);
    }
}
