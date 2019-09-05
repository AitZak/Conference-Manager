<?php

namespace App\Manager;

use App\Entity\Conference;
use App\Entity\Rating;
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

    public function getBestConferences(int $nbConferences)
    {
        return $this->ratingRepository->createQueryBuilder('r')
            ->innerJoin('r.conference', 'c')
            ->select('c.id, c.title, c.description, AVG(r.value) AS average')
            ->groupBy('c.id, c.title, c.description')
            ->orderBy('AVG(r.value) ', 'DESC')
            ->setMaxResults($nbConferences)
            ->getQuery()
            ->getArrayResult();
    }

}