<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\AdminEditUserForm;
use App\Form\ChangePasswordForm;
use App\Form\ResetPasswordForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{

    public function changePassword(Request $request, UserPasswordEncoderInterface $passwordEncoder){

        /**
         * @var UserInterface $user
         */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordForm::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $newPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData()
            );

            $em = $this->getDoctrine()->getManager()->getRepository(User::class)->upgradePassword($user, $newPassword);

            return $this->redirectToRoute('index');
        }

        return $this->render('user/changePassword.html.twig',[
           "form" => $form->createView()
        ]);
    }

    public function resetPassword( UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        //LOGIN: znalezione.up@interia.pl HASŁO: Znalezione998

        /**
         * @var UserInterface $user
         */

        $form = $this->createForm(ResetPasswordForm::class);
        $form->handleRequest($request);

        if($form->isSubmitted()) {

            $email = $form->getData()->getEmail();

            $userRep = $this->getDoctrine()->getRepository(User::class);
            $user = $userRep->findOneBy(['email' => $email]);

            if(!$user) {

                return $this->render('user/resetPasswordError.html.twig');
            }

            $newPassword = $this->getDoctrine()->getRepository(User::class)->passwordGenerator();

            $hashedPassword = $passwordEncoder->encodePassword(
                    $user,
                    $newPassword
            );

            $userRep->upgradePassword($user, $hashedPassword);

            $transport = (new \Swift_SmtpTransport('poczta.interia.pl',587))
                    ->setUsername('znalezione.up@interia.pl')
                    ->setPassword('Znalezione998')
            ;

            $mailer = new \Swift_Mailer($transport);

            $message =( new \Swift_Message('Reset Password'))
                    ->setFrom('znalezione.up@interia.pl')
                    ->setTo($email)
                    ->setBody($this->renderView('user/resetPasswordEmail.txt.twig',[
                     "email" => $email,
                     "password" => $newPassword
                    ]));

            $mailer->send($message);

            return $this->redirectToRoute('login');

        }
        return $this->render('user/resetPassword.html.twig',[
            "form" => $form ->createView()
        ]);
    }


}