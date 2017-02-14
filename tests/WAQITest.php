<?php
/**
 * This file is part of the WAQI (World Air Quality Index) package.
 *
 * Copyright (c) 2017 AzuyaLabs
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author Sacha Telgenhof <stelgenhof@gmail.com>
 */

namespace Azuyalabs\WAQI\Test;

use Azuyalabs\WAQI\WAQI;
use Faker\Factory;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Unit test class containing tests for the WAQI class
 *
 * @package Azuyalabs\WAQI\Test
 */
class WAQITest extends TestCase
{
    /**
     * @var object mock object representing the WAQI class
     */
    private $waqi;

    /**
     * @var object Faker object instance for randomizing test values
     */
    private $faker;

    /**
     * Prepare and initialize tests
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
        $this->waqi = Mockery::mock(WAQI::class, [$this->faker->md5]);
    }

    /**
     * Clean up after tests have been performed
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();

        unset($this->faker);
        unset($this->waqi);
    }

    /**
     * Tests that a valid temperature value is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getTemperature()
     *
     * @return void
     */
    public function shouldGetTemperature(): void
    {
        $this->assertPollutantLevel('getTemperature', $this->faker->randomFloat(2, -100, 100));
    }

    /**
     * Tests that a valid barometric pressure value is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getPressure()
     *
     * @return void
     */
    public function shouldGetPressure(): void
    {
        $this->assertPollutantLevel('getPressure', $this->faker->randomFloat(2, -800, 1100));
    }

    /**
     * Tests that a valid humidity value is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getHumidity()
     *
     * @return void
     */
    public function shouldGetHumidity(): void
    {
        $this->assertPollutantLevel('getHumidity', $this->faker->randomFloat(2, -800, 1100));
    }

    /**
     * Tests that a valid value for the fine particulate matter, 2.5 micrometers or less (PM2.5), is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getPM25()
     *
     * @return void
     */
    public function shouldGetPM25(): void
    {
        $this->assertPollutantLevel('getPM25', $this->faker->randomFloat(2, 0, 500));
    }

    /**
     * Tests that a valid value for the respirable particulate matter, 10 micrometers or less (PM2.5), is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getPM10()
     *
     * @return void
     */
    public function shouldGetPM10(): void
    {
        $this->assertPollutantLevel('getPM10', $this->faker->randomFloat(2, 0, 500));
    }

    /**
     * Tests that a valid CO (carbon monoxide) value is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getCO()
     *
     * @return void
     */
    public function shouldGetCO(): void
    {
        $this->assertPollutantLevel('getCO', $this->faker->randomFloat(2, 0, 500));
    }

    /**
     * Tests that a valid NO2 (nitrogen dioxide) value is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getNO2()
     *
     * @return void
     */
    public function shouldGetNO2(): void
    {
        $this->assertPollutantLevel('getNO2', $this->faker->randomFloat(2, 0, 500));
    }

    /**
     * Tests that a valid O3 (ozone) value is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getO3()
     *
     * @return void
     */
    public function shouldGetO3(): void
    {
        $this->assertPollutantLevel('getO3', $this->faker->randomFloat(2, 0, 500));
    }

    /**
     * Tests that a valid SO2 (sulfur dioxide) value is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getSO2()
     *
     * @return void
     */
    public function shouldGetSO2(): void
    {
        $this->assertPollutantLevel('getSO2', $this->faker->randomFloat(2, 0, 500));
    }

    /**
     * Tests that a valid measurement time value is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getMeasurementTime()
     *
     * @return void
     */
    public function shouldGetMeasurementTime(): void
    {
        $expectedValue = $this->faker->dateTime();

        $this->waqi->shouldReceive('getMeasurementTime')
            ->once()
            ->withNoArgs()
            ->andReturn($expectedValue);

        $result = $this->waqi->getMeasurementTime();

        $this->assertEquals($expectedValue, $result);
        $this->assertInstanceOf('DateTime', $result);
        $this->assertNotEmpty($result);
        $this->assertNotNull($result);
    }

    /**
     * Tests that a valid array is returned that represents the monitoring station information.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getMonitoringStation()
     *
     * @return void
     */
    public function shouldGetMonitoringStationInformation(): void
    {
        $expectedValue = [
            'id' => $this->faker->randomDigitNotNull(),
            'name' => $this->faker->text(50),
            'coordinates' => [
                'latitude' => $this->faker->latitude(),
                'longitude' => $this->faker->longitude()
            ],
            'url' => $this->faker->url()
        ];

        $this->waqi->shouldReceive('getMonitoringStation')
            ->once()
            ->withNoArgs()
            ->andReturn($expectedValue);

        $result = $this->waqi->getMonitoringStation();

        // Assertion of overall structure
        $this->assertValue($result, $expectedValue, 'array');

        // Assertion of each individual element
        foreach (['id' => 'int', 'name' => 'string', 'url' => 'string', 'coordinates' => 'array'] as $name => $type) {
            $this->assertValue($result[$name], $expectedValue[$name], $type);
        }

        // Assertion of the coordinates element
        $this->assertValue($result['coordinates']['longitude'], $expectedValue['coordinates']['longitude'], 'float');
        $this->assertValue($result['coordinates']['latitude'], $expectedValue['coordinates']['latitude'], 'float');
    }

    /**
     * Tests that a valid array is returned that represents the Air Quality Index information.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getAQI()
     *
     * @return void
     */
    public function shouldGetAQIInformation(): void
    {
        $expectedValue = [
            'aqi' => $this->faker->randomFloat(2, 0, 500),
            'pollution_level' => $this->faker->text(50),
            'health_implications' => $this->faker->text(150),
            'cautionary_statement' => $this->faker->text(150)
        ];

        $this->waqi->shouldReceive('getAQI')
            ->once()
            ->withNoArgs()
            ->andReturn($expectedValue);

        $result = $this->waqi->getAQI();

        // Assertion of overall structure
        $this->assertValue($result, $expectedValue, 'array');

        // Assertion of each individual element
        foreach (['aqi' => 'float', 'pollution_level' => 'string', 'health_implications' => 'string', 'cautionary_statement' => 'string'] as $name => $type) {
            $this->assertValue($result[$name], $expectedValue[$name], $type);
        }
    }

    /**
     * Tests that a valid value for the primary pollutant is returned.
     *
     * @test
     * @covers \Azuyalabs\WAQI\WAQI::getPrimaryPollutant()
     *
     * @return void
     */
    public function shouldGetPrimaryPollutant(): void
    {
        $expectedValue = $this->faker->randomElement(['pm25', 'pm10', 'co', 'no2', 'o3', 'so2']);

        $this->waqi->shouldReceive('getPrimaryPollutant')
            ->once()
            ->withNoArgs()
            ->andReturn($expectedValue);

        $result = $this->waqi->getPrimaryPollutant();

        $this->assertValue($result, $expectedValue, 'string');
    }

    /**
     * Performs basic assertions on a result value.
     *
     * @param $result mixed the value to be asserted
     * @param $expectedValue mixed the expected value
     * @param string $type the internal type representing the given value (e.g. 'int', 'string', etc.)
     */
    private function assertValue($result, $expectedValue, string $type): void
    {
        $this->assertEquals($expectedValue, $result);
        $this->assertInternalType($type, $result);
        if (is_object($result)) {
            $this->assertInstanceOf($type, $result);
        } else {
            $this->assertInternalType($type, $result);
        }

        $this->assertNotEmpty($result);
        $this->assertNotNull($result);
    }

    /**
     * Performs basic assertions on a pollutant level value.
     *
     * @param string $method the class method that obtains the pollutant level value
     * @param float $expectedValue the expected value the provided method should be
     */
    private function assertPollutantLevel(string $method, float $expectedValue): void
    {
        $this->waqi->shouldReceive($method)
            ->once()
            ->withNoArgs()
            ->andReturn($expectedValue);

        $result = $this->waqi->$method();

        $this->assertValue($result, $expectedValue, 'float');
    }
}