<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Models\Utils\Profession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfessionController extends Controller
{
    public function index()
    {
        $data = Profession::all()->toArray();
        return $data;
    }
}
