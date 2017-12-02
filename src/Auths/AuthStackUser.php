<?php
namespace AuthStack\Auths;

class AuthStackUser implements  IUser{
    public $sub;
    public $name;
    public $given_name;
    public $family_name;
    public $middle_name;
    public $nickname;
    public $preferred_username;
    public $profile;
    public $picture;
    public $email;
    public $email_verified;
    public $gender;
    public $birthdate;
    public $zoneinfo;
    public $locale;
    public $phone_number;
    public $phone_number_verified;
    public $address;
    public $updated_at;

    public $username;
    public $idp;
    public $roles;
    public $groups;
}