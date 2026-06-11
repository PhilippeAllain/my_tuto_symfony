<?php

namespace App\DTO;

class CategoryWithCountDTO
{
    public function __construct( // On utilise un constructeur pour initialiser les propriétés de la classe, 
    // readonly pour indiquer que ces propriétés ne peuvent pas être modifiées après l'instanciation de l'objet
        public readonly int $id,
        public readonly string $name,
        public readonly int $count
    ) {
    }
}