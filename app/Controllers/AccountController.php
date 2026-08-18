<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Models\User;
use Ecommerce\Shop\Core\Session;
use Ecommerce\Shop\Core\Logger;
use Ecommerce\Shop\Core\Database;


class AccountController extends Controller
{
    private User $userModel;


    public function __construct($db)
    {
        parent::__construct($db);

        $this->userModel = new User();
    }


    public function showLogin(): void
    {
        echo $this->twig->render('account/login.html.twig');
    }



    public function login(): void
    {
        $email = $_POST['email'];
        $lozinka = $_POST['lozinka'];


        $user = $this->userModel->authenticate($email, $lozinka);
       
        



        if (!$user) {

            Session::setFlash(
                'error',
                'Pogrešan email ili lozinka'
            );


            $this->redirect('/login');
            return;

        }


        Session::set('user_id', $user['korisnik_id']);
        Session::set('ime', $user['ime']);
        Session::set('uloga', strtoupper($user['uloga']));



        Logger::info('Uspešna prijava', [

            'email' => $_POST['email'],
            'ip' => $_SERVER['REMOTE_ADDR']

        ]);




    

        if (Session::get('uloga') === 'ADMIN') {


            $this->redirect('/admin');
            return;


        }





     

        if (Session::get('uloga') === 'KUPAC') {


            $this->redirect('/account');
            return;

            

        }





 
        Session::destroy();

        Session::setFlash(
            'error',
            'Nemate dozvolu za pristup'
        );


        $this->redirect('/login');
        return;

    }





    public function showRegister(): void
    {
        echo $this->twig->render('account/register.html.twig');
    }





    public function updatePassword(): void
    {
        echo $this->twig->render('account/updatepassword.html.twig');
    }





    public function changePassword(): void
    {

        $email = $_POST['email'];
        $lozinka = $_POST['lozinka'];



        $this->userModel->changePassword(
            $lozinka,
            $email
        );



        Session::setFlash(
            'success',
            'Lozinka je uspešno promenjena.'
        );



        $this->redirect('/login');

    }





    public function register(): void
    {


        $data = [

            'ime' => $_POST['ime'],
            'prezime' => $_POST['prezime'],
            'email' => $_POST['email'],
            'lozinka' => $_POST['lozinka'],
            'telefon' => $_POST['telefon'],
            'adresa' => $_POST['adresa'],
            'grad' => $_POST['grad'],
            'postanski_broj' => $_POST['postanski_broj'],
            'uloga' => 'KUPAC'

        ];




        $exists = $this->userModel->findByEmail(
            $data['email']
        );




        if ($exists) {


            Session::setFlash(
                'error',
                'Email već postoji'
            );


            $this->redirect('/registracija');
            return;

        }




        $this->userModel->createUser($data);




        Session::setFlash(
            'success',
            'Uspešna registracija'
        );




        $this->redirect('/login');

    }






    // KUPAC DASHBOARD

    public function account(): void
    {


        if (!Session::get('user_id')) {


            $this->redirect('/login');
            return;


        }

        



        if (Session::get('uloga') !== 'KUPAC') {


            $this->redirect('/login');
            return;


        }

        $db=Database::getInstance()->getConnection();
        $korisnik=Session::get('user_id');

        $stmt=$db->prepare("
        
        SELECT 
        porudzbina_id,
        ukupna_cena,
        status
        FROM porudzbine 
        WHERE korisnik_id=?
        ORDER BY ukupna_cena DESC
        LIMIT 1
        
        
    "
    );
    $stmt->execute([$korisnik]);
    $porudzbine=$stmt->fetchAll();
    echo $this->twig->render(
            'account/dashboard.html.twig',[

            'porudzbine'=>$porudzbine
            ]
        );

    }

 
  public function removeFromAccount($id)
{

    if(!Session::get('user_id')){

        header("Location: /ecommerce-shop/public/login");
        exit;

    }


    $db = Database::getInstance()->getConnection();


    $korisnik = Session::get('user_id');


    $stmt = $db->prepare("
        DELETE FROM porudzbine
        WHERE porudzbina_id=?
        AND korisnik_id=?
    ");


    $stmt->execute([
        $id,
        $korisnik
    ]);


    header("Location: /ecommerce-shop/public/account/orders");
    exit;

}

 public function logout(): void
    {

        Session::destroy();


        $this->redirect('/login');

    }
    public function Myprofile(){
        
        if(!Session::get('user_id')){
            header("Location: /ecommerce-shop/public/login");
            exit;
        }

        $db=Database::getInstance()->getConnection();

        $id=Session::get('user_id');

        $stmt=$db->prepare("
        SELECT * FROM korisnici WHERE korisnik_id=?
        ");

        $stmt->execute([$id]);
        $korisnik=$stmt->fetch();

        echo $this->twig->render('admin/profile.html.twig',[
        'korisnik'=>$korisnik,

        ]);

    }

     public function addresses(){
        
        if(!Session::get('user_id')){
            header("Location: /ecommerce-shop/public/login");
            exit;
        }

        $db=Database::getInstance()->getConnection();

        $id=Session::get('user_id');

        $stmt=$db->prepare("
        SELECT 
        telefon,
        grad,
        adresa,
        postanski_broj
        FROM korisnici
        WHERE korisnik_id=?

        ");

        $stmt->execute([$id]);
        $korisnik=$stmt->fetch();

        echo $this->twig->render('admin/addresses.html.twig',[
        'adresa'=>$korisnik,

        ]);

    }

}

   