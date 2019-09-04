<?php

namespace App\Manager;

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
}