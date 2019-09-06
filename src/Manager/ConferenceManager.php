<?php

namespace App\Manager;

use App\Repository\ConferenceRepository;

class ConferenceManager
{
    private $ConferenceRepository;

    public function __construct(ConferenceRepository $conferenceRepository)
    {
        $this->conferenceRepository = $conferenceRepository;
    }


    public function searchConferencesByTitle(string $search)
    {
        return $this->conferenceRepository->createQueryBuilder('c')
            ->where("c.title like '%" . $search . "%'")
            ->getQuery()
            ->getArrayResult();
    }

}