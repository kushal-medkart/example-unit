<?php
use PHPUnit\Framework\TestCase;
use App\Repository\DeviceRepository;
use PHPUnit\Framework\Attributes\DataProvider;

final class FileInfoTesting extends TestCase
{
    /**
     * @dataProvider ExternalDataProvider::testProvider
     */
    public function InvalidControllerPath()
    {
        $this->assert(true);
    }

}
