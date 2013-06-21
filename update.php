<?php

include( "settings.php" );

$file_data = file_get_contents( $filename );
$decoded_json = json_decode( $file_data, true );

$datapoints = $decoded_json["graph"]["datasequences"][0]["datapoints"];

if ( !count($datapoints) ) {
	for ($offset = 6; $offset >= 0; $offset--) {
		$temp_data = array(
			"title" => date( "l", strtotime("-".$offset." days") ),
			"value" => 0
		);

		array_push( $datapoints, $temp_data );
	}
}

$day_of_week = date( "l" );
if ( $type == "total" ) {
	$number_of_things = exec( "osascript -e 'tell application \"Things\"' -e 'return count every to do whose status is open' -e 'end tell'" );	
} else {
	$number_of_things = exec( "osascript -e 'tell application \"Things\"' -e 'return count to dos of list \"Today\" whose status is open' -e 'end tell'" );
}

$new_record = array(
	"title" => $day_of_week,
	"value" => $number_of_things
);

$last_record = end( $datapoints );
if ( $last_record["title"] == $day_of_week ) {
	array_pop( $datapoints );
} else {
	array_shift( $datapoints );
}

array_push( $datapoints, $new_record );

if ( $datapoints != $decoded_json["graph"]["datasequences"][0]["datapoints"] ) {
	$decoded_json["graph"]["datasequences"][0]["datapoints"] = $datapoints;
    file_put_contents( $filename, json_encode($decoded_json) );
}
