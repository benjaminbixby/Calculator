<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Calc Class
 *
 * @package		ExpressionEngine
 * @category	Plugin
 * @author		SHB
 * @copyright	Copyright (c) 2010, SHB
 * @link 		http://seehowbig.com
 */

$plugin_info = array(
  'pi_name' 		=> 'Calc',
  'pi_version' 		=> '1.0',
  'pi_author'	 	=> 'Jane Doe',
  'pi_author_url' 	=> 'http://example.com/',
  'pi_description' 	=> 'Calculates stuff.',
  'pi_usage' 		=> Calc::usage()
);

class Calc
{

	public $return_data = "";

	public function __construct()
	{
		$this->EE =& get_instance();
	}

	function calc_fence() {
		
		/*
		*  vars
		*  product choice = value is rail length, used to divide cust input
		*  price of rail = 
		*  price of post = 
		*/
		
		$custinput = $this->EE->input->post('custinput');
		$product = $this->EE->input->post('product');
		
		if ($custinput === "") {
		return "<p id='error'>Please enter in an amount (numbers only)!</p>";
		}

		if (!is_numeric($custinput)) {
		return "<p id='error'>Please enter numbers only!</p>";
		}
		// Separate product conversion factor from product price (in that order)
		$exp = explode("|",$product);
	
		/*
		*	$exp[0] is the rail length
		*	$exp[1] is the number of rails
		*	$exp[2] is the rail price
		*	$exp[3] is the line post price
		*   $exp[4] is the end post price
		*/
	
		$rl = $exp[0];
		$nr = $exp[1];
		$rp = $exp[2];
		$pp = $exp[3];
		$ep = $exp[4];

		// Cust input divided by rail length gives you X num of rails
		$rails = $custinput / $rl;

		// Add .5 to ensure round up
		$roundrails = $rails + .5;

		// Do the rounding
		$rounded = round($roundrails, 0, PHP_ROUND_HALF_UP);

		// Multiply times numrails to get total rails req
		$totalrails = $nr * $rounded;

		// Get number of total line posts
		$lineposts = $rounded - 1;
		$endposts = 2;
		$totalposts = $lineposts + $endposts;

		// Calculate price
		$railsprice = $totalrails * $rp;
		$linepostsp = $lineposts * $pp;
		$endpostsp = $endposts * $ep;
		$postsprice = $linepostsp + $endpostsp;
		$cost = $railsprice + $postsprice;

		// Put material and price into correct html
		$data = '<p id="fence">'.$totalposts.' posts<br />'.$totalrails.' rails</p>'.'<p id="cost">$'.$cost.'</p>';
		
		return $data;
		
	// end calc_fence	
	}

	// Test function
	function siding() {

	// Get all inputs
	$gableh = $this->EE->input->post('gableh');
	$gablew = $this->EE->input->post('gablew');
	$length = $this->EE->input->post('length');
	$height = $this->EE->input->post('height');
	$wd = $this->EE->input->post('windowsdoors');
	$product = $this->EE->input->post('product');
	
	if (!is_numeric($gableh) AND $gableh != "") {
		return "<p id='error'>Please enter numbers only!</p>";
	}
	
	if (!is_numeric($gablew) AND $gablew != "") {
		return "<p id='error'>Please enter numbers only!</p>";
	}

	if (!is_numeric($length) AND $length != "") {
		return "<p id='error'>Please enter numbers only!</p>";
	}

	if (!is_numeric($height) AND $height != "") {
		return "<p id='error'>Please enter numbers only!</p>";
	}

	if (!is_numeric($wd) AND $wd != "") {
		return "<p id='error'>Please enter numbers only!</p>";
	}

	if ($product === "undefined" OR $product === "") {
		return "<p id='error'>Please choose a *Material* option!</p>";
	}

	if (($length === "" AND $height === "") AND ($gableh === "" OR $gablew === "")) {
		return "<p id='error'>Please enter in a valid length & height (numbers only)!</p>";
	}

	if ($length === "" AND ($gablew === "" OR $gableh === "")) {
		return "<p id='error'>Please enter in a valid length (numbers only)!</p>";
	}

	if ($height === "" AND ($gablew === "" OR $gableh === "")) {
		return "<p id='error'>Please enter in a valid height (numbers only)!</p>";
	}

	// Separate product conversion factor from product price (in that order)
	$exp = explode("|",$product);

	/*
	*	$exp[0] is the product conversion factor
	*	$exp[1] is the product price
	*/

	$pc = $exp[0];
	$pp = $exp[1];
	
	// Calculate gable sq ft
	if ($gablew != "" AND $gableh !="") {
		$gable_one = $gablew * $gableh;
		$gable = $gable_one * .6;
	}

	if ($gablew == "" OR $gableh == "") {
		$gable = 0;
	}

	// Calculate wall sq ft
	if ($wd != "") {
		$sqft_one = ($height * $length) - $wd;
		$sqft = $sqft_one + $gable;
	} else {
		$sqft_one = $height * $length;
		$sqft = $sqft_one + $gable;
	}

	// Calculate conversion
	$add = $sqft / $pc;
	
	// Add .5 so it rounds correctly
	$convert = $add + .5;
	
	// Round converted ft to nearest whole number
	$round = round($convert, 0, PHP_ROUND_HALF_UP);

	// Times by 8 for linear feet
	$linear = $round * 8;

	// Price
	$rprice = $linear * $pp;

	// Round price
	
	$price = round($rprice, 2);

	// Put material and price into correct html
	$data = '<p id="mamount">'.$linear.' ft</p>'.'<p id="cost">$'.$price.'</p>';

	// Return the $data string
	return $data;
	
	}

	// --------------------------------------------------------------------

	/**
	 * Usage
	 *
	 * This function describes how the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */
	public static function usage()
	{
		ob_start();  ?>

		Calculates product amount and cost.

	<?php
		$buffer = ob_get_contents();
		ob_end_clean(); 

		return $buffer;
	}
	// END
}
/* End of file pi.calc.php */ 
/* Location: ./system/expressionengine/third_party/calc/pi.calc.php */