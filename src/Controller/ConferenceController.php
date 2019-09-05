<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Manager\EmailManager;
use App\Manager\RatingManager;
use App\Repository\ConferenceRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conference")
 */
class ConferenceController extends AbstractController
{
    /**
     * @Route("/all", name="conference_index", methods={"GET"})
     */
    public function index(ConferenceRepository $conferenceRepository, RatingManager $ratingManager): Response
    {
        $conferences = $conferenceRepository->findAll();
        $averageRatings = [];
        foreach ($conferences as $conference){
            $average = $ratingManager->getAverageRatingFromConferenceId($conference->getId());
            $averageRatings[$conference->getId()] = $average;
        }

        return $this->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
            'averageRatings' => $averageRatings,
        ]);
    }

    /**
     * @Route("/best", name="conference_best", methods={"GET"})
     */
    public function best(ConferenceRepository $conferenceRepository, RatingManager $ratingManager): Response
    {

        // Nombre de conférences à afficher
        $nbConferences = 10;

        return $this->render('conference/best.html.twig', [
            'conferences' => $ratingManager->getBestConferences($nbConferences),
            'nbConferences' => $nbConferences,
        ]);
    }

    /**
     * @Route("/new", name="conference_new", methods={"GET","POST"})
     */
    public function new(Request $request, EmailManager $emailManager): Response
    {
        $conference = new Conference();
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conference);
            $entityManager->flush();

            $emailManager->sendMailNewConferenceToAllUsers($conference);
            return $this->redirectToRoute('conference_index');
        }

        return $this->render('conference/new.html.twig', [
            'conference' => $conference,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="conference_show", methods={"GET"})
     */
    public function show(Conference $conference, RatingManager $ratingManager): Response
    {
        $average = $ratingManager->getAverageRatingFromConferenceId($conference->getId());

        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'average' => $average,
        ]);
    }

    /**
     * @Route("edit/{id}", name="conference_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Conference $conference): Response
    {
        $form = $this->createForm(ConferenceType::class, $conference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('conference_index');
        }

        return $this->render('conference/edit.html.twig', [
            'conference' => $conference,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="conference_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Conference $conference): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conference->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($conference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conference_index');
    }
}
