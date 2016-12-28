<?php
    /**
     * Created by PhpStorm.
     * User: Natalie Piron
     * Date: 04/11/2016
     * Time: 12:20
     */

    namespace AnnonceBundle\Controller;



    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use AnnonceBundle\Entity\Annonce;
    use AnnonceBundle\Form\AnnonceType;
    use Symfony\Component\Validator\Constraints\Date;
    use Doctrine\ORM\Query\ResultSetMappingBuilder;
    use \PDO;

    class AnnonceController  extends Controller
    {
        /**
         * @Route("/app/annonce/add")
         *
         */
        public function addAction(Request $request)
        {
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();

            $username  = $this->getUser()
            ->getUsername()      ;
        // $session->set('user' , $username);

        $authentifiedUser = $em->getRepository('UserBundle:User')
        ->findOneByUsername($username);

        if (! $authentifiedUser->isProfilCompleted()){

            return $this->redirectToRoute('add_person');
        }
        $annonce = new Annonce();
        $annonce->setDatecreation(new \DateTime());
        $annonce->setEtat(1);
        $form   = $this->createForm(new AnnonceType(), $annonce);
        $form->handleRequest($request);
        if ($request->isMethod('POST') ){

            $annonce = $form->getData();

            $annonce->setUser($authentifiedUser);

            $em->persist($annonce);
            $em->flush();


            return $this->redirectToRoute ('annonce_view' , array ('id' => $annonce->getId()));


        }

        return $this->render('AnnonceBundle:Annonce:add.html.twig', array ('form' => $form->createView()  ));


    }

    /**
     * @param $id
     * @ROUTE ("/app/annonce/view/{id}" , name="annonce_view")
     */
    public function viewAction($id){
        $em = $this->getDoctrine()->getManager();
        $username  = $this->getUser()
            ->getUsername()      ;
        // $session->set('user' , $username);

        User:$authentifiedUser = $em->getRepository('UserBundle:User')
            ->findOneByUsername($username);

        $annonce = $em->getRepository('AnnonceBundle:Annonce')
            ->find($id);
        if (null != $annonce ){
            return $this->render ('AnnonceBundle:Annonce:view.html.twig' , array('annonce' => $annonce));
        }
    }



    /**
     * @param $id
     * @param Request $request
     * @ROUTE ("/app/annonce/edit/{id}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère person $id
        $annonce = $em->getRepository('AnnonceBundle:Annonce')->find($id);

        if (null === $annonce) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $form = $this->createForm(new AnnonceType(), $annonce);

        if ($form->handleRequest($request)->isValid()) {


            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirect($this->generateUrl('annonce_view', array('id' => $annonce->getId())));

        }


        return $this->render('AnnonceBundle:Annonce:edit.html.twig', array(
            'form'   => $form->createView(),
            'annonce' => $annonce // Je passe également l'annonce à la vue si jamais elle veut l'afficher
        ));
    }

    /**
     * @param $id
     * @param Request $request
     * @ROUTE ("/app/annonce/delete/{id}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id

        $annonce = $em->getRepository('AnnonceBundle:Annonce')->find($id);

        if (null === $annonce) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()
            ->getForm()->add('delete' , 'submit')
            ->add('cancel' , 'submit') ;

        if ($form->handleRequest($request)->isValid()) {

            if ($form->get('cancel')->isClicked()){

                return $this->redirectToRoute('home_page');

            }

            $em->remove($annonce);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

            return $this->redirect($this->generateUrl('home_page'));
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('AnnonceBundle:Annonce:delete.html.twig', array(
            'annonce' => $annonce,
            'form'   => $form->createView()
        ));
    }

        /**
         * @param $id
         * @ROUTE ("/app/annonce/annonce_user_view/{id}" , name="annonce_user_view")
         */

        public function viewUserAnnonceAction($id)
        {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            if ($user->isProfilCompleted()) {
                $annonce = $em->getRepository('AnnonceBundle:Annonce')
                    ->find($id);
                if (null != $annonce) {
                    return $this->render('AnnonceBundle:Annonce:annonce_user_view.html.twig', array('annonce' => $annonce));
                } else {
                    throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
                }
            }

            else {
                return $this->redirectToRoute('add_person');
            }
        }





    /**
     * @param $id
     * @param Request $request
     * @ROUTE ("/app/annonce/publish/{id}")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */

    public function publishAction($id , Request $request){

        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id

        $annonce = $em->getRepository('AnnonceBundle:Annonce')->find($id);

        if (null === $annonce) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()
            ->getForm()->add('delete' , 'submit')
            ->add('cancel' , 'submit') ;

        if ($form->handleRequest($request)->isValid()) {
            if ($form->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('annonce_view', array('id' => $annonce->getId())));
            }
            // on envois les notifications


            $zipCodes = $this->getListCodeByDistance($annonce->getZipCode());
            $listWommen = $this->getListWommenInCodes($zipCodes);
            // on envois les notifications

            foreach ( $listWommen as $wommen){
                $this->sendNotification($annonce->getId() , $wommen->getUser()->getEmail());
            }

            return $this->render('AnnonceBundle:Annonce:publishSuccess.html.twig', array ('listWomen' =>$listWommen, 'zipcodes' => $zipCodes ,
                'zip'=>$annonce->getZipCode( ) ));
        }

        return $this->render('AnnonceBundle:Annonce:publish.html.twig', array(
            'annonce' => $annonce,
            'form'   => $form->createView()
        ));
    }


    public function sendNotification($idAnnonce , $email){

        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('noreplay@aiddo.be')
            ->setTo($email)
            ->setBody(
                $this->renderView(
                // app/Resources/views/Annonce/notification.html.twig
                    'AnnonceBundle:Annonce:notification.html.twig',
                    array('idAnnonce' => $idAnnonce)
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

    }

// à modifier mettre dans la fonction ( table de base de donnée suivant pays ici zip_code_be , le zip code ex : 1020 , le radius en km ex : 20)
    public function getListCodeByDistance($zip){

        $connect = mysql_connect('localhost','root','') or die("Erreur de connexion au serveur.");
        mysql_select_db("aidoo", $connect) or die("Erreur de connexion à la base");
        $req = "SELECT  Lat, Lon FROM zip_code_be WHERE zip_code = '". $zip."'";
        $resultat = mysql_query($req) or die(mysql_error());
        $row = mysql_fetch_array($resultat);
        $latitude  = $row['Lat'];
        $longitude = $row['Lon'];
        $req = "SELECT   *  FROM zip_code_be ";
        $resultat = mysql_query($req) or die(mysql_error());

      while ($row = mysql_fetch_array($resultat)) {
            $latitude2 = $row['Lat'];
            $longitude2 = $row['Lon'];
           $formule = (6371*acos(cos(deg2rad($latitude))*cos(deg2rad($latitude2))*cos(deg2rad($longitude)
                        -deg2rad($longitude2))+sin(deg2rad($latitude))*sin(deg2rad($latitude2))));
          if ($formule <=20) {
              $result[] = $row['zip_code'];
          }
       }

        return $result  ;
    }


        public function getListWommenInCodes($zipcodes){
            $em = $this->getDoctrine()->getEntityManager();
            $repository = $em->getRepository('PersonBundle:Person');
            $query = $repository->createQueryBuilder('p')
                    ->addSelect('u')
                    ->innerJoin('p.user','u')
                    -> where("p.zipCode IN (:zipcodes) ")
                    ->andWhere("u.gender = 1")
                   // ->andWhere("u.profilCompleted = 1")
                    ->setParameter('zipcodes', $zipcodes);

            $listWomen  = $query->getQuery()->getResult();
            return $listWomen;
        }

}