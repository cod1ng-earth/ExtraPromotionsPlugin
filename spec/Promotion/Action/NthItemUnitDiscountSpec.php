<?php

namespace spec\AppBundle\Promotion\Action;

use AppBundle\Promotion\Action\NthItemUnitDiscount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class NthItemUnitDiscountSpec extends ObjectBehavior
{
    function let(UnitsPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator, ProportionalIntegerDistributorInterface $proportionalIntegerDistributor)
    {
        $this->beConstructedWith($proportionalIntegerDistributor, $adjustmentsApplicator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NthItemUnitDiscount::class);
    }

    function it_implements_promotion_action_command_interface()
    {
        $this->shouldImplement(PromotionActionCommandInterface::class);
    }

    function it_discounts_nth_unit_for_only_one_item(
        UnitsPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderInterface $subject,
        PromotionInterface $promotion,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $firstUnit,
        OrderItemUnitInterface $secondUnit
    ) {
        $subject->getItems()->willReturn([$orderItem]);
        $orderItem->getUnitPrice()->willReturn(10);
        $orderItem->getUnits()->willReturn([$firstUnit, $secondUnit]);
        $orderItem->getTotal()->willReturn(20);

        $proportionalIntegerDistributor->distribute([20], 10)->willReturn([10]);
        $adjustmentsApplicator->apply($subject, $promotion, [10])->shouldBeCalled();
        $this->execute($subject, ['nth' => 2], $promotion);
    }

    function it_discounts_nth_unit_for_two_items(
        UnitsPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderInterface $subject,
        PromotionInterface $promotion,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        OrderItemUnitInterface $firstUnit,
        OrderItemUnitInterface $secondUnit,
        OrderItemUnitInterface $thirdUnit
    ) {
        $subject->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
        $firstOrderItem->getUnitPrice()->willReturn(10);
        $firstOrderItem->getUnits()->willReturn([$firstUnit, $secondUnit]);
        $firstOrderItem->getTotal()->willReturn(20);

        $secondOrderItem->getUnitPrice()->willReturn(10);
        $secondOrderItem->getUnits()->willReturn([$thirdUnit]);
        $secondOrderItem->getTotal()->willReturn(10);

        $proportionalIntegerDistributor->distribute([20, 10], 10)->willReturn([10]);
        $adjustmentsApplicator->apply($subject, $promotion, [10])->shouldBeCalled();
        $this->execute($subject, ['nth' => 2], $promotion);
    }

    function it_discounts_two_times_the_nth_unit_for_one_item(
        UnitsPromotionAdjustmentsApplicatorInterface $adjustmentsApplicator,
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderInterface $subject,
        PromotionInterface $promotion,
        OrderItemInterface $firstOrderItem,
        OrderItemUnitInterface $firstUnit,
        OrderItemUnitInterface $secondUnit,
        OrderItemUnitInterface $thirdUnit,
        OrderItemUnitInterface $forthUnit,
        OrderItemUnitInterface $fifthUnit
    ) {
        $subject->getItems()->willReturn([$firstOrderItem]);
        $firstOrderItem->getUnitPrice()->willReturn(10);
        $firstOrderItem->getUnits()->willReturn([$firstUnit, $secondUnit, $thirdUnit, $forthUnit, $fifthUnit]);
        $firstOrderItem->getTotal()->willReturn(50);

        $proportionalIntegerDistributor->distribute([50], 20)->willReturn([20]);
        $adjustmentsApplicator->apply($subject, $promotion, [20])->shouldBeCalled();
        $this->execute($subject, ['nth' => 2], $promotion);
    }

    function it_expects_an_invalid_argument_exception_when_anything_other_than_order_interface_is_given(
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $this->shouldThrow(\InvalidArgumentException::class)->during('execute', [
            $subject, [], $promotion
        ]);
    }
}
