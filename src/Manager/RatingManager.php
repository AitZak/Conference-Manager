<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\RatingRepository;

class RatingManager
{
    private $ratingRepository;

    public function __construct(RatingRepository $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }


    public function getAverageRatingFromConferenceId(int $conferenceId)
    {
        $ratings = $this->ratingRepository->findBy(['conference'=>$conferenceId]);
        $ratingArray = [];
        foreach ($ratings as $rating)
        {
            $ratingArray[] = $rating->getValue();
        }
        if (count($ratings) > 0){
            return array_sum($ratingArray) / count($ratings);
        }
        return 0;
    }

    public function getVotedConferencesByUser(User $user)
    {
        $ratings = $this->ratingRepository->findBy(['user' => $user]);
        $conferencesId = [];
        foreach ($ratings as $rating){
            array_push($conferencesId, $rating->getConference()->getId());
        }
        return $conferencesId;
    }

}