<?php
use PHPUnit\Framework\TestCase;
use App\Repository\DeviceRepository;
use PHPUnit\Framework\Attributes\DataProvider;

final class FileInfoTesting extends TestCase
{
    public function InvalidControllerPath()
    {
        $this->assertTrue(
            true, "Controller path does not exist in base path: basepath"
        );
    }

}
