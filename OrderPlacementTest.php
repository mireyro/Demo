<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\WebDriver\WebDriverBy;
use PHPUnit\WebDriver\WebDriverExpectedCondition;

class DemoBlazeOrderPlacementTest extends TestCase
{
    protected $webDriver;

    protected function setUp(): void
    {
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', \Facebook\WebDriver\Remote\DesiredCapabilities::chrome());
    }

    public function testUserRegistration()
    {
        // Navigate to page
        $this->webDriver->get('https://www.demoblaze.com/');

        // Fill in registration form
        $this->webDriver->findElement(WebDriverBy::cssSelector('a#signin2.nav-link'))->click();
        $this->webDriver->findElement(WebDriverBy::id('sign-username'))->sendKeys('testuser');
        $this->webDriver->findElement(WebDriverBy::id('sign-password'))->sendKeys('password');
        $this->webDriver->findElement(WebDriverBy::cssSelector('button#signInModal.btn.btn-primary'))->click();

        // Verify registration success message
        $this->assertStringContainsString('Sign up successful.', $this->webDriver->getPageSource());
    }

    public function testLogin()
    {
        // Navigate to page
        $this->webDriver->get('https://www.demoblaze.com');

        // Fill in login form
        $this->webDriver->findElement(WebDriverBy::cssSelector('a#login2.nav-link'))->click();
        $this->webDriver->findElement(WebDriverBy::id('loginusername'))->sendKeys('testuser');
        $this->webDriver->findElement(WebDriverBy::id('loginpassword'))->sendKeys('password');
        $this->webDriver->findElement(WebDriverBy::cssSelector('button#logInModal.btn.btn-primary'))->click();

    }

    public function testAddProductToCart()
    {
        // Navigate to product page
        $this->webDriver->get('https://www.demoblaze.com/');

        // Click on a product
        $this->webDriver->findElement(WebDriverBy::linkText('Samsung galaxy s6'))->click();

        // Add the product to cart
        $this->webDriver->findElement(WebDriverBy::cssSelector('a.btn.btn-success'))->click();

        // Verify product added to cart
        $this->webDriver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('cartur'))
        );
    }

    public function testPlaceOrder()
    {
        // Navigate to cart
        $this->webDriver->get('https://www.demoblaze.com/');

        // Proceed to checkout
        $this->webDriver->findElement(WebDriverBy::id('cartur'))->click();

        // Fill in shipping information
        $this->webDriver->findElement(WebDriverBy::id('name'))->sendKeys('John Doe');
        $this->webDriver->findElement(WebDriverBy::id('country'))->sendKeys('United States');
        $this->webDriver->findElement(WebDriverBy::id('city'))->sendKeys('New York');
        $this->webDriver->findElement(WebDriverBy::id('card'))->sendKeys('1234567890123456');
        $this->webDriver->findElement(WebDriverBy::id('month'))->sendKeys('01');
        $this->webDriver->findElement(WebDriverBy::id('year'))->sendKeys('2025');
        $this->webDriver->findElement(WebDriverBy::cssSelector('button.btn.btn-primary'))->click();

        // Verify order placement success message
        $this->assertStringContainsString('Thank you for your purchase!', $this->webDriver->getPageSource());
    }

    // Negative scenario: Submitting empty form
    public function testEmptyFormSubmission()
    {
        $this->webDriver->get('http://example.com/checkout');

        // Submitting without filling in the form
        $this->webDriver->findElement(WebDriverBy::id('submit'))->click();

        // Verify error message
        $errorMessage = $this->webDriver->findElement(WebDriverBy::id('error'))->getText();
        $this->assertStringContainsString('Please fill in all the required fields', $errorMessage);
    }

    //Testing maximum order quantity
    public function testMaximumOrderQuantity()
    {
        $this->webDriver->get('http://example.com/product/1');

        // Add maximum allowed quantity to cart
        $this->webDriver->findElement(WebDriverBy::id('quantity'))->clear()->sendKeys('10');
        $this->webDriver->findElement(WebDriverBy::id('add-to-cart'))->click();

        // Proceed to checkout
        $this->webDriver->get('http://example.com/checkout');

        // Verify quantity in the order summary
        $orderQuantity = $this->webDriver->findElement(WebDriverBy::id('order-quantity'))->getText();
        $this->assertEquals(10, (int)$orderQuantity);
    }

    // Positive scenario: Select order successfully
    public function testSelectOrderSuccessfully()
    {
        // Navigate to the DemoBlaze website
        $this->webDriver->get('https://www.demoblaze.com/');

        // Click on a product category
        $this->webDriver->findElement(WebDriverBy::linkText('Laptops'))->click();

        // Click on a specific product
        $this->webDriver->findElement(WebDriverBy::linkText('Sony vaio i5'))->click();

        // Add the product to the cart
        $this->webDriver->findElement(WebDriverBy::xpath("//a[contains(text(),'Add to cart')]"))->click();

        // Check for success message
        $successMessage = $this->webDriver->findElement(WebDriverBy::xpath("//div[@id='cartur']/preceding-sibling::h3"))->getText();
        $this->assertEquals('Product added.', $successMessage);
    }

    // Negative scenario: Select non-existing order
    public function testSelectNonExistingOrder()
    {
        // Navigate to the DemoBlaze website
        $this->webDriver->get('https://www.demoblaze.com/');

        // Try to click on a non-existing product
        $this->webDriver->findElement(WebDriverBy::linkText('Non-existent product'))->click();

        // Check for error message
        $errorMessage = $this->webDriver->findElement(WebDriverBy::xpath("//body"))->getText();
        $this->assertStringContainsString('Not Found', $errorMessage);
    }

    // Select order with maximum price
    public function testSelectOrderWithMaximumPrice()
    {
        // Navigate to the DemoBlaze website
        $this->webDriver->get('https://www.demoblaze.com/');

        // Click on the product category
        $this->webDriver->findElement(WebDriverBy::linkText('Monitors'))->click();

        // Click on the product with maximum price
        $this->webDriver->findElement(WebDriverBy::xpath("//a[contains(text(),'Apple monitor 24')]"))->click();

        // Add the product to the cart
        $this->webDriver->findElement(WebDriverBy::xpath("//a[contains(text(),'Add to cart')]"))->click();

        // Check for success message
        $successMessage = $this->webDriver->findElement(WebDriverBy::xpath("//div[@id='cartur']/preceding-sibling::h3"))->getText();
        $this->assertEquals('Product added.', $successMessage);
    }

    protected function tearDown(): void
    {
        $this->webDriver->quit();
    }
}
