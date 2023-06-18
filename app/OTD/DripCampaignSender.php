<?php

namespace App\OTD;

use App\WelcomeEmail;

class DripCampaignSender {

    public function __invoke()
    {
        $this->processDripCampaigns();
    }

    protected function processDripCampaigns()
    {
        $campaigns = WelcomeEmail::where('enabled', 1)->get();

        foreach ($campaigns as $campaign) {
            $campaign->send(); 
        }
    }

}