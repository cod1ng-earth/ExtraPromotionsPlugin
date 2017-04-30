<?php

namespace Tests\CodingBerlin\ExtraPromotionPlugin\Behat\Context\Setup;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use CodingBerlin\ExtraPromotionPlugin\Promotion\Rule\BirthdayRuleChecker;
use CodingBerlin\ExtraPromotionPlugin\Promotion\Rule\EmailListRuleChecker;
use CodingBerlin\ExtraPromotionPlugin\Promotion\Rule\PreviousOrdersTotalAmountPromotion;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Factory\PromotionActionFactoryInterface;
use Sylius\Component\Core\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;

class ExtraPromotionContext implements Context
{
    /**
     * @var PromotionRuleFactoryInterface
     */
    private $ruleFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var PromotionActionFactoryInterface
     */
    private $actionFactory;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PromotionActionFactoryInterface $actionFactory
     * @param PromotionRuleFactoryInterface $ruleFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PromotionActionFactoryInterface $actionFactory,
        PromotionRuleFactoryInterface $ruleFactory,
        ObjectManager $objectManager
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->objectManager = $objectManager;
        $this->sharedStorage = $sharedStorage;
        $this->actionFactory = $actionFactory;
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount to the customer with email "([^"]+)"$/
     */
    public function thePromotionGivesDiscountToTheCustomerWithEmail(
        PromotionInterface $promotion,
        $discount,
        $email
    ) {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->ruleFactory->createNew();
        $rule->setType(EmailListRuleChecker::TYPE);
        $rule->setConfiguration(['emails' => $email]);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount at the customers birthday$/
     */
    public function thePromotionGivesDiscountAtTheCustomersBirthday(
        PromotionInterface $promotion,
        $discount
    ) {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->ruleFactory->createNew();
        $rule->setType(BirthdayRuleChecker::TYPE);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @param PromotionInterface $promotion
     * @param int $discount
     * @param array $configuration
     * @param PromotionRuleInterface|null $rule
     */
    private function createFixedPromotion(
        PromotionInterface $promotion,
        $discount,
        array $configuration = [],
        PromotionRuleInterface $rule
    ) {
        $this->persistPromotion($promotion, $this->actionFactory->createFixedDiscount($discount, $this->sharedStorage->get('channel')->getCode()), $configuration, $rule);
    }

    /**
     * @param PromotionInterface $promotion
     * @param PromotionActionInterface $action
     * @param array $configuration
     * @param PromotionRuleInterface $rule
     */
    private function persistPromotion(PromotionInterface $promotion, PromotionActionInterface $action, array $configuration, PromotionRuleInterface $rule)
    {
        $configuration = array_merge_recursive($action->getConfiguration(), $configuration);
        $action->setConfiguration($configuration);

        $promotion->addAction($action);
        if (null !== $rule) {
            $promotion->addRule($rule);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount if the customer spent ("(?:€|£|\$)[^"]+") previously$/
     */
    public function thePromotionGivesDiscountToTheCustomerWithPreviouslySpentAmount(
        PromotionInterface $promotion,
        $discount,
        $previousAmount
    ) {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->ruleFactory->createNew();
        $rule->setType(PreviousOrdersTotalAmountPromotion::TYPE);
        $rule->setConfiguration(['previous_orders_total_amount' => $previousAmount]);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }
}
