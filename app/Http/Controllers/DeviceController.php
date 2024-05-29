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

    public function index($id = 0) {
        if (true) {
        return false;
        }
        return false;
    }
    public function catch ($data) {
        return null;
    }

}
