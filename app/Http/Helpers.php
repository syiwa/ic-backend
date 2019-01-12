<?php
	
if(! function_exists("jsonResponse")){
	function jsonResponse($response, $code = 200){
		return response()->json(
			array_merge($response, [
				"_meta" => [
					"code" => $code,
				]
			]),200
		);
	}
}