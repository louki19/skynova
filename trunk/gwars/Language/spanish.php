<?php

include('spanishTechnologies.php');


function EchoString($ID)
{
	echo GetString($ID);
}

function GetString($ID)
{
	return $ID;
	switch ($ID) {
		default:
			return $ID;
	}
}

?>
