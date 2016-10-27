<?
//purpose: to check if a variable is not an empty string
function isFilled($obj)
{
	if ($obj == "")
	{
		return false;
	}
	return true;
}

//purpose: to check if a variable is a valid email
function isEmail($obj){
	if (!ereg("[a-zA-Z0-9_\.\-\+\#\%]+@[a-zA-Z0-9_\.\-]+\.[a-zA-Z]{2,3}", $obj))
	{
		return false;
	}
	return true;
}

// purpose: to check if it is a numeric value
function isNumeric($obj)
{
	if (!ereg("[0-9]{1,255}", $obj))
	{
		return false;
	}
	return true;
}

function isAlphabetic($obj, $ignoreWhiteSpace) 
{
	if (($ignoreWhiteSpace && (!ereg("[^a-zA-Z\s]", $obj)) != -1) || (!$ignoreWhiteSpace && (!ereg("[^a-zA-Z]", $obj)) != -1))
	{
			return false;
	}
	return true;
}
?>