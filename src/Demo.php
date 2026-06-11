<?php

namespace App;

use Symfony\Component\Validator\Validator\ValidatorInterface; // Import du ValidatorInterface pour l'injection de dépendance

class Demo
{
    public function __construct(private ValidatorInterface $validator, private string $key) { // Injection de dépendance du ValidatorInterface
        // Vous pouvez utiliser le $validator ici pour valider des données ou effectuer d'autres opérations
        // Par exemple, vous pourriez valider un objet ou une valeur spécifique
        // $errors = $validator->validate($someObject);
    }
}
