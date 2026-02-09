<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ProjectData
{
    // === STEP 1 : Infos générales ===

    #[Assert\NotBlank(groups: ['general'])]
    #[Assert\Length(min: 3, max: 100, groups: ['general'])]
    public ?string $name = null;

    #[Assert\NotBlank(groups: ['general'])]
    #[Assert\Length(min: 10, max: 500, groups: ['general'])]
    public ?string $description = null;

    // === STEP 2 : Paramètres ===

    #[Assert\NotBlank(groups: ['settings'])]
    #[Assert\Choice(choices: ['public', 'private', 'team'], groups: ['settings'])]
    public ?string $visibility = null;

    #[Assert\NotBlank(groups: ['settings'])]
    #[Assert\Choice(choices: ['low', 'medium', 'high'], groups: ['settings'])]
    public ?string $priority = null;

    // === STEP 3 : Confirmation ===

    #[Assert\IsTrue(message: 'Vous devez accepter les conditions.', groups: ['confirmation'])]
    public bool $termsAccepted = false;
}
