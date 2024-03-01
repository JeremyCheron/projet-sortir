<?php

namespace App\Services;

use App\Entity\User;
use App\Form\RegistrationAdminFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationService
{
    public function __construct(private EntityManagerInterface $em,
                                private FormFactoryInterface $formFactory,
                                private UserRepository $userRepository,
                                private UserPasswordHasherInterface $passwordHasher,
                                private MailerInterface $mailer,
                                private TranslatorInterface $translator,
                                private RouterInterface $router)
    {
    }


    public function registerAsAdmin(Request $request): true|\Symfony\Component\Form\FormInterface
    {
        $user = new User();
        $form = $this->formFactory->create(RegistrationAdminFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setActive(true);
            $nickname = $user->getFirstname();
            $nickname = $nickname . $user->getLastname();

            //le password est le prenomnom (hashé dans la base de données)

            $hashedPassword = $this->passwordHasher->hashPassword($user, $nickname);
            $user->setPassword($hashedPassword);
            $nickname = $nickname . rand(100, 999);

            //le pseudo unique et le prenomnom suivi d'un nombre à 3 chiffres aléatoire

            $user->setNickname($nickname);

            $this->em->persist($user);
            $this->em->flush();

            //todo: envoyer mail au nouvel inscrit avec les infos de connexion

            $email = (new TemplatedEmail())
                ->from(new Address('admin@sortir.com', 'Sortir'))
                ->to($user->getEmail())
                ->subject($this->translator->trans('Your new account on Sortir.com'))
                ->htmlTemplate('admin/new_user_email.html.twig')
                ->context(['user'=>$user, 'url'=>$this->router->generate('app_login',[],UrlGeneratorInterface::ABSOLUTE_URL)])
                ;

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }

            return true;
        }

        return $form;

    }


}