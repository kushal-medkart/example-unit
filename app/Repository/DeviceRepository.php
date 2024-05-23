<?php

namespace App\Repository;

class DeviceRepository {
    
    public function execute($input = 0)
    {
        $variable = 'initial value';

        if ($input > 10) {
            $variable = 'updated value';
        }

        return $variable;
    }
}