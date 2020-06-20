<?php
$svr=$_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'];
return [
    'adminEmail' => 'admin@example.com',
    'timeZone'=>'America/New_York',
	//rootUrl no slash at end
    'rootUrl'=>$svr,
    'stickerPre'=>'',
    'conf'=> [
        'offer'=>'50',
    ],
    'maskMoneyOptions' => [
        'prefix' => 'US $',
        'suffix' => '',
        'affixesStay' => true,
        'thousands' => ',',
        'decimal' => '.',
        'precision' => 2, 
        'allowZero' => false,
        'allowNegative' => false,
    ]
];