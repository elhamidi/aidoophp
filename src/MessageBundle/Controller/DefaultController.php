<?php

namespace MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\MessageBundle\FormType\NewThreadMessageFormType;
use Symfony\Component\Form\FormBuilderInterface;

use MessageBundle\Model\NewThreadMessageFormFactory;

class DefaultController extends Controller
{
    /**
     * @Route("/mege")
     * @Template()
     */
    public function indexAction($name)
    {
        $provider = $this->get('fos_message.provider');

        $threads = $provider->getInboxThreads();

        return $this->render('MessageBundle:message:inbox.html.twig', ['threads' => $threads]);
    }


    /**
     * @Route("/message/start")
     * @Template()
     */
    public function startAction($name)
    {

        $form = $this->container->get('fos_message.new_thread_form.factory')->create();
        $formHandler = $this->container->get('fos_message.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('fos_message_thread_view', array(
                'threadId' => $message->getThread()->getId(),
            )));
        }

        return $this->container->get('templating')->renderResponse('FOSMessageBundle:Message:newThread.html.twig', array(
            'form' => $form->createView(),
            'data' => $form->getData(),
        ));

        return $this->render('MessageBundle:message:index.html.twig', ['form' => $form->createView() ]);
    }
}
