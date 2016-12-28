<?php
/**
 * Created by PhpStorm.
 * User: Natalie Piron
 * Date: 26/10/2016
 * Time: 11:41
 */

namespace UserBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PersonBundle\Form\PersonType;
use PersonBundle\Entity\Person;

class ProfilController extends Controller
{

    /**
     * @ROUTE ("/UserProfil/{id}",
     * requirements={"id":"\d+"})
     */
    public function showProfil ($id){


        return $this->render('FOSUserBundle:Profil:show.html.twig');
    }


    /**
     * @ROUTE ("/UserProfil/addProfile")
     */
    public function addProfil (Request $request){

        $person = new Person();
        $form   = $this->createForm(new PersonType(), $person);
        $form->handleRequest($request);


        if ($request->isMethod('POST') ){

            // on ajoute person a la base

            return $this->render('FOSUserBundle:Profil:show.html.twig');

        }

        // sinon je retourne le formulaire



        return $this->render('FOSUserBundle:Profil:add.html.twig', array ('form' => $form->createView()));


    }


    /**
     * @ROUTE ("/UserProfil/editProfile/{id}")
     */
    public function editProfil ($id){


        return $this->render('UserBundle:Profil:add.html.twig');

    }

    /**
     * @ROUTE ("/UserProfil/deleteProfile/{id}")
     */
    public function deleteProfil ($id){


        return $this->render('UserBundle:Profil:add.html.twig');

    }




}