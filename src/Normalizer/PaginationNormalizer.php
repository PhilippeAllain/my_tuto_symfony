<?php

namespace App\Normalizer;

use ArrayObject;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Entity\Recipe;


class PaginationNormalizer implements NormalizerInterface
{

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer)
    {
        
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array | string | int | float | bool | null| ArrayObject
    {
        // TODO: Implement normalize() method.
        if (!$object instanceof PaginationInterface) {
            throw new \InvalidArgumentException('The object must implement PaginationInterface.');
        }

        return [
            'items' => array_map(fn (Recipe $recipe) => $this->normalizer->normalize($recipe, $format, $context), $object->getItems()),
            'pagination' => [
                'total' => $object->getTotalItemCount(),
                'page' => $object->getCurrentPageNumber(),
                'lastPage' => ceil($object->getTotalItemCount() / $object->getItemNumberPerPage()),
            ],
        ];
    }


    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof PaginationInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        // TODO: Implement getSupportedTypes() method.
        return [
            PaginationInterface::class => true,
        ];
    }
}
