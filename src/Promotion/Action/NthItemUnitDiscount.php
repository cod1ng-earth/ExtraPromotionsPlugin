<?php


namespace CodingBerlin\ExtraPromotionPlugin\Promotion\Action;

use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Action\DiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

class NthItemUnitDiscount extends DiscountPromotionActionCommand
{
    const TYPE = 'nth_item_unit_discount';
    const KEY = 'nth';

    /**
     * @var ProportionalIntegerDistributorInterface
     */
    private $proportionalDistributor;

    /**
     * @var UnitsPromotionAdjustmentsApplicatorInterface
     */
    private $unitsPromotionAdjustmentsApplicator;

    /**
     * @param ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
     * @param UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
     */
    public function __construct(
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator
    ) {
        $this->proportionalDistributor = $proportionalIntegerDistributor;
        $this->unitsPromotionAdjustmentsApplicator = $unitsPromotionAdjustmentsApplicator;
    }

    /**
     * @inheritdoc
     */
    protected function isConfigurationValid(array $configuration)
    {
        Assert::keyExists($configuration, self::KEY);
    }

    /**
     * @param PromotionSubjectInterface $subject
     * @param array $configuration
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf($subject, OrderInterface::class);

        try {
            $this->isConfigurationValid($configuration);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        $items = $subject->getItems();
        $nth = $configuration[self::KEY];
        $adjustAmount = 0;

        foreach ($items as $item) {
            $adjustAmount += $item->getUnitPrice() * ((int) floor(count($item->getUnits()) / $nth));
        }

        $itemsTotals = [];
        foreach ($items as $item) {
            $itemsTotals[] = $item->getTotal();
        }

        $splitPromotion = $this->proportionalDistributor->distribute($itemsTotals, (int) $adjustAmount);
        $this->unitsPromotionAdjustmentsApplicator->apply($subject, $promotion, $splitPromotion);
    }
}
