<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Repository\ConferenceRepository;
use App\Repository\RatingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    /**
     * @Route("/submitRating", name="submit_rate")
     */
    public function index(Request $request, UserRepository $userRepository, ConferenceRepository $conferenceRepository, RatingRepository $ratingRepository, EntityManagerInterface $entityManager)
    {
        $rate = $request->get('rate');
        $userId = $request->get('userId');
        $confId = $request->get('confId');
        $user = $userRepository->findOneBy(['id'=>$userId]);
        $conference = $conferenceRepository->findOneBy(['id' => $confId]);
        $rating = $ratingRepository->findOneBy(['conference'=> $conference, 'user'=>$user]);
        if (empty($rating)) {
            $ratingNew = new Rating();
            $ratingNew->setConference($conference);
            $ratingNew->setUser($user);
            $ratingNew->setValue($rate);
            $entityManager->persist($ratingNew);
        } else {
            $rating->setValue($rate);
        }
        $entityManager->flush();
        return $this->redirectToRoute('home');
    }
}
