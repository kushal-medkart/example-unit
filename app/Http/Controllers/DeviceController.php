<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\DeviceRepository;

class DeviceController extends Controller
{
    protected $deviceRepo;
    
    public function __construct()
    {
        $this->deviceRepo = new DeviceRepository;
    }   

    public function index() {
        return $this->deviceRepo->execute();
    }
}
