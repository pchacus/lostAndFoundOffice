<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class,[

                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Proszę podać hasło'
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => "Hasło powinno składać się z conajmniej 6 liczb"
                    ])
                ]
            ])
            ->add('Zapisz', SubmitType::class)
            ->getForm()
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
       $resolver->setDefaults([
           "data_class" => User::class
       ]);
    }


}