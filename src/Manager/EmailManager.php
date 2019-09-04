<?php


namespace App\Manager;


use App\Entity\Conference;
use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Container\ContainerInterface;

class EmailManager
{
    private $mailer;
    private $userRepository;
    private $container;

    public function __construct(\Swift_Mailer $mailer, UserRepository $userRepository, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->container =$container;
    }

    public function sendMailNewConferenceToAllUsers(Conference $conference)
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user){
            $message = new  \Swift_Message('Une nouvelle conférence a été ajoutée !');
            $message->setTo($user->getEmail())
                ->setFrom('test@test.fr')
                ->setBody(
                    $this->renderView(
                        'emails/new_conference.html.twig',
                        [
                            'firstName' => $user->getFirstName(),
                            'titleConf' => $conference->getTitle(),
                        ]
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        }
    }

    /**
     * Returns a rendered view.
     *
     * @final
     */
    protected function renderView(string $view, array $parameters = []): string
    {
        if ($this->container->has('templating')) {
            @trigger_error('Using the "templating" service is deprecated since version 4.3 and will be removed in 5.0; use Twig instead.', E_USER_DEPRECATED);

            return $this->container->get('templating')->render($view, $parameters);
        }

        if (!$this->container->has('twig')) {
            throw new \LogicException('You can not use the "renderView" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');
        }

        return $this->container->get('twig')->render($view, $parameters);
    }
}