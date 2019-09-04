<?php

namespace App\Controller;

use App\Manager\RatingManager;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/    ", name="home")
     */
    public function index(ConferenceRepository $conferenceRepository, RatingManager $ratingManager)
    {
        $conferences =$conferenceRepository->findAll();
        $averageRatings = [];
        foreach ($conferences as $conference){
            $average = $ratingManager->getAverageRatingFromConferenceId($conference->getId());
            $averageRatings[$conference->getId()] = $average;
        }
        return $this->render('home/index.html.twig', [
            'conferences' => $conferences,
            'averageRatings' => $averageRatings,
        ]);
    }
}
