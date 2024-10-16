<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Indexing;
use Google_Service_Indexing_UrlNotification;
use Illuminate\Http\Request;

class IndexingController extends Controller
{
    protected $client;
    protected $service;

    public function __construct()
    {
        // Path to the JSON key file
        $jsonKeyFilePath = storage_path('app/credentials.json');

        // Create the client and authorize it using the service account
        $this->client = new Google_Client();
        $this->client->setAuthConfig($jsonKeyFilePath);
        $this->client->addScope(Google_Service_Indexing::INDEXING);

        // Initialize the Indexing API service
        $this->service = new Google_Service_Indexing($this->client);
    }

    public function indexUrl(Request $request)
    {
        $url = $request->input('url');

        $urlNotification = new Google_Service_Indexing_UrlNotification();
        $urlNotification->setUrl($url);
        $urlNotification->setType('URL_UPDATED');

        try {
            $response = $this->service->urlNotifications->publish($urlNotification);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteUrl(Request $request)
    {
        $url = $request->input('url');

        $urlNotification = new Google_Service_Indexing_UrlNotification();
        $urlNotification->setUrl($url);
        $urlNotification->setType('URL_DELETED');

        try {
            $response = $this->service->urlNotifications->publish($urlNotification);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getQuota()
    {
        try {
            // Get the current quota status
            $quotaStatus = $this->service->urlNotifications->getMetadata([]);
            return response()->json($quotaStatus);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
