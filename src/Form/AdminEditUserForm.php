<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class AdminEditUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class,[
                'label' => false

            ])
            ->add('lastName', TextType::class,[
                'label' => false,
            ])
            ->add('email', EmailType::class,[
                'label' => false,
                'constraints' => [
                    new Regex('/^[a-zA-Z0-9.\-_]+@([a-zA-Z0-9\-.])+up.krakow.pl|up.krakow.pl$/'),
                ],
                'invalid_message' => "Email musi być z domeny UP",
                'invalid_message_parameters' => "Email musi być z domeny UP"
            ])
            ->add('Zapisz', SubmitType::class)
            ->getForm()
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}