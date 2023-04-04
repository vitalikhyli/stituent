<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class PDFController extends Controller
{
    public function marketingSummary()
    {
    	return view('marketing.summary');
    }
}
