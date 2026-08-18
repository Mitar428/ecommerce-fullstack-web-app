<?php

namespace Ecommerce\Shop\Controllers;

use Ecommerce\Shop\Core\Controller;
use Ecommerce\Shop\Core\Database;
use Ecommerce\Shop\Core\Session;

class WishlistController extends Controller
{

    public function add($id)
    {
        if (!Session::get('user_id')) {
            header("Location: /ecommerce-shop/public/login");
            exit;
        }

        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT * FROM wishlist
            WHERE korisnik_id = ? AND proizvod_id = ?
        ");

        $stmt->execute([
            Session::get('user_id'),
            $id
        ]);

        if (!$stmt->fetch()) {

            $stmt = $db->prepare("
                INSERT INTO wishlist (korisnik_id, proizvod_id)
                VALUES (?, ?)
            ");

            $stmt->execute([
                Session::get('user_id'),
                $id
            ]);
        }

        header("Location: /ecommerce-shop/public/favoriti");
        exit;
    }


    public function index()
    {
        if (!Session::get('user_id')) {
            header("Location: /ecommerce-shop/public/login");
            exit;
        }

        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT
                w.wishlist_id,
                p.proizvod_id,
                p.naziv,
                p.cena,
                p.slika
            FROM wishlist w
            JOIN proizvodi p
                ON w.proizvod_id = p.proizvod_id
            WHERE w.korisnik_id = ?
        ");

        $stmt->execute([
            Session::get('user_id')
        ]);

        $items = $stmt->fetchAll();

        echo $this->twig->render(
            'wishlist/index.html.twig',
            [
                'items' => $items
            ]
        );
    }


    public function remove($id)
    {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            DELETE FROM wishlist
            WHERE wishlist_id = ?
            AND korisnik_id = ?
        ");

        $stmt->execute([
            $id,
            Session::get('user_id')
        ]);

        header("Location: /ecommerce-shop/public/favoriti");
        exit;
    }

}