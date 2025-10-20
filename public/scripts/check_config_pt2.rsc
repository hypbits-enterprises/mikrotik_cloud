
    
    :if ($successStatus = "true") do={
        :local domainStart ([:find $globalConfig "domain"]+9);
        :local domainEnd ([:find $globalConfig "router_id"]-3);
        :local domainConfig [:pick $globalConfig $domainStart $domainEnd];
        :put $domainConfig;
        
        #edit domain
        :local ffslash [:find $domainConfig "\\"];
        :local domainp1 [:pick $domainConfig 0 $ffslash];

        :local domain1 [:pick $domainConfig ($ffslash+1) [:len $domainConfig]]
        :local sfslash [:find $domain1 "\\"]
        :local domainp2 [:pick $domain1 0 $sfslash]

        :local domain2 [:pick $domain1 ($sfslash+1) [:len $domain1]]
        :put $domain2;
        
        :local fullDomain "$domainp1$domainp2$domain2"
        :put $fullDomain;
        
        #edit router id
        :local routerStart ([:find $globalConfig "router_id"]+11)
        :local routerEnd ([:find $globalConfig "account"]-2)
        :local routerIds [:pick $globalConfig $routerStart $routerEnd]
        :put $routerIds
        
        #edit account
        :local accStart ([:find $globalConfig "account"]+10)
        :local accEnd [:find $globalConfig "\"}"]
        :local accName [:pick $globalConfig $accStart $accEnd]
        :put $accName;
        
        :set $domain $fullDomain
        :set $routerId $routerIds
        :set $userAccount $accName
    }

    #delete the file
    :local f1 [/file find name="global_config.txt"];
    :if ([:len $f1] > 0) do={
        /file remove $f1
    }
}