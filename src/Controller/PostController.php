<?php


namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\AddPostForm;
use App\Form\EditPostForm;
use App\Form\SearchForm;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class PostController extends AbstractController
{
    public function addPost(Request $request){

        /**
         * @var Post $post
         */
        $post = new Post();
        $form = $this->createForm(AddPostForm::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $post->setUserId($this->getUser()->getId());
            $post = $form->getData();
            $post->setCreateDate(new \DateTime('now'));
            $post->setStatus(1);

            /**
             * @var UploadedFile $file
             */
            $file = $form['file']->getData();

            if($file){

                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $fileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$file->guessExtension();

                try{
                    $file ->move(
                        $this->getParameter('files_directory'),
                        $newFileName
                    );

                }catch(FileException $e) {

                    echo "Błąd zapisu pliku!!";
                }

                $post->setFileName($newFileName);
            }

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('index');
        }

       return $this->render('post/addPost.html.twig',[
            'form' => $form->createView()
        ]);
    }

    public function allPosts(){

        $posts = $this->getDoctrine()->getManager()->getRepository(Post::class)->findAllActive();

        return $this->render('post/allPosts.html.twig',[
            'posts' => $posts
        ]);
    }

    public function userPosts(){

        $userId = $this->getUser()->getId();
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAllUserPosts($userId);

        return $this->render('post/myPosts.html.twig',[
           "posts" => $posts
        ]);
    }


    public function deactivatePost($postId){

        $post = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['id' => $postId]);

        if(!$post){

            return new \Exception('Brak użytkownika o podanym ID!');
        }
        $post->setStatus(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute('post_all');

    }

    public function editPost(Request $request, $postId){

        $post = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['id' => $postId]);
        $form = $this->createForm(EditPostForm::class,$post);
        $form->handleRequest($request);

        if(!$post){
            return new \Exception("Brak postu o podanym ID!");
        }

        if($form->isSubmitted() && $form->isValid()){

            $post = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_all');
        }

       return $this->render('/post/editPost.html.twig',[
           "form" =>$form ->createView(),
       ]);
    }

    public function search(Request $request){

        $keywords = $request->query->get('_keywords');
        $posts = $this->getDoctrine()->getRepository(Post::class)->search($keywords);

        return $this->render('search/search.html.twig',[
                "posts" => $posts,
                "keywords" => $keywords
        ]);

    }

}