<?php
/**
 * Created by PhpStorm.
 * User: Natalie Piron
 * Date: 19/11/2016
 * Time: 11:35
 */

namespace MessageBundle\Model;

use Symfony\Component\Form\FormInterface;

/**
 * Instanciates message forms.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormFactory extends AbstractMessageFormFactory
{
    /**
     * Creates a new thread message.
     *
     * @return FormInterface
     */
    public function create()
    {
        return $this->formFactory->createNamed($this->formName, $this->formType, $this->createModelInstance());
    }
}