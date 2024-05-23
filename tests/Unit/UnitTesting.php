<?php
use PHPUnit\Framework\TestCase;
use App\Repository\DeviceRepository;
use PHPUnit\Framework\Attributes\DataProvider;

final class UnitTesting extends TestCase
{
    /**
     * @dataProvider ExternalDataProvider::testProvider
     */
    public function testFunction(int $input, string $expectedOutput)
    {
        $deviceRepo = new DeviceRepository;
        $actualOutput = $deviceRepo->execute($input);
        
        // Assert that the output matches the expected output
        $this->assertEquals($expectedOutput, $actualOutput);
    }

}

final class ExternalDataProvider {
    public static function testProvider(): array
    {
        return [
            'case 1' => [0, "initial value"],
            'case 2' => [20, "updated value"]
        ];
    }
}