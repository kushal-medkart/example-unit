<?php

namespace App\Repository;

class DeviceRepository extends BaseRepository {
    
    public function execute($input = 0)
    {
        $variable = 'initial value';

        if ($input > 30) {
            $variable = 'updated value';
        }

        return $variable;
    }
}