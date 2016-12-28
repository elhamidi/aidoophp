<?php

/**
 * Created by PhpStorm.
 * User: Natalie Piron
 * Date: 18/11/2016
 * Time: 16:37
 */
class NewThreadMessageFormType extends  AbstractType
{

    public function getParent()
    {

        return 'fos_message.new_thread_form.type';
    }
    public function getBlockPrefix()
    {
        return 'fos_message.new_thread_form.type';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }




}