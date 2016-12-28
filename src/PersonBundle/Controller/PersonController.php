<?php
/**
 * Created by PhpStorm.
 * User: Natalie Piron
 * Date: 28/10/2016
 * Time: 14:19
 */

namespace PersonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PersonBundle\Entity\Person;
use PersonBundle\Form\PersonType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonController extends Controller
{


    /**
     * @ROUTE ("/app/person/add" , name="add_person")
     */
    public function addPerson (Request $request){

        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $username  = $this->getUser()
            ->getUsername()      ;
        // $session->set('user' , $username);

        User:$authentifiedUser = $em->getRepository('UserBundle:User')
            ->findOneByUsername($username);

        if ($authentifiedUser->isProfilCompleted()){

            return $this->render('PersonBundle:Person:profilCompleted.html.twig');
        }

        $person = new Person();

        $form   = $this->createForm(new PersonType(), $person);
      //  $person->setUser($user);
        $form->handleRequest($request);
        if ($request->isMethod('POST') ){
            // on ajoute person a la base
            $person = $form->getData();

            $file = $person->getImage();

            if (null != $file){
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                $person->setImage($fileName);
            }

            else {$person->setImage("nopic.jpg");}

            $authentifiedUser->setprofilCompleted(true);

            $person->setUser($authentifiedUser);
            $em->persist($person);
            $em->flush();
            return $this->redirectToRoute('person_view', array ('id' => $person->getId()) );

        }

        // sinon je retourne le formulaire
        return $this->render('PersonBundle:Person:add.html.twig', array ('form' => $form->createView()  ));
    }


    /**
     * @param $id
     * @ROUTE ("/app/person/view/{id}" , name="person_view")
     */
    public function viewPerson($id){
        $em = $this->getDoctrine()->getManager();
        $username  = $this->getUser()
                          ->getUsername()      ;
        // $session->set('user' , $username);

        User:$authentifiedUser = $em->getRepository('UserBundle:User')
            ->findOneByUsername($username);

        $person = $em->getRepository('PersonBundle:Person')
                     ->find($id);
        if (null != $person && (!strcmp($authentifiedUser->getUsername() , $person->getUser()->getUsername()))){
            return $this->render ('PersonBundle:Person:view.html.twig' , array('person' => $person));
        }

        else
            return $this->render('PersonBundle:Person:profilError.html.twig' , array ('user' => $username , 'person' => $person));
    }


    /**
     * @param $id
     * @param Request $request
     * @ROUTE ("/app/person/edit/{id}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function editAction($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $username  = $this->getUser()
            ->getUsername()      ;
        // $session->set('user' , $username);

        User:$authentifiedUser = $em->getRepository('UserBundle:User')
        ->findOneByUsername($username);

        // On récupère person $id
        $person = $em->getRepository('PersonBundle:Person')->find($id);

        if (null === $person  ) {
            throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        if (strcmp($authentifiedUser->getUsername() , $person->getUser()->getUsername())){
            throw new NotFoundHttpException("Vous n'avez pas le droit de modifier ce profil");
        }

        $form = $this->createForm(new PersonType(), $person);

        if ($form->handleRequest($request)->isValid()) {

            $file = $person->getImage();

            if (null != $file){
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                $person->setImage($fileName);
            }

            else {$person->setImage("nopic.jpg");}
            // Inutile de persister ici, Doctrine connait déjà notre person
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Profil bien modifié.');

            return $this->redirect($this->generateUrl('person_view', array('id' => $person->getId())));
        }

        return $this->render('PersonBundle:Person:edit.html.twig', array(
            'form'   => $form->createView(),
            'person' => $person // Je passe également la personne à la vue si jamais elle veut l'afficher
        ));
    }

    /**
     * @param $id
     * @ROUTE ("/app/delete/{id}" , requirements ={"/+d"})
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $person = $em->getRepository('PersonBundle:Person')->find($id);

        if (null === $person) {
            throw new NotFoundHttpException("La personne d'id ".$id." n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()
                    ->getForm()->add('delete' , 'submit')
                               ->add('cancel' , 'submit') ;

        if ($form->handleRequest($request)->isValid()) {
            $em->remove($person);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

            return $this->redirect($this->generateUrl('home_page'));
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('PersonBundle:Person:delete.html.twig', array(
            'person' => $person,
            'form'   => $form->createView()
        ));
    }



}