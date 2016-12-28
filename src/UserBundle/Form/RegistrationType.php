<?php
/**
 * Created by PhpStorm.
 * User: Natalie Piron
 * Date: 26/10/2016
 * Time: 11:06
 */

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pseudo');
        $builder->add('gender', 'choice', array(
            'choices'   => array('0' => 'Male', '1' => 'Female'),
            'required'  => false,
        ));

    }

    public function getParent()
    {
       // return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
         return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}