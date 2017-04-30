<?php

namespace CodingBerlin\ExtraPromotionPlugin\Promotion\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class PreviousOrdersTotalAmountPromotion implements RuleCheckerInterface
{
    const KEY = 'previous_orders_total_amount';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        $customer = $subject->getCustomer();

        /** @var OrderInterface[] $previousOrders */
        $previousOrders = $this->orderRepository->findByCustomer($customer);

        $previousAmountTotal = 0;
        foreach ($previousOrders as $previousOrder) {
            $previousAmountTotal += $previousOrder->getTotal();
        }

        if ($previousAmountTotal >= $configuration[self::KEY]) {
            return true;
        }

        return false;
    }
}
