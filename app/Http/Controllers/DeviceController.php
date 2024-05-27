<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\DeviceRepository;

class DeviceController extends ApiController
{
    protected $deviceRepo;
    
    public function __construct()
    {
        $this->deviceRepo = new DeviceRepository;
    }   

    public function index($id = 0) {
        return $this->deviceRepo->execute($id);
    }
}
