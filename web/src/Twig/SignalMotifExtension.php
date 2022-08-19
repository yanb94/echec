<?php

namespace App\Twig;

use App\Entity\Signal;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SignalMotifExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('signal_motif', [$this, 'getLabelOfMotif']),
        ];
    }

    public function getLabelOfMotif(string $motif)
    {
        foreach (Signal::motifList() as $key => $value) {
            if ($value == $motif) {
                return $key;
            }
        }
    }
}
