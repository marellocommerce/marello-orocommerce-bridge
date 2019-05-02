<?php

namespace Marello\Bundle\TaxBundle\Form\DataTransformer;

use Marello\Bundle\TaxBundle\Entity\ZipCode;
use Symfony\Component\Form\DataTransformerInterface;

class ZipCodeTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     * @param ZipCode $zipCode
     */
    public function transform($zipCode)
    {
        if (null === $zipCode) {
            return null;
        }

        if ($zipCode->isSingleZipCode()) {
            $zipCode
                ->setZipRangeStart($zipCode->getZipCode())
                ->setZipCode(null);
        }

        return $zipCode;
    }

    /**
     * {@inheritdoc}
     * @param ZipCode $zipCode
     */
    public function reverseTransform($zipCode)
    {
        if (null === $zipCode) {
            return null;
        }

        $zipRangeStart = $zipCode->getZipRangeStart();
        $zipRangeEnd = $zipCode->getZipRangeEnd();

        if ($zipRangeStart === $zipRangeEnd || ($zipRangeStart && !$zipRangeEnd) || (!$zipRangeStart && $zipRangeEnd)) {
            $zipCode
                ->setZipCode($zipRangeStart ?: $zipRangeEnd)
                ->setZipRangeStart(null)
                ->setZipRangeEnd(null);
        }

        return $zipCode;
    }
}
