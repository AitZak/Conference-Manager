<?php

namespace App\Manager;

use App\Repository\RankingRepository;

class RankingManager
{
    private $rankingRepository;

    public function __construct(RankingRepository $rankingRepository)
    {
        $this->rankingRepository = $rankingRepository;
    }


    public function getAverageRatingFromConferenceId(int $conferenceId)
    {
        $rankings = $this->rankingRepository->findBy(['conference'=>$conferenceId]);
        $rankingArray = [];
        foreach ($rankings as $ranking)
        {
            $rankingArray[] = $ranking->getValue();
        }
        if (count($rankings) > 0){
            return array_sum($rankingArray) / count($rankings);
        }
        return 0;
    }
}