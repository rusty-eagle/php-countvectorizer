<?php

declare(strict_types=1);

namespace PhpCountVectorizer;

final class CountVectorizer
{
	/**
	 * The token(s) to break up a string.
	 * @var string
	 */
	private $token;

	/**
	 * Index of words being accounted for
	 * @var array
	 */
	private $index;

	/**
	 * pay attention to case or not
	 * @var bool
	 */
	private $case_sensitive;

	/**
	 * array of stop words.
	 * @var array
	 */
	private $stop_words;

	/**
	 * Default list of stop words
	 *
	 * Thanks to this reference:
	 * https://kb.yoast.com/kb/list-stop-words/
	 *
	 * @var const array
	 */
	private const STOP_WORDS = [
		"a","about","above","after","again","against","all","am",
		"an","and","any","are","as","at","be","because","been",
		"before","being","below","between","both","but","by",
		"could","did","do","does","doing","down","during","each",
		"few","for","from","further","had","has","have","having",
		"he","her","here","hers","herself","him","himself","his",
		"how","I","if","in","into","is","it","its","itself","let",
		"me","more","most","my","myself","nor","of","on","once",
		"only","or","other","ought","our","ours","ourselves","out",
		"over","own","same","she","should","so","some","such","than",
		"that","the","their","theirs","them","themselves","then",
		"there","these","they","this","those","through","to","too",
		"under","until","up","very","was","we","were","what","when",
		"where","which","while","who","whom","why","with","would",
		"you","your","yours","yourself","yourselves"
	];

	/**
	 * Character count limit before multiplier is applied
	 * @var const int
	 */
	private const CHAR_COUNT = 3;

	/**
	 * Weight multiplier
	 * @var const int
	 */
	private const WEIGHT_MULTIPLIER = 2;

	/**
	 * Default tokens to split input text
	 * @var const string
	 */
	private const TOKENS = " .!?\n\t";

	/**
	 * Constructor
	 *
	 * @param bool $case_sensitive
	 * @param string $token
	 * @return void
	 */
	final public function __construct( bool $case_sensitive = false, string $token = self::TOKENS, array $stop_words = self::STOP_WORDS )
	{
		$this->token = $token;
		$this->index = [];
		$this->case_sensitive = $case_sensitive;
		$this->stop_words = $stop_words;
	}

	/**
	 * Get the amount of words being kept track of
	 *
	 * @return int
	 */
	final public function index_size(): int
	{
		return (int) sizeof($this->index);
	}

	/**
	 * Used for the data that you will run $classifier->predict() on.
	 * This will not alter the index size.
	 *
	 * @param string $sample
	 * @return array
	 */
	final public function test_fit_transform( string $sample ): array
	{
		return $this->word2vec($sample);
	}

	/**
	 * Used to fit an array of strings into an array of counted vectors.
	 *
	 * Example Input
	 * [
	 * 	"this is the first string of text to vectorize.",
	 * 	"secondly this information is provided."
	 * ]
	 *
	 * @param array $samples
	 * @return array
	 */
	final public function fit_transform( array $samples ): array
	{
		$this->index($samples);
		return array_map( array(__NAMESPACE__ . '\CountVectorizer', 'word2vec'), $samples);
	}

	/**
	 * Build an index of words from an array of samples. Stored as a
	 * class variable.
	 *
	 * @param array $samples
	 * @return void
	 */
	final private function index( array $samples ): void
	{
		foreach($samples as $sample)
		{
			$tok = strtok($sample, $this->token);
			while($tok !== false)
			{
				if( ! $this->case_sensitive )
					$tok = strtolower($tok);
				if( ! in_array( strtolower($tok), $this->stop_words ) )
					$this->index[] = $tok;
				$tok = strtok($this->token);
			}
		}
	}

	/**
	 * Convert a string of words into an array of counted values.
	 *
	 * @param string $words
	 * @return array
	 */
	final private function word2vec( string $words ): array
	{
		$word2vec = [];
		$vec = [];
		$tok = strtok($words, $this->token);
		while ($tok !== false) {
			if( ! $this->case_sensitive )
				$tok = strtolower($tok);
			$multiplier = (int) strlen($tok) >= self::CHAR_COUNT ? self::WEIGHT_MULTIPLIER : 1;
			if( ! isset($vec["$tok"]) AND in_array($tok,$this->index) )
				$vec["$tok"] = (int) 1 * $multiplier;
			else if ( in_array($tok, $this->index) )
				$vec["$tok"] += (int) 1 * $multiplier;
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

	/**
	 * Set the "stop words", ie, frequent words to ignore that don't
	 * contain much information, such as "of", "a", etc.
	 *
	 * @param array $stop_words
	 * @return void
	 *
	 */
	final public function set_stop_words( array $stop_words ): void
	{
		$this->stop_words = $stop_words;
	}
}
