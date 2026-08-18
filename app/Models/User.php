<?php

namespace Ecommerce\Shop\Models;

use Ecommerce\Shop\Core\Model;

class User extends Model
{
    protected string $table = 'korisnici';


    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM korisnici WHERE email = :email LIMIT 1"
        );

        $stmt->execute([
            'email' => $email
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }


    public function authenticate(string $email, string $lozinka): ?array
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (!password_verify($lozinka, $user['lozinka'])) {
            return null;
        }

        return $user;
    }


    public function createUser(array $data): int
    {
        $data['lozinka'] = password_hash(
            $data['lozinka'],
            PASSWORD_BCRYPT
        );

        return $this->create($data);
    }


    public function findAdmins(): array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM korisnici
             WHERE uloga = 'ADMIN'
             ORDER BY datum_registracije DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function findCustomers(): array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM korisnici
             WHERE uloga = 'KUPAC'
             ORDER BY datum_registracije DESC"
        );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function changePassword(string $novalozinka,string $email) :bool{
        $stmt=$this->db->prepare(
            "SELECT korisnik_id FROM korisnici WHERE email=:email LIMIT 1"
        );
        $stmt->execute([
            'email'=>$email
        ]);

        $user=$stmt->fetch();

        if(!$user){
            return false;
        }

        $hash=password_hash(
            $novalozinka,
            PASSWORD_BCRYPT
        );

        $stmt= $this->db->prepare(
            "UPDATE korisnici SET lozinka = :lozinka WHERE korisnik_id= :id"
        );

        return $stmt->execute([
            'lozinka'=> $hash,
             'id'=>$user['korisnik_id']
        ]);

    }


    }
