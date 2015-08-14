<?php
namespace Creative {

defined( 'ABSPATH' ) or die;



#<!-- sort -->
#generic functions to translate from a source array to a target array
class Sort {
	public static $languages = array(
		"english alphabet" => array(
			"sort" => "default",
			"delimiter" => "",
			"keys" => array(
				'a', 'b', 'c', 'd', 'e',
				'f', 'g', 'h', 'i', 'j',
				'k', 'l', 'm', 'n', 'o',
				'p', 'q', 'r', 's', 't',
				'u', 'v', 'w', 'x', 'y', 'z'
			)
		),
		"greek phonetic" => array(
			"sort" => "by word",
			"delimiter" => " ",
			"keys" => array(
				"alpha",  "beta", "gamma", "delta", "epsilon",
				"zeta",   "eta",  "theta", "iota",  "kappa",
				"lambda", "mu",   "nu",    "xi",    "omicron",
				"pi",     "rho",  "sigma", "tau",   "upsilon",
				"phi",    "chi",  "psi",   "omega"
			)
		),
		"greek alphabet" => array(
			"sort" => "default",
			"delimiter" => "",
			"keys" => array(
				'α', 'β', 'γ', 'δ', 'ε',
				'ζ', 'η', 'θ', 'ι', 'κ',
				'λ', 'μ', 'ν', 'ξ', 'ο',
				'π', 'ρ', 'ς', 'τ', 'υ',
				'φ', 'χ', 'ψ', 'ω'
			)
		),
		"spanish alphabet" => array(
			"sort" => "default",
			"delimiter" => "",
			"keys" => array(
				'a', 'b', 'c', 'd', 'e',
				'f', 'g', 'h', 'i', 'j',
				'k', 'l', 'm', 'n', 'ñ', 'o',
				'p', 'q', 'r', 's', 't',
				'u', 'v', 'w', 'x', 'y', 'z'
			)
		),
		"russian alphabet" => array(
			"sort" => "default",
			"delimiter" => "",
			"keys" => array(
				'а', 'б', 'в', 'г', 'д',
				'е', 'ё', 'ж', 'з', 'и',
				'й', 'к', 'л', 'м', 'н',
				'о', 'п', 'р', 'с', 'т',
				'у', 'ф', 'х', 'ц', 'ч',
				'ш', 'щ', 'ъ', 'ы', 'ь',
				'э', 'ю', 'я'
			)
		),
		/*
		"chinese alphabet (simple)" => array(
			"sort" => "default",
			"delimiter" => "",
			"keys" => array(
				'诶', '比', '西', '迪', '伊',
				'艾弗', '吉', '艾尺', '艾', '杰',
				'开', '艾勒', '艾马', '艾娜', '哦',
				'屁', '吉吾', '艾儿', '艾丝', '提',
				'伊吾', '维', '豆贝尔维', '艾克斯', '吾艾',
				'贼德'
			)
		),
		*/
	);


	public $source = array();
	public $split  = "";
	public $formatter = null; #anonymous function


	public function __construct($source, $split=" ", $formatter=null) {
		$this->source     = $source;
		$this->split      = $split;
		$this->formatter  = $formatter;

		if($formatter) {
			for($i = 0; $i < count($this->source["keys"]); $i++) {
				$this->source["keys"][$i] = $formatter($this->source["keys"][$i]);
			}
		}
	}


	#<!-- STATIC FUNCTIONS -->
	public static function getSortOptions() {
		return array("default", "by character", "by word");
	}

	public static function getSortFunction($input) {
		$prefix = 'sort';

		switch($input) {
		case "default":
			return $prefix . "Strings";
		case "by character":
			return $prefix . "Strings";
		case "by word":
			return $prefix . "StringsByLength";
		default:
			return $prefix . "Strings";
		}
	}

	public static function getLanguage($language) {
		$a = Sort::$languages;

		if(array_key_exists($language, $a))
			return Sort::$languages[$language];
		else
			return null;
	}

	public static function getLanguageOptions() {
		$a = Sort::$languages;
		unset($a["english alphabet"]);
		$a = array_keys($a);
		return $a;
	}

	//gets the value of a key for all elements in an array 
	private static function getAttribute($array, $key) {
		$a = array();
		foreach($array as $element) {
			$a[] = $element[$key];
		}
		return $a;
	}
	#<!-- STATIC FUNCTIONS -->


	public function posts($posts, $sort_function) {
		$elements = array();

		#get titles
		foreach($posts as $post) {
			$elements[] = array(
				"post" => $post,
				"string" => get_the_title($post->ID)
			);
		}

		#sort the titles
		$sorted_elements = $this->$sort_function($elements);

		$output = self::getAttribute($sorted_elements, "post");

		return $output;
	}


	#@todo #documentation needs rewritten for this function
	#separates strings into words
	#uses bucket sort to sort via word count
		#individually sorts all strings with 1 word, 2 words, 3 words, etc
	#concatenates individual sorts into an array
	public function sortStringsByLength($elements) {
		#get $length of the longest string
		$max_length = 0;
		foreach($elements as $element) {
			$length = count(explode($this->split, $element["string"]));
			if($length > $max_length) $max_length = $length;
		}

		#create $length arrays
		$sorting_arrays = array_fill(0, $max_length, array());

		#sort $elements into arrays based on their length
		foreach($elements as $element) {
			$length = count(explode($this->split, $element["string"]));
			$index  = $length - 1;
			array_push($sorting_arrays[$index], $element);
		}

		#sort individual arrays
		for($i = 0; $i < count($sorting_arrays); $i++) {
			$sorting_arrays[$i] = $this->sortStrings($sorting_arrays[$i]);
		}

		#store sorted data into $elements
		$sorted_elements = array();
		foreach($sorting_arrays as $array) {
			foreach($array as $element) {
				$sorted_elements[] = $element;
			}
		}

		return $sorted_elements;
	}


	public function sortStrings($elements) {
		//print "before " . implode(", ", self::getAttribute($elements, 'string')) . "<br/>";

		//sort elements in the array
		$sorted = array();
		foreach($elements as $element) {
			$string = $element['string'];
			$numeric = $this->translateString($string);
			$sorted[$numeric] = array('e' => $element);
		}
		//print implode(", ", array_keys($sorted)) . "</br>";

		//sort the array
		ksort($sorted, SORT_NATURAL);

		$sorted_elements = self::getAttribute($sorted, "e");
		//print "after " . implode(", ", self::getAttribute($sorted_elements, "string")) . "<br/>" . "<br/>";

		return $sorted_elements;
	}


	#iterates through all tokens in the string and converts them individually
	public function translateString($string) {
		{
			//print "before split " . $string . "<br/>";
			$split = $this->split;
			$elements = array();
			if($split == "") {
				$elements = $this->mbStringToArray($string);
			} else {
				$elements = mb_split($split, $string);
			}
			//print "after split " . implode($split, $string) . "<br/>";
		}

		$numeric = $this->translateElements($elements);

		return $numeric;
	}


	#@note #just a helper function #really should be somewhere else
	function mbStringToArray ($string) {
	    $strlen = mb_strlen($string);
	    while ($strlen) {
	        $char = mb_substr($string,0,1,"UTF-8");
	        $array[] = $char;
	        $string = mb_substr($string,1,$strlen,"UTF-8");
	        $strlen = mb_strlen($string);
	    }
	    return $array;
	}


	#@todo #documentation needs rewritten for this function
	#takes two parrallel arrays as arguments, $source and $target
	#swaps elements from the source with elements from the target
	public function translateElements($elements) {
		$numerics = array();
		foreach($elements as $element) {
			$numeric = $this->translateElement($element);
			if($numeric != -1) {
				$numerics[] = $numeric;
			} else {
				$numerics[] = $numeric;
			}
		}
		return implode(" ", $numerics);
	}

	#@todo #documentation needs rewritten for this function
	#translates a single element from the source array to the target array
	#failures are silently ignored
	#@note #failures could also be forced after recognized elements or ignored entirely?
	public function translateElement($element) {
		$string = $element;
		if($this->formatter) {
			$formatter = $this->formatter;
			$string = $formatter($string);
		}

		if(!in_array($string, $this->source["keys"])) return -1;

		#remove whitespace
		//$element = preg_replace('/\s+/', '', $element);

		$index = array_search($string, $this->source["keys"]);
		#print $element . " translated to " . $index . "<br/>";
		return $index;
	}
} #<!-- sort -->

}
?>