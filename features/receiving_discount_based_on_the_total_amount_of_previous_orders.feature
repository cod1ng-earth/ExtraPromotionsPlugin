@applying_extra_promotion_rules
Feature: Receiving discount based on the total amount of previous orders
    In order to pay decreased amount for my order during promotion
    As a Visitor
    I want to have promotion applied to my cart when the total amount of my previous orders has reached the defined amount

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store ships everywhere for free
        And the store allows paying "Cash on Delivery"
        And there is a promotion "previous orders total amount promotion"
        And it gives "$20.00" discount if the customer spent "$300.00" previously
        And I am a logged in customer

    @ui
    Scenario: Receiving a discount on an order if the previous amount reaches the given amount
        Given I have already placed 4 orders choosing "PHP T-Shirt" product, "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"
