<?php

namespace Interfaces;

use Entities\User;
use XUA\InterfaceEve;

class TestInterface extends InterfaceEve
{
    public static function execute(): string
    {
        echo "<pre>";
        echo \Entities\User::alter();
        echo \Entities\Farm::alter();
        echo "</pre>";

//        $farm = new Farm(4);
//        var_dump($farm);
//        $farm->delete();

        $users = User::getMany(
//            Condition::leaf(User::CF(User::workingFarms)->rel(Farm::F(Farm::title)), Condition::LIKE, '%قارچ%')
        );
        var_dump($users);

//    $user = new \Entities\User();
//    $user->personType = 'juridical';
//    $user->firstNameEn = 'Abbas';
//    $user->lastNameEn = 'Ghaderi';
//    $user->firstNameFa = 'عباس';
//    $user->lastNameFa = 'قادری';
//    $user->nationalCode = '0123456789';
//    $user->cellphoneNumber = '09123456789';
//    $user->email = 'a@b.c';
//    $user->address = 'nah nah';
//
//    $simCard = new \Entities\SimCard();
//    $simCard->code = 'aaabbbcccds';
//    $simCard->owner = $user;
//    $simCard->store();
//    $user->simCard = $simCard;
//
//    $user->store();

//    $users = \Entities\User::getMany(\XUA\Tools\Condition::trueLeaf() , null, null);
//    var_dump($users);
//    $farm = new \Entities\Farm(1);
//    $farm->workers = $users;
//    $farm->delete();
//    var_dump($simCard->lastOwners);
//    $farm = new \Entities\Farm(2);
//    $farm
//        ->markToDelete()
//        ->store();

//    var_dump($farm->workers[1]);
//    var_dump($user->personType);
//    var_dump($user);
//    $t = $user->simCard->lastOwners[1]->firstName;
//    var_dump($t);
//    var_dump($user);
//    $user->simCard->owner->firstName = 'تست';
//    var_dump($user->simCard);
//    var_dump($user->simCard);
//    var_dump($user);
//    $simCard = new \Entities\SimCard();
//    $simCard->owner = $user;
//    $simCard->code = 'ABCDEFGHIJ';
//    $simCard->store();
//    var_dump($user->simCard);

//    $user = \Entities\User::getOne(\XUA\Tools\Condition::trueLeaf());
//    var_dump($user);
        //
        //$user->posts = [new Entities\Post()];
        //
        //$user->posts[0]->style = 'something';
        //
        //$user->store();

//    echo Entities\User::alter();

        //$user = new Entities\User();
        //$user->firstName = "Madjid";
        //$user->lastName = "Mirzavaziri";
        //$user->store();
        //var_dump($user);

        //$user->firstName = 'Kamyar';
        //$user->lastName = 'Mirzavaziri';
        //$user->store();
        //var_dump($type->values);
        //Entity::structure['gender']->values

        //$type = new \Supers\Basics\Strings\Enum(['values' => ['male', 'female'], 'nullable' => true]);
        //
        //
        //$input = null;
        //var_dump($type->accepts($input, $message), $message);
        //var_dump($input);
        //var_dump($type);
        //var_dump($type->databaseType());

        //$type = new Supers\Integers\NaturalUpperLimit(['end' => 30]);
        //
        //var_dump($type);
        //
        //$type = new Supers\Integers\EfficientRange(['start' => 10, 'end' => 30]);
        //
        //var_dump($type);
        //
        //$value = 25;
        //var_dump($type->explicitlyAccepts($value, $message)); # dumps true
        //$value = 5;
        //var_dump($type->implicitlyAccepts($value)); # dumps true
        //
        //$value = 25;
        //var_dump($type->implicitlyAccepts($value)); # dumps true
        //
        //$value = 15;
        //var_dump($type->accepts($value)); # dumps true
        //var_dump($value); # dumps 15
        //
        //$value = 5;
        //var_dump($type->accepts($value)); # dumps true
        //var_dump($value); # dumps 15
        //
        //$value = 5;
        //var_dump($type->explicitlyAccepts($value, $reason)); # dumps false
        //var_dump($reason); # dumps '5 is less than 10'
        //
        //$value = 30;
        //var_dump($type->explicitlyAccepts($value, $reason)); # dumps false
        //var_dump($reason); # dumps '30 is not less than 30'
        //
        //$value = 30;
        //var_dump($type->accepts($value, $reasons)); # dumps false
        //var_dump($reasons);
        //
        //$value = 5;
        //var_dump($type->accepts($value, $reasons)); # dumps false
        //var_dump($reasons);
        //
        //var_dump($type->DatabaseType()); # Dumps 'INT(6)'
        return '';
    }
}