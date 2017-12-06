<?php

namespace UWDOEM\REST\Backend\Test;


class FormsAPIFakerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FormsAPIFaker */
    protected $faker;

    public function setUp()
    {
        parent::setUp();

        $referenceIds = [
            'elements' => 1,
            'visitors' => 2,
        ];

        $this->faker = new FormsAPIFaker(
            [
                'reference' => function($resourceType) use ($referenceIds) {
                    return $referenceIds[$resourceType];
                },
            ]
        );
    }

    public function testDefaultAttributes()
    {
        $fakeForm = $this->faker->fake('forms');

        $this->assertArrayHasKey('name', $fakeForm);
        $this->assertArrayHasKey('slug', $fakeForm);
        $this->assertArrayHasKey('success_message', $fakeForm);
        $this->assertArrayHasKey('retired', $fakeForm);

        $this->assertInternalType(gettype(''), $fakeForm['name']);
        $this->assertInternalType(gettype(''), $fakeForm['slug']);
        $this->assertInternalType(gettype(''), $fakeForm['success_message']);
        $this->assertInternalType(gettype(False), $fakeForm['retired']);

        $this->assertEquals(5, sizeof($fakeForm));
    }

    public function testRequiredOnlyAttributes()
    {
        $fakeForm = $this->faker->fakeRequiredOnly('forms');

        $this->assertArrayHasKey('name', $fakeForm);
        $this->assertArrayHasKey('slug', $fakeForm);
        $this->assertArrayHasKey('success_message', $fakeForm);

        $this->assertInternalType(gettype(''), $fakeForm['name']);
        $this->assertInternalType(gettype(''), $fakeForm['slug']);
        $this->assertInternalType(gettype(''), $fakeForm['success_message']);

        $this->assertEquals(3, sizeof($fakeForm));
    }

    public function testProvideSomeAttributes()
    {
        $specificName = 'Specific Name for this Test';
        $extraAttributeKey = 'extra-attribute';
        $extraAttributeValue = 'extra-attribute-value';

        $fakeForm = $this->faker->fake('forms', ['name' => $specificName, $extraAttributeKey => $extraAttributeValue]);

        $this->assertArrayHasKey('name', $fakeForm);
        $this->assertArrayHasKey('slug', $fakeForm);
        $this->assertArrayHasKey('success_message', $fakeForm);
        $this->assertArrayHasKey('retired', $fakeForm);
        $this->assertArrayHasKey($extraAttributeKey, $fakeForm);

        $this->assertInternalType(gettype(''), $fakeForm['name']);
        $this->assertInternalType(gettype(''), $fakeForm['slug']);
        $this->assertInternalType(gettype(''), $fakeForm['success_message']);
        $this->assertInternalType(gettype(False), $fakeForm['retired']);
        $this->assertInternalType(gettype($extraAttributeValue), $fakeForm[$extraAttributeKey]);

        $this->assertEquals($specificName, $fakeForm['name']);
        $this->assertEquals($extraAttributeValue, $fakeForm[$extraAttributeKey]);

        $this->assertEquals(6, sizeof($fakeForm));
    }

    public function testProvideSomeAttributesToRequiredOnly()
    {

        $specificName = 'Specific Name for this Test';
        $extraAttributeKey = 'extra-attribute';
        $extraAttributeValue = 'extra-attribute-value';

        $fakeForm = $this->faker->fakeRequiredOnly('forms', ['name' => $specificName, $extraAttributeKey => $extraAttributeValue]);

        $this->assertArrayHasKey('name', $fakeForm);
        $this->assertArrayHasKey('slug', $fakeForm);
        $this->assertArrayHasKey('success_message', $fakeForm);
        $this->assertArrayHasKey($extraAttributeKey, $fakeForm);

        $this->assertInternalType(gettype(''), $fakeForm['name']);
        $this->assertInternalType(gettype(''), $fakeForm['slug']);
        $this->assertInternalType(gettype(''), $fakeForm['success_message']);
        $this->assertInternalType(gettype($extraAttributeValue), $fakeForm[$extraAttributeKey]);

        $this->assertEquals($specificName, $fakeForm['name']);
        $this->assertEquals($extraAttributeValue, $fakeForm[$extraAttributeKey]);

        $this->assertEquals(4, sizeof($fakeForm));
    }

}