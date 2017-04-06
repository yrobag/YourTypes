<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Game controller.
 *
 * @Route("admin/game")
 */
class GameController extends Controller
{
    /**
     * Lists all game entities.
     *
     * @Route("/previous/{page}", defaults={"page"=1})
     * @Method("GET")
     * @Template(":game:previous_games.html.twig")
     */
    public function showPreviousAction($page)
    {
        $dql = 'SELECT g FROM AppBundle:Game g WHERE g.data < CURRENT_TIMESTAMP() ORDER BY g.data DESC';
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

        $forms = [];

        foreach ($games as $key => $game) {

            $form = $this->createForm("AppBundle\Form\ResultType", $game);
            $form->get('gameId')->setData($game->getId());
            $forms[] = ['form' => $form->createView(), 'game' => $game];
        }

        return ['forms' => $forms, 'page'=>['currentPage'=>$page, 'pageCount'=>$pageCount]];
    }

    /**
     * Lists all game entities.
     *
     * @Route("/next/{page}", defaults={"page"=1})
     * @Method("GET")
     * @Template(":game:next_games.html.twig")
     */
    public function showNextAction($page)
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

        return ['games' => $games, 'page'=>['currentPage'=>$page, 'pageCount'=>$pageCount]];
    }

    /**
     * Creates a new game entity.
     *
     * @Route("/new")
     * @Method({"GET", "POST"})
     * @Template("game/new.html.twig")
     */
    public function newAction(Request $request)
    {
        $game = new Game();
        $form = $this->createForm('AppBundle\Form\GameType', $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush();

            return $this->redirectToRoute('app_game_shownext');
        }

        return ['game' => $game, 'form' => $form->createView()];
    }


    /**
     * Displays a form to edit an existing game entity.
     *
     * @Route("/{id}/edit", name="game_edit")
     * @Method({"GET", "POST"})
     * @Template("game/edit.html.twig")
     */
    public function editAction(Request $request, Game $game)
    {
        $deleteForm = $this->createDeleteForm($game);
        $editForm = $this->createForm('AppBundle\Form\GameType', $game);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_game_shownext');
        }

        return [
            'game' => $game,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }


    /**
     * @Route("/setResult")
     * @Method("POST")
     */
    public function setResultAction(Request $request)
    {

        $form = $this->createForm('AppBundle\Form\ResultType');
        $form->handleRequest($request);


        $game = $this->getDoctrine()->getManager()->getRepository('AppBundle:Game')->find($form->get('gameId')->getData());
        if (!$game) {
            throw $this->createNotFoundException('There is no game with this id');
        }

        $form = $this->createForm('AppBundle\Form\ResultType', $game);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush();
            $this->pointsCalculateAction($game);
        }

        return $this->redirectToRoute('app_game_showprevious');

    }

    /**
     * Deletes a game entity.
     *
     * @Route("/{id}", name="game_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Game $game)
    {
        $form = $this->createDeleteForm($game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($game);
            $em->flush();
        }

        return $this->redirectToRoute('app_game_shownext');
    }

    /**
     * Creates a form to delete a game entity.
     *
     * @param Game $game The game entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Game $game)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('game_delete', ['id' => $game->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


    private function pointsCalculateAction($game)
    {
        $em = $this->getDoctrine()->getManager();
        $types = $em->getRepository('AppBundle:Type')->findGameAllTypes($game);
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
    }

    private function sign($n)
    {
        return ($n > 0) - ($n < 0);
    }
}
