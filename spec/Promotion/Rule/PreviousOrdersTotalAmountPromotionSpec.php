<?php

namespace spec\CodingBerlin\ExtraPromotionPlugin\Promotion\Rule;

use CodingBerlin\ExtraPromotionPlugin\Promotion\Rule\PreviousOrdersTotalAmountPromotion;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class PreviousOrdersTotalAmountPromotionSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository)
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PreviousOrdersTotalAmountPromotion::class);
    }

    function it_implements_rule_checker_interface()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_expects_an_order_interface(PromotionSubjectInterface $promotionSubject)
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('isEligible', [$promotionSubject, []]);
    }

    function it_allows_the_customer_with_the_given_amount_of_previous_orders_to_receive_a_promotion(
        OrderInterface $order,
        OrderInterface $previousOrder,
        CustomerInterface $customer,
        OrderRepositoryInterface $orderRepository
    ) {
        $order->getCustomer()->willReturn($customer);
        $previousOrder->getTotal()->willReturn(100);
        $orderRepository->findByCustomer($customer)->willReturn([$previousOrder]);
        $this->isEligible($order, ['previous_orders_total_amount' => 100])->shouldReturn(true);
    }
}
