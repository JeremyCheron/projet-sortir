<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class UploadController extends AbstractController
{

    public function __construct(private  TranslatorInterface $translator)
    {
    }

    #[Route('/upload', name: 'app_upload')]
    #[IsGranted('ROLE_USER')]
    public function upload(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createFormBuilder()
            ->add('image',FileType::class)
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $imageFile = $form->get('image')->getData();
            $uploadDirectory = $this->getParameter('uploads_directory');
            $imageFile->move($uploadDirectory,$imageFile->getClientOriginalName());

            $activeUser = $this->getUser();

            if ($activeUser instanceof User) {

                $activeUser->setProfilePic($imageFile->getClientOriginalName());
                $em->flush();

            }

            $this->addFlash('success',
                $this->translator->trans(
                    'flashmessage.uploadpp'
                ));
            return $this->redirectToRoute('user_modify_profile');
        }

        return $this->render('upload/upload.html.twig', ['form'=>$form->createView()]);
    }
}
