<?php


namespace App\Controller;


use App\Entity\Post;
use App\Entity\User;
use App\Form\AdminEditUserForm;
use App\Repository\UserRepository;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package App\Controller
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 */
class AdminController extends AbstractController
{
    public function adminDashboard()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    }

    public function listOfUsers(){

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        if($users)
        {
            return $this->render('admin/listOfUsers.html.twig',[
                'users' => $users
            ]);
        }
    }

    public function listOfPosts(){

        $posts = $this->getDoctrine()->getRepository(Post::class)->findAllPostsWithUsers();
        if($posts){

            return $this->render('admin/listOfPosts.html.twig',[
                "posts" => $posts,
            ]);
        }
    }

    public function editAccount(Request $request, $userId){

        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        if(!$user) {

           return new \Exception("Brak uzytkownika o podanym ID");
        }

        $form = $this->createForm(AdminEditUserForm::class, $user);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $firstName = $form->getData()->getFirstName();
            $lastName = $form->getData()->getLastName();
            $user->setUsername($firstName.' '.$lastName);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_list_of_users');
        }

        return $this->render('admin/editUser.html.twig',[
            "form" => $form->createView()
        ]);
    }

    public function deleteAccount($userId){

        $user= $this->getDoctrine()->getRepository(User::class)->find($userId);

        if(!$user) {

            return new \Exception("Brak użytkownika o podanym ID");
        }

        $em =$this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_list_of_users');
    }


    public function promoToAdmin($userId){

        $user= $this->getDoctrine()->getRepository(User::class)->find($userId);

        if(!$user) {

            return new \Exception("Brak użytkownika o podanym ID");
        }

        $em =$this->getDoctrine()->getManager();
        $user->setRoles(array('ROLE_ADMIN'));
        $em->persist($user);
        $em->flush();


        return $this->redirectToRoute('admin_list_of_users');
    }

}