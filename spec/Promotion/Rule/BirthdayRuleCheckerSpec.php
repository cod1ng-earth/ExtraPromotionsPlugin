<?php

namespace spec\CodingBerlin\ExtraPromotionPlugin\Promotion\Rule;

use CodingBerlin\ExtraPromotionPlugin\Promotion\Rule\BirthdayRuleChecker;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;

class BirthdayRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BirthdayRuleChecker::class);
    }

    function it_is_a_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_allows_the_customer_having_birthday_today_to_receive_a_promotion(
        OrderInterface $order,
        CustomerInterface $customer
    ) {
        $order->getCustomer()->willReturn($customer);
        $customer->getBirthday()->willReturn(new \DateTime());
        $this->isEligible($order, [])->shouldReturn(true);
    }

    function it_does_not_allow_the_customer_not_having_birthday_today_to_receive_a_promotion(
        OrderInterface $order,
        CustomerInterface $customer
    ) {
        $birthday = new \DateTime();
        $birthday->add(new \DateInterval('P01D'));

        $order->getCustomer()->willReturn($customer);
        $customer->getBirthday()->willReturn($birthday);
        $this->isEligible($order, [])->shouldReturn(false);
    }
}
