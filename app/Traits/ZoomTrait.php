<?php

namespace App\Traits;

use Auth;
use DateTime;
use datetimezone;
use Carbon\Carbon;

trait ZoomTrait
{
    private function generateZoomToken()
	{
	    $key = config('otd.zoom_api_key', '');
	    $secret = config('otd.zoom_api_secret', '');
	    $payload = [
	        'iss' => $key,
	        'exp' => strtotime('+1 minute'),
	    ];
	    return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
	}

	private function retrieveZoomUrl()
	{
	    return config('otd.zoom_api_url', '');
	}

	private function zoomRequest()
	{
	    $jwt = $this->generateZoomToken();
	    return \Illuminate\Support\Facades\Http::withHeaders([
	        'authorization' => 'Bearer ' . $jwt,
	        'content-type' => 'application/json',
	    ]);
	}

	public function zoomGet(string $path, array $query = [])
	{
	    $url = $this->retrieveZoomUrl();
	    $request = $this->zoomRequest();
	    return $request->get($url . $path, $query);
	}

	public function zoomPost(string $path, $body = [])
	{
	    $url = $this->retrieveZoomUrl();
	    $request = $this->zoomRequest();
	    return $request->post($url . $path, $body);
	}

	public function zoomPatch(string $path, array $body = [])
	{
	    $url = $this->retrieveZoomUrl();
	    $request = $this->zoomRequest();
	    return $request->patch($url . $path, $body);
	}

	public function zoomDelete(string $path, array $body = [])
	{
	    $url = $this->retrieveZoomUrl();
	    $request = $this->zoomRequest();
	    return $request->delete($url . $path, $body);
	}

	public function toZoomTimeFormat(string $dateTime)
	{
	    try {
	        $date = new \DateTime($dateTime);
	        return $date->format('Y-m-d\TH:i:s');
	    } catch(\Exception $e) {
	        Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());
	        return '';
	    }
	}

	public function toUnixTimeStamp(string $dateTime, string $timezone)
	{
	    try {
	        $date = new \DateTime($dateTime, new \DateTimeZone($timezone));
	        return $date->getTimestamp();
	    } catch (\Exception $e) {
	        Log::error('ZoomJWT->toUnixTimeStamp : ' . $e->getMessage());
	        return '';
	    }
	}
}