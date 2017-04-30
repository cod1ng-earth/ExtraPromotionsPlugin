<?php

namespace CodingBerlin\ExtraPromotionPlugin\Promotion\Rule;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class BirthdayRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'birthday_today';

    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        $customer = $subject->getCustomer();
        if (!$customer instanceof CustomerInterface) {
            return false;
        }

        $birthday = $customer->getBirthday();
        if (!$birthday instanceof \DateTime) {
            return false;
        }

        $today = new \DateTime();
        return ($birthday->format('md') === $today->format('md'));
    }
}
