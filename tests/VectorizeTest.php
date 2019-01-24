<?php

namespace PhpCountVectorizer\Tests;

include("../src/CountVectorizer.php");

use PhpCountVectorizer\CountVectorizer;
$cv = new CountVectorizer;

$samplesRaw = [
	"This is a test",
	"To check out the CountVectorizer",
	"To ensure it works.",
];

$samples = $cv->fit_transform( $samplesRaw );
$labels = ['test','check','works'];

$input = "Customer response data.";

$response = $cv->test_fit_transform( $input );
