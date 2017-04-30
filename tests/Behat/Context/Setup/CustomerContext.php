<?php

namespace Tests\CodingBerlin\ExtraPromotionPlugin\Behat\Context\Setup;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CustomerContext implements Context
{


    /**
     * @Given /^(this user) has birthday today$/
     */
    public function thisUserHasBirthdayToday(ShopUserInterface $user)
    {
        $user->getCustomer()->setBirthday(new \DateTime());
    }
}
