<?php
return [
    'redirect_uri'     =>  env('LINKEDIN_SHARE_REDIRECT_URI', ''),
    'client_id'        =>  env('LINKEDIN_SHARE_CLIENT_ID', ''),
    'client_secret'    =>  env('LINKEDIN_SHARE_CLIENT_SECRET', ''),
    'scopes'           =>  env('LINKEDIN_SHARE_SCOPES', ''),

    'company_id' => env('LINKEDIN_COMPANY_ID','')
];
