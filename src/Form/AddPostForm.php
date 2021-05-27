<?php


namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class AddPostForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class,[
                "label" => false
            ])
            ->add('description', TextareaType::class,[
                "label" => false,
                "required" => false
            ])
            ->add('placeOfFound', TextareaType::class,[
                "label" => false
            ])
            ->add('placeOfPick',TextType::class, [
                "label" => false
            ])
            ->add('file', FileType::class,[

                'mapped' => false,
                'required' => false,
                "label" => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ]
                    ])
                ]
            ])

            ->add('Dodaj',SubmitType::class)
            ->add('Reset', ResetType::class)
            ->getForm()
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class
        ]);
    }

}