<?php


namespace CodingBerlin\ExtraPromotionPlugin\Promotion\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class EmailListRuleChecker  implements RuleCheckerInterface
{
    const TYPE = 'email_list';

    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        $customerEmail = $subject->getCustomer()->getEmail();

        return in_array($customerEmail, explode(',', $configuration['emails']), true);
    }
}