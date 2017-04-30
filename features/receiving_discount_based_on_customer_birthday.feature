@applying_extra_promotion_rules
Feature: Receiving discount based on customer birthday
    In order to pay decreased amount for my order during promotion
    As a Visitor
    I want to have promotion applied to my cart when my birthday is today

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Customer birthday promotion"
        And this user has birthday today

    @ui
    Scenario: Receiving discount when the birthday is today
        Given I am logged in as "john@example.com"
        And the promotion gives "$10.00" discount at the customers birthday
        When I add 2 products "PHP T-Shirt" to the cart
        Then my cart total should be "$190.00"
        And my discount should be "-$10.00"
