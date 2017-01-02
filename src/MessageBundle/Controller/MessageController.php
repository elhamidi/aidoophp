<?php

namespace MessageBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\MessageBundle\Provider\ProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Component\Form\Form;
use Doctrine\Common\Collections\ArrayCollection;

class MessageController extends Controller implements ContainerAwareInterface
{
    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Displays the authenticated participant inbox.
     * @ROUTE("/message" ,  name= "message_inbox" )
     * @return Response
     */
    public function inboxAction()
    {
        $threads = $this->getProvider()->getInboxThreads();

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:inbox.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays the authenticated participant messages sent.
     * @ROUTE("/message/sent" ,  name= "message_sent" )
     * @return Response
     */
    public function sentAction()
    {
        $threads = $this->getProvider()->getSentThreads();

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:sent.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays the authenticated participant deleted threads.
     * @ROUTE("/message/deleted" ,  name= "message_deleted" )
     * @return Response
     */
    public function deletedAction()
    {
        $threads = $this->getProvider()->getDeletedThreads();

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:deleted.html.twig', array(
            'threads' => $threads,
        ));
    }

    /**
     * Displays a thread, also allows to reply to it.
     *
     * @param string $threadId the thread id
     * @Route ("/message/threadview/{threadId}" , name="message_thread_view")
     * @return Response
     */
    public function threadAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $form = $this->container->get('fos_message.reply_form.factory')->create($thread);
        $formHandler = $this->container->get('fos_message.reply_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('message_thread_view', array(
                'threadId' => $message->getThread()->getId(),
            )));
        }

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:thread.html.twig', array(
            'form' => $form->createView(),
            'thread' => $thread,
        ));
    }

    /**
     * Create a new message thread.
     * @ROUTE("/message/new" ,  name= "message_thread_new" )
     * @return Response
     */
    public function newThreadAction()
    {
        // $form = $this->container->get('fos_message.new_thread_form.factory')->create();

        $id = $_POST["id"];
        $destinataire = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository('UserBundle:User')
            ->findOneById($id);;
        $form = $this->getFormForNewThreadAction($destinataire);

        $formHandler = $this->container->get('fos_message.new_thread_form.handler');


        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('message_thread_view', array(
                'threadId' => $message->getThread()->getId(),
            )));
        }

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:newThread.html.twig', array(
            'form' => $form->createView(),
            'data' => $form->getData(), 'id' => $id,
        ));
    }

    /**
     * Deletes a thread.
     * @ROUTE("/message/new" ,  name= "message_thread_delete" )
     * @param string $threadId the thread id
     *
     * @return RedirectResponse
     */
    public function deleteAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->container->get('fos_message.deleter')->markAsDeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
    }

    /**
     * Undeletes a thread.
     *
     * @param string $threadId
     *
     * @return RedirectResponse
     */
    public function undeleteAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->container->get('fos_message.deleter')->markAsUndeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);

        return new RedirectResponse($this->container->get('router')->generate('fos_message_inbox'));
    }

    /**
     * Searches for messages in the inbox and sentbox.
     *
     * @return Response
     */
    public function searchAction()
    {
        $query = $this->container->get('fos_message.search_query_factory')->createFromRequest();
        $threads = $this->container->get('fos_message.search_finder')->find($query);

        return $this->container->get('templating')->renderResponse('MessageBundle:Message:search.html.twig', array(
            'query' => $query,
            'threads' => $threads,
        ));
    }

    /**
     * Gets the provider service.
     *
     * @return ProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('fos_message.provider');
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    /**
     * @param ParticipantInterface $account
     * @return \FOS\MessageBundle\FormFactory\Form|FormInterface
     */
    protected function getFormForNewThreadAction(ParticipantInterface $account)
    {
        //$form = $this->getNewThreadFormFactory()->create();
        $form = $this->container->get('fos_message.new_thread_form.factory')->create();
        $preSetFormData = $this->getPreSetFormData($account, $form);
        $form->setData($preSetFormData);

        return $form;
    }

    /**
     * @param ParticipantInterface $account
     * @param Form $form
     * @return NewThreadMessage
     */
    protected function getPreSetFormData(ParticipantInterface $account, Form $form)
    {
        $recipients = new ArrayCollection();
        $recipients->add($account);

        /** @var NewThreadMessage $newThreadMessage */
        $newThreadMessage = $form->getData();
        $newThreadMessage->setRecipient($account);

        return $newThreadMessage;
    }

    /**
     * @param $threadId
     * @return Response
     * @ROUTE ("/message/thread/refresh/" , name = "message_thread_refresh")
     */

    public function refreshThreadMessagesAction()
    {
        $threadId =  $_GET['threadId'];

        $thread = $this->getProvider()->getThread($threadId);

        return $this->render('MessageBundle:Message:refreshThread.html.twig' , array('thread' => $thread));

       }
}


