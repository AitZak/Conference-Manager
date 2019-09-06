<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Form\ConferenceType;
use App\Manager\ConferenceManager;
use App\Manager\EmailManager;
use App\Manager\RatingManager;
use App\Repository\ConferenceRepository;
use App\Repository\RatingRepository;
use App\Repository\UserRepository;
use Monolog\Handler\SwiftMailerHandler;
use Swift_SendmailTransport;
use Swift_SmtpTransport;
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
     * @Route("/voted", name="voted_conferences", methods={"GET"})
     */
    public function votedConferences(ConferenceRepository $conferenceRepository, RatingManager $ratingManager)
    {
        $conferencesId = [];
        foreach ($conferenceRepository->findAll() as $conference) {
            array_push($conferencesId, $conference->getId());
        }
        $votedConfId = $ratingManager->getVotedConferencesByUser($this->getUser());
        $unvotedConfId = array_diff($conferencesId,$votedConfId);

        $votedConf = [];
        $i = 0;
        foreach ($votedConfId as $conferenceId){
            $conf = $conferenceRepository->findOneBy(['id' => $conferenceId]);
            $votedConf[$i]['id'] = $conf->getId();
            $votedConf[$i]['title'] = $conf->getTitle();
            $votedConf[$i]['description'] = $conf->getDescription();
            $i++;
        }
        $unvotedConf = [];
        $j = 0;
        foreach ($unvotedConfId as $conferenceId) {
            $conf = $conferenceRepository->findOneBy(['id' => $conferenceId]);
            $unvotedConf[$j]['id'] = $conf->getId();
            $unvotedConf[$j]['title'] = $conf->getTitle();
            $unvotedConf[$j]['description'] = $conf->getDescription();
            $j++;
        }

        return $this->render('conference/votedconferences.html.twig', [
            'votedConferences' => $votedConf,
            'unvotedConferences' => $unvotedConf,
        ]);
    }

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
    public function show(Conference $conference, RatingManager $ratingManager, RatingRepository $ratingRepository): Response
    {
        $average = $ratingManager->getAverageRatingFromConferenceId($conference->getId());

        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'ratings' => $ratingRepository->findAll(),
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

    /**
     * @Route("/search", name="conference_search")
     */
    public function search(Request $request, ConferenceManager $conferenceManager, RatingManager $ratingManager): Response
    {
        $conferences = $conferenceManager->searchConferencesByTitle($request->request->get('titleSearch'));
        $averageRatings = [];
        foreach ($conferences as $conference){
            $average = $ratingManager->getAverageRatingFromConferenceId($conference['id']);
            $averageRatings[$conference['id']] = $average;
        }

        return $this->render('conference/index.html.twig', [
            'conferences' => $conferences,
            'averageRatings' => $averageRatings,
        ]);


    }
}
