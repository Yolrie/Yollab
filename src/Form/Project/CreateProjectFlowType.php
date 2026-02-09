<?php

namespace App\Form\Project;

use Symfony\Component\Form\Extension\Core\Type\Flow\AbstractFlowType;
use Symfony\Component\Form\Extension\Core\Type\Flow\FlowBuilderInterface;

class CreateProjectFlowType extends AbstractFlowType
{
    public function buildFormFlow(FlowBuilderInterface $builder): void
    {
        $builder
            ->addStep('general', GeneralStepType::class, [
                'label' => 'Infos générales',
            ])
            ->addStep('settings', SettingsStepType::class, [
                'label' => 'Paramètres',
            ])
            ->addStep('confirmation', ConfirmationStepType::class, [
                'label' => 'Confirmation',
            ])
        ;
    }
}
