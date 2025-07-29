<?php

return [

    /*
     *
     * Shared translations.
     *
     */
    'title' => 'लारावेल स्थापक',
    'next' => 'अगला कदम',
    'back' => 'पिछला',
    'finish' => 'स्थापित करें',
    'forms' => [
        'errorTitle' => 'निम्नलिखित त्रुटियां हुई हैं:',
    ],


    /*
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => 'स्वागत',
        'title'   => 'लारावेल स्थापक',
        'message' => 'आसान स्थापना और सेटअप विजार्ड।',
        'next'    => 'आवश्यकताओं की जाँच करें',
    ],


    /*
     *
     * Requirements page translations.
     *
     */
    'requirements' => [
        'templateTitle' => 'स्टेप 1 | सर्वर की आवश्यकताएँ',
        'title' => 'सर्वर की आवश्यकताएँ',
        'next'    => 'अनुमतियों की जाँच करें',
    ],


    /*
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'templateTitle' => 'स्टेप 2 | अनुमतियाँ',
        'title' => 'अनुमतियाँ',
        'next' => 'पर्यावरण को कॉन्फ़िगर करें',
    ],


    /*
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'menu' => [
                'templateTitle' => 'स्टेप 3 | पर्यावरण सेटिंग्स',
                'title' => 'पर्यावरण सेटिंग्स',
                'desc' => 'कृपया चुनें कि आप कैसे ऐप का <code>.env</code> फ़ाइल कॉन्फ़िगर करना चाहते हैं।',
                'wizard-button' => 'फ़ॉर्म विज़ार्ड सेटअप',
                'classic-button' => 'क्लासिक पाठ संपादक',
            ],

        'wizard' => [
                'templateTitle' => 'स्टेप 3 | पर्यावरण सेटिंग्स | मार्गदर्शित विजार्ड',
                'title' => 'मार्गदर्शित <code>.env</code> विजार्ड',
                'tabs' => [
                    'environment' => 'पर्यावरण',
                    'database' => 'डेटाबेस',
                    'application' => 'एप्लिकेशन',
                ],
            ],

            'form' => [
                'name_required' => 'पर्यावरण नाम की आवश्यकता है।',
                'app_name_label' => 'ऐप का नाम',
                'app_name_placeholder' => 'ऐप का नाम',
                'app_environment_label' => 'ऐप पर्यावरण',
                'app_environment_label_local' => 'स्थानीय',
                'app_environment_label_developement' => 'विकास',
                'app_environment_label_qa' => 'क्यूए',
                'app_environment_label_production' => 'उत्पादन',
                'app_environment_label_other' => 'अन्य',
                'app_environment_placeholder_other' => 'अपने पर्यावरण डालें...',
                'app_debug_label' => 'ऐप डीबग',
                'app_debug_label_true' => 'सही',
                'app_debug_label_false' => 'गलत',
                'app_log_level_label' => 'ऐप लॉग स्तर',
                'app_log_level_label_debug' => 'डीबग',
                'app_log_level_label_info' => 'जानकारी',
                'app_log_level_label_notice' => 'नोटिस',
                'app_log_level_label_warning' => 'चेतावनी',
                'app_log_level_label_error' => 'त्रुटि',
                'app_log_level_label_critical' => 'महत्वपूर्ण',
                'app_log_level_label_alert' => 'अलर्ट',
                'app_log_level_label_emergency' => 'आपात',
                'app_url_label' => 'ऐप Url',
                'app_url_placeholder' => 'ऐप Url',
                'db_connection_failed' => 'डेटाबेस से कनेक्ट करने में असमर्थ।',
                'db_connection_label' => 'डेटाबेस कनेक्शन',
                'db_connection_label_mysql' => 'मायस्क्यूएल',
                'db_connection_label_sqlite' => 'एसक्यूएलाइट',
                'db_connection_label_pgsql' => 'पीजीसीएल',
                'db_connection_label_sqlsrv' => 'एसक्यूएलसरवर',
                'db_host_label' => 'डेटाबेस होस्ट',
                'db_host_placeholder' => 'डेटाबेस होस्ट',
                'db_port_label' => 'डेटाबेस पोर्ट',
                'db_port_placeholder' => 'डेटाबेस पोर्ट',
                'db_name_label' => 'डेटाबेस का नाम',
                'db_name_placeholder' => 'डेटाबेस का नाम',
                'db_username_label' => 'डेटाबेस उपयोगकर्ता नाम',
                'db_username_placeholder' => 'डेटाबेस उपयोगकर्ता नाम',
                'db_password_label' => 'डेटाबेस पासवर्ड',
                'db_password_placeholder' => 'डेटाबेस पासवर्ड',


                'app_tabs' => [
                    'more_info' => 'अधिक जानकारी',
                    'broadcasting_title' => 'ब्रॉडकास्टिंग, कैशिंग, सत्र, और कतार',
                    'broadcasting_label' => 'ब्रॉडकास्ट ड्राइवर',
                    'broadcasting_placeholder' => 'ब्रॉडकास्ट ड्राइवर',
                    'cache_label' => 'कैश ड्राइवर',
                    'cache_placeholder' => 'कैश ड्राइवर',
                    'session_label' => 'सत्र ड्राइवर',
                    'session_placeholder' => 'सत्र ड्राइवर',
                    'queue_label' => 'कतार ड्राइवर',
                    'queue_placeholder' => 'कतार ड्राइवर',
                    'redis_label' => 'रेडिस ड्राइवर',
                    'redis_host' => 'रेडिस होस्ट',
                    'redis_password' => 'रेडिस पासवर्ड',
                    'redis_port' => 'रेडिस पोर्ट',

                    'mail_label' => 'मेल',
                    'mail_driver_label' => 'मेल ड्राइवर',
                    'mail_driver_placeholder' => 'मेल ड्राइवर',
                    'mail_host_label' => 'मेल होस्ट',
                    'mail_host_placeholder' => 'मेल होस्ट',
                    'mail_port_label' => 'मेल पोर्ट',
                    'mail_port_placeholder' => 'मेल पोर्ट',
                    'mail_username_label' => 'मेल उपयोगकर्ता नाम',
                    'mail_username_placeholder' => 'मेल उपयोगकर्ता नाम',
                    'mail_password_label' => 'मेल पासवर्ड',
                    'mail_password_placeholder' => 'मेल पासवर्ड',
                    'mail_encryption_label' => 'मेल एन्क्रिप्शन',
                    'mail_encryption_placeholder' => 'मेल एन्क्रिप्शन',

                    'pusher_label' => 'पुशर',
                    'pusher_app_id_label' => 'पुशर एप्लिकेशन आईडी',
                    'pusher_app_id_palceholder' => 'पुशर एप्लिकेशन आईडी',
                    'pusher_app_key_label' => 'पुशर एप्लिकेशन कुंजी',
                    'pusher_app_key_palceholder' => 'पुशर एप्लिकेशन कुंजी',
                    'pusher_app_secret_label' => 'पुशर एप्लिकेशन सीक्रेट',
                    'pusher_app_secret_palceholder' => 'पुशर एप्लिकेशन सीक्रेट',

                ],
                'buttons' => [
                    'setup_database' => 'डेटाबेस सेटअप करें',
                    'setup_application' => 'ऐप्लिकेशन सेटअप करें',
                    'install' => 'स्थापित करें',
                ],

            ],
        ],
        'classic' => [
                'templateTitle' => 'स्टेप 3 | पर्यावरण सेटिंग्स | क्लासिक संपादक',
                'title' => 'क्लासिक पर्यावरण संपादक',
                'save' => '.env फ़ाइल को सहेजें',
                'back' => 'फ़ॉर्म विज़ार्ड का उपयोग करें',
                'install' => 'सहेजें और स्थापित करें',
            ],
            'success' => 'आपकी .env फ़ाइल की सेटिंग्स सहेज ली गई हैं।',
            'errors' => '.env फ़ाइल को सहेजने में असमर्थ, कृपया इसे मैन्युअल रूप से बनाएं।',



   'install' => 'स्थापित करें',


    /*
     *
     * Installed Log translations.
     *
     */
    'installed' => [
        'success_log_message' => 'लारावेल स्थापक को सफलतापूर्वक स्थापित किया गया ',
    ],


    /*
     *
     * Final page translations.
     *
     */
    'final' => [
        'title' => 'स्थापना समाप्त',
        'templateTitle' => 'स्थापना समाप्त',
        'finished' => 'ऐप्लिकेशन को सफलतापूर्वक स्थापित किया गया है।',
        'migration' => 'विवाह और बीज कंसोल आउटपुट:',
        'console' => 'ऐप्लिकेशन कंसोल आउटपुट:',
        'log' => 'स्थापना लॉग प्रविष्टि:',
        'env' => 'अंतिम .env फ़ाइल:',
        'exit' => 'निकासी के लिए यहाँ क्लिक करें',
    ],


    /*
     *
     * Update specific translations
     *
     */
    'updater' => [
        /*
         *
         * Shared translations.
         *
         */
        'title' => 'लारावेल अपडेटर',

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'welcome' => [
            'title'   => 'अपडेटर में आपका स्वागत है',
            'message' => 'अपडेट विजार्ड में आपका स्वागत है।',
        ],

        /*
         *
         * Welcome page translations for update feature.
         *
         */
       'overview' => [
            'title'   => 'अवलोकन',
            'message' => 'एक अपडेट है।|:number अपडेट हैं।',
            'install_updates' => 'अपडेट स्थापित करें',
        ],

        /*
         *
         * Final page translations.
         *
         */

        'final' => [
            'title' => 'समाप्त',
            'finished' => 'ऐप्लिकेशन का डेटाबेस सफलतापूर्वक अपडेट हो गया है।',
            'exit' => 'निकासी के लिए यहाँ क्लिक करें',
        ],

        'log' => [
            'success_message' => 'लारावेल स्थापक को सफलतापूर्वक अपडेट किया गया ',
        ],
    ],
];
