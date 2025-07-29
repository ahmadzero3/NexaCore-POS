<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute फ़ील्ड को स्वीकार करना होगा।',
    'accepted_if' => ':other के होने पर :attribute फ़ील्ड को स्वीकार करना होगा :value.',
    'active_url' => ':attribute फ़ील्ड में मान्य URL डालना होगा।',
    'after' => ':attribute फ़ील्ड में कोई तारीख डालना होगी :date के बाद।',
    'after_or_equal' => ':attribute फ़ील्ड में कोई तारीख डालना होगी :date से बेहतर या समान तारीख के बाद।',
    'alpha' => ':attribute फ़ील्ड में केवल अक्षर हो सकते हैं।',
    'alpha_dash' => ':attribute फ़ील्ड में केवल अक्षर, संख्या, डैश और अंडरस्कोर हो सकते हैं।',
    'alpha_num' => ':attribute फ़ील्ड में केवल अक्षर और संख्या हो सकते हैं।',
    'array' => ':attribute फ़ील्ड एक सभ्य होना चाहिए।',
    'ascii' => ':attribute फ़ील्ड में केवल एक्सीले वाले एलफ़बेट चिह्न और चिह्न हो सकते हैं।',
    'before' => ':attribute फ़ील्ड में कोई तारीख डालना होगी :date के पहले।',
    'before_or_equal' => ':attribute फ़ील्ड में कोई तारीख डालना होगी :date से कम या समान तारीख के पहले।',
    'between' => [
        'array' => ':attribute फ़ील्ड में :min और :max आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड में :min और :max किलोबाइट होने चाहिए।',
        'numeric' => ':attribute फ़ील्ड में :min और :max के बीच होने चाहिए।',
        'string' => ':attribute फ़ील्ड में :min और :max वर्ण होने चाहिए।',
    ],
    'boolean' => ':attribute फ़ील्ड true या false होना चाहिए।',
    'confirmed' => ':attribute फ़ील्ड पुष्टि से मेल नहीं खाती है।',
    'current_password' => 'पासवर्ड गलत है।',
    'date' => ':attribute फ़ील्ड एक मान्य तिथि होना चाहिए।',
    'date_equals' => ':attribute फ़ील्ड :date के बराबर तिथि होना चाहिए।',
    'date_format' => ':attribute फ़ील्ड :format प्रारूप से मेल खाना चाहिए।',
    'decimal' => ':attribute फ़ील्ड में :decimal दशमलव स्थान होने चाहिए।',
    'declined' => ':attribute फ़ील्ड अस्वीकृत होना चाहिए।',
    'declined_if' => ':other :value होने पर :attribute फ़ील्ड अस्वीकृत होना चाहिए।',
    'different' => ':attribute फ़ील्ड और :other अलग-अलग होना चाहिए।',
    'digits' => ':attribute फ़ील्ड में :digits अंक होने चाहिए।',
    'digits_between' => ':attribute फ़ील्ड में :min और :max अंकों के बीच होना चाहिए।',
    'dimensions' => ':attribute फ़ील्ड में अमान्य छवि आयाम हैं।',
    'distinct' => ':attribute फ़ील्ड में एक डुप्लिकेट मान है।',
    'doesnt_end_with' => ':attribute फ़ील्ड निम्न में से किसी एक के साथ समाप्त नहीं होनी चाहिए: :values.',
    'doesnt_start_with' => ':attribute फ़ील्ड निम्न में से किसी एक के साथ शुरू नहीं होनी चाहिए: :values.',
    'email' => ':attribute फ़ील्ड एक मान्य ईमेल पता होना चाहिए।',
    'ends_with' => ':attribute फ़ील्ड निम्न में से किसी एक के साथ समाप्त होनी चाहिए: :values.',
    'enum' => 'चयनित :attribute अमान्य है।',
    'exists' => 'चयनित :attribute अमान्य है।',
    'file' => ':attribute फ़ील्ड एक फ़ाइल होनी चाहिए।',
    'filled' => ':attribute फ़ील्ड में एक मान होना चाहिए।',
    'gt' => [
        'array' => ':attribute फ़ील्ड में :value से अधिक आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड :value किलोबाइट से अधिक होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value से अधिक होनी चाहिए।',
        'string' => ':attribute फ़ील्ड :value वर्णों से अधिक होनी चाहिए।',
    ],
    'gte' => [
        'array' => ':attribute फ़ील्ड में :value आइटम या अधिक होने चाहिए।',
        'file' => ':attribute फ़ील्ड :value किलोबाइट या अधिक होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value या अधिक होनी चाहिए।',
        'string' => ':attribute फ़ील्ड :value वर्णों या अधिक होनी चाहिए।',
    ],
    'image' => ':attribute फ़ील्ड एक छवि होनी चाहिए।',
    'in' => 'चयनित :attribute अमान्य है।',
    'in_array' => ':attribute फ़ील्ड :other में मौजूद होनी चाहिए।',
    'integer' => ':attribute फ़ील्ड एक पूर्णांक होना चाहिए।',
    'ip' => ':attribute फ़ील्ड एक मान्य IP पता होना चाहिए।',
    'ipv4' => ':attribute फ़ील्ड एक मान्य IPv4 पता होना चाहिए।',
    'ipv6' => ':attribute फ़ील्ड एक मान्य IPv6 पता होना चाहिए।',
    'json' => ':attribute फ़ील्ड एक मान्य JSON स्ट्रिंग होना चाहिए।',
    'lowercase' => ':attribute फ़ील्ड लोअरकेस में होनी चाहिए।',
    'lt' => [
        'array' => ':attribute फ़ील्ड में :value से कम आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड :value किलोबाइट से कम होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value से कम होनी चाहिए।',
        'string' => ':attribute फ़ील्ड :value वर्णों से कम होनी चाहिए।',
    ],
    'lte' => [
        'array' => ':attribute फ़ील्ड में :value से अधिक आइटम नहीं होने चाहिए।',
        'file' => ':attribute फ़ील्ड :value किलोबाइट या कम होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :value या कम होनी चाहिए।',
        'string' => ':attribute फ़ील्ड :value वर्णों या कम होनी चाहिए।',
    ],
    'mac_address' => ':attribute फ़ील्ड एक मान्य MAC पता होना चाहिए।',
    'max' => [
        'array' => ':attribute फ़ील्ड में :max से अधिक आइटम नहीं होने चाहिए।',
        'file' => ':attribute फ़ील्ड :max किलोबाइट से अधिक नहीं होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :max से अधिक नहीं होनी चाहिए।',
        'string' => ':attribute फ़ील्ड :max वर्णों से अधिक नहीं होनी चाहिए।',
    ],
    'max_digits' => ':attribute फ़ील्ड में :max से अधिक अंक नहीं होने चाहिए।',
   'mimes' => ':attribute फ़ील्ड :values प्रकार की फ़ाइल होनी चाहिए।',
   'mimetypes' => ':attribute फ़ील्ड :values प्रकार की फ़ाइल होनी चाहिए।',
   'min' => [
        'array' => ':attribute फ़ील्ड में कम से कम :min आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड कम से कम :min किलोबाइट होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड कम से कम :min होना चाहिए।',
        'string' => ':attribute फ़ील्ड कम से कम :min वर्ण होना चाहिए।',
   ],
   'min_digits' => ':attribute फ़ील्ड में कम से कम :min अंक होने चाहिए।',
   'missing' => ':attribute फ़ील्ड अनुपस्थित होनी चाहिए।',
   'missing_if' => ':other :value होने पर :attribute फ़ील्ड अनुपस्थित होनी चाहिए।',
   'missing_unless' => ':other :value में न होने पर :attribute फ़ील्ड अनुपस्थित होनी चाहिए।',
   'missing_with' => ':values मौजूद होने पर :attribute फ़ील्ड अनुपस्थित होनी चाहिए।',
   'missing_with_all' => ':values मौजूद होने पर :attribute फ़ील्ड अनुपस्थित होनी चाहिए।',
   'multiple_of' => ':attribute फ़ील्ड :value का गुणक होना चाहिए।',
   'not_in' => 'चयनित :attribute अमान्य है।',
   'not_regex' => ':attribute फ़ील्ड का प्रारूप अमान्य है।',
   'numeric' => ':attribute फ़ील्ड एक संख्या होनी चाहिए।',
   'password' => [
        'letters' => ':attribute फ़ील्ड में कम से कम एक अक्षर होना चाहिए।',
        'mixed' => ':attribute फ़ील्ड में कम से कम एक अपरकेस और एक लोअरकेस अक्षर होना चाहिए।',
        'numbers' => ':attribute फ़ील्ड में कम से कम एक संख्या होनी चाहिए।',
        'symbols' => ':attribute फ़ील्ड में कम से कम एक प्रतीक होना चाहिए।',
        'uncompromised' => 'दी गई :attribute डेटा लीक में प्रकट हुई है। कृपया एक भिन्न :attribute चुनें।',
   ],
   'present' => ':attribute फ़ील्ड मौजूद होनी चाहिए।',
   'prohibited' => ':attribute फ़ील्ड प्रतिबंधित है।',
   'prohibited_if' => ':other :value होने पर :attribute फ़ील्ड प्रतिबंधित है।',
   'prohibited_unless' => ':other :values में न होने पर :attribute फ़ील्ड प्रतिबंधित है।',
   'prohibits' => ':attribute फ़ील्ड :other को मौजूद होने से रोकती है।',
   'regex' => ':attribute फ़ील्ड का प्रारूप अमान्य है।',
   'required' => ':attribute फ़ील्ड आवश्यक है।',
   'required_array_keys' => ':attribute फ़ील्ड में :values के लिए प्रविष्टियाँ होनी चाहिए।',
   'required_if' => ':other :value होने पर :attribute फ़ील्ड आवश्यक है।',
   'required_if_accepted' => ':other स्वीकृत होने पर :attribute फ़ील्ड आवश्यक है।',
    'required_unless' => ':other :values में न होने पर :attribute फ़ील्ड आवश्यक है।',
   'required_with' => ':values मौजूद होने पर :attribute फ़ील्ड आवश्यक है।',
   'required_with_all' => ':values मौजूद होने पर :attribute फ़ील्ड आवश्यक है।',
   'required_without' => ':values मौजूद न होने पर :attribute फ़ील्ड आवश्यक है।',
   'required_without_all' => ':values में से कोई भी मौजूद न होने पर :attribute फ़ील्ड आवश्यक है।',
   'same' => ':attribute फ़ील्ड :other से मेल खाना चाहिए।',
   'size' => [
        'array' => ':attribute फ़ील्ड में :size आइटम होने चाहिए।',
        'file' => ':attribute फ़ील्ड :size किलोबाइट होनी चाहिए।',
        'numeric' => ':attribute फ़ील्ड :size होना चाहिए।',
        'string' => ':attribute फ़ील्ड :size वर्ण होना चाहिए।',
   ],
   'starts_with' => ':attribute फ़ील्ड निम्नलिखित में से एक से शुरू होनी चाहिए: :values।',
   'string' => ':attribute फ़ील्ड एक स्ट्रिंग होनी चाहिए।',
   'timezone' => ':attribute फ़ील्ड एक मान्य समय क्षेत्र होना चाहिए।',
   'unique' => ':attribute पहले ही ले ली गई है।',
   'uploaded' => ':attribute अपलोड करने में विफल रहा।',
   'uppercase' => ':attribute फ़ील्ड अपरकेस में होनी चाहिए।',
   'url' => ':attribute फ़ील्ड एक मान्य URL होना चाहिए।',
   'ulid' => ':attribute फ़ील्ड एक मान्य ULID होना चाहिए।',
   'uuid' => ':attribute फ़ील्ड एक मान्य UUID होना चाहिए।',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
