<?php

namespace PhpCountVectorizer;

final class CountVectorizer
{
	private $token;
	private $index;
	private $case_sensitive;

	final public function __construct( bool $case_sensitive = false, string $token = " .!?\n\t" )
	{
		$this->token = $token;
		$this->index = [];
		$this->case_sensitive = $case_sensitive;
	}

	final public function index_size(): int
	{
		return (int) sizeof($this->index);
	}

	final public function test_fit_transform( $sample )
	{
		return $this->word2vec($sample);
	}
	final public function fit_transform( $samples )
	{
		// Ex. data
		// [
		// 	"string 1",
		// 	"string 2",
		// ]
		$this->index($samples);
		return array_map( array(__NAMESPACE__ . '\CountVectorizer', 'word2vec'), $samples);
	}

	final private function index( $samples ): void
	{
		foreach($samples as $sample)
		{
			$tok = strtok($sample, $this->token);
			while($tok !== false)
			{
				if( ! $this->case_sensitive )
					$tok = strtolower($tok);
				$this->index[] = $tok;
				$tok = strtok($this->token);
			}
		}
	}
	final private function word2vec( string $words )
	{
		$word2vec = [];
		$vec = [];
		$tok = strtok($words, $this->token);
		while ($tok !== false) {
			if( ! $this->case_sensitive )
				$tok = strtolower($tok);
			if( ! isset($vec["$tok"]) AND in_array($tok,$this->index) )
				$vec["$tok"] = 1;
			else if ( in_array($tok, $this->index) )
				$vec["$tok"]++;
			$tok = strtok( $this->token );
		}
		foreach( $this->index as $val )
		{
			if( !isset($vec["$val"]) )
				$vec["$val"] = 0;
		}
		ksort($vec);
		foreach($vec as $key => $val)
		{
			$word2vec[] = $val;
		}
		return $word2vec;
	}
}
