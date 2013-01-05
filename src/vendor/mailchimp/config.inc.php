<?
    //API Key - see http://admin.mailchimp.com/account/api or run login() once
    $apikey = '57ccf590899bb917d6033c92df8cbd98-us2';
        
    //the Username & Password you use to login to your MailChimp account
    $username = 'xennightz';
    $password = 'fripter22';
    
    // A List Id to run examples against. use lists() to view all
    // Also, login to MC account, go to List, then List Tools, and look for the List ID entry
    $listId = '4e79639456';
    
    // A Campaign Id to run examples against. use campaigns() to view all
    $campaignId = 'YOUR MAILCHIMP CAMPAIGN ID - see campaigns() method';

    //some email addresses used in the examples:
    $my_email = 'INVALID@example.org';
    $boss_man_email = 'INVALID@example.com';

    //just used in xml-rpc examples
    $apiUrl = 'http://api.mailchimp.com/1.2/';
