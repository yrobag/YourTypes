<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/")
     * @Method("GET")
     */
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();

        $games = $em->getRepository('AppBundle:Game')->findAll();

        return $this->render('game/index.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * Creates a new game entity.
     *
     * @Route("/new")
     * @Method({"GET", "POST"})
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

            return $this->redirectToRoute('game_show', array('id' => $game->getId()));
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a game entity.
     *
     * @Route("/{id}", name="game_show")
     * @Method("GET")
     */
    public function showAction(Game $game)
    {
        $deleteForm = $this->createDeleteForm($game);

        return $this->render('game/show.html.twig', [
            'game' => $game,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing game entity.
     *
     * @Route("/{id}/edit", name="game_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Game $game)
    {
        $deleteForm = $this->createDeleteForm($game);
        $editForm = $this->createForm('AppBundle\Form\GameType', $game);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_edit', array('id' => $game->getId()));
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }


    /**
     * @Route("/{id}/setResult")
     * @Method({"GET", "POST"})
     */
    public function setResultAction(Request $request, Game $game)
    {
        $resultForm = $this->createForm('AppBundle\Form\ResultType', $game);
        $resultForm->handleRequest($request);

        if ($resultForm->isSubmitted() && $resultForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->pointsCalculateAction();
            return $this->redirectToRoute("app_game_showall");
        }

        return $this->render('game/result.html.twig', [
            'game' => $game,
            'form' => $resultForm->createView()
        ]);
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

        return $this->redirectToRoute('game_index');
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


    private function pointsCalculateAction()
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
    }
}
