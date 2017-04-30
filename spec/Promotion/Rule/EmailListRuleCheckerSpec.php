<?php

namespace spec\CodingBerlin\ExtraPromotionPlugin\Promotion\Rule;

use CodingBerlin\ExtraPromotionPlugin\Promotion\Rule\EmailListRuleChecker;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class EmailListRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EmailListRuleChecker::class);
    }

    function it_is_a_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_allows_the_customer_from_the_list_to_receive_a_promotion(
        OrderInterface $order,
        CustomerInterface $customer
    ) {
        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('abc@example.com');
        $this->isEligible($order, ['emails' => 'abc@example.com'])->shouldReturn(true);
        $this->isEligible($order, ['emails' => 'abc@example.com,bla@example.com'])->shouldReturn(true);
        $this->isEligible($order, ['emails' => 'bla@example.com'])->shouldReturn(false);
    }

    function it_expects_an_order_interface(PromotionSubjectInterface $promotionSubject)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('isEligible', [$promotionSubject, []]);
    }
}
