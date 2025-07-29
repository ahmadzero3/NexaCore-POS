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

    'accepted' => 'يجب قبول حقل :attribute.',
    'accepted_if' => 'يجب قبول حقل :attribute عندما يكون :other هو :value.',
    'active_url' => 'يجب أن يكون حقل :attribute عنوان URL صحيحًا.',
    'after' => 'يجب أن يكون حقل :attribute تاريخًا بعد :date.',
    'after_or_equal' => 'يجب أن يكون حقل :attribute تاريخًا بعد أو يساوي :date.',
    'alpha' => 'يجب أن يحتوي حقل :attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام وشرطات وشرطات تحتية فقط.',
    'alpha_num' => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام فقط.',
    'array' => 'يجب أن يكون حقل :attribute مصفوفة.',
    'ascii' => 'يجب أن يحتوي حقل :attribute على أحرف أبجدية فقط من نوع البايت الواحد ورموز.',
    'before' => 'يجب أن يكون حقل :attribute تاريخًا قبل :date.',
    'before_or_equal' => 'يجب أن يكون حقل :attribute تاريخًا قبل أو يساوي :date.',
    'between' => [
        'array' => 'يجب أن يحتوي حقل :attribute على بين :min و :max عنصرًا.',
        'file' => 'يجب أن يكون حجم حقل :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute بين :min و :max.',
        'string' => 'يجب أن يكون حقل :attribute بين :min و :max حرفًا.',
    ],
    'boolean' => 'يجب أن يكون حقل :attribute صحيحًا أو خاطئًا.',
    'confirmed' => 'تأكيد حقل :attribute غير مطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'يجب أن يكون حقل :attribute تاريخًا صحيحًا.',
    'date_equals' => 'يجب أن يكون حقل :attribute تاريخًا مساويًا لـ :date.',
    'date_format' => 'يجب أن يتطابق حقل :attribute مع التنسيق :format.',
    'decimal' => 'يجب أن يحتوي حقل :attribute على :decimal أرقام عشرية.',
    'declined' => 'يجب أن يكون حقل :attribute مرفوضًا.',
    'declined_if' => 'يجب أن يكون حقل :attribute مرفوضًا عندما يكون :other هو :value.',
    'different' => 'يجب أن يكون حقل :attribute و :other مختلفين.',
    'digits' => 'يجب أن يكون حقل :attribute :digits أرقام.',
    'digits_between' => 'يجب أن يكون حقل :attribute بين :min و :max أرقام.',
    'dimensions' => 'الصورة في حقل :attribute بها أبعاد صورة غير صالحة.',
    'distinct' => 'يحتوي حقل :attribute على قيمة مكررة.',
    'doesnt_end_with' => 'يجب أن لا ينتهي حقل :attribute بأحد القيم التالية: :values.',
    'doesnt_start_with' => 'يجب أن لا يبدأ حقل :attribute بأحد القيم التالية: :values.',
    'email' => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صالحًا.',
    'ends_with' => 'يجب أن ينتهي حقل :attribute بأحد القيم التالية: :values.',
    'enum' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'exists' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'file' => 'يجب أن يكون حقل :attribute ملفًا.',
    'filled' => 'يجب أن يحتوي حقل :attribute على قيمة.',

    'gt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أكثر من :value عنصرًا.',
        'file' => 'يجب أن يكون حجم حقل :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أكبر من :value.',
        'string' => 'يجب أن يكون حقل :attribute أكبر من :value حرفًا.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :value عنصرًا أو أكثر.',
        'file' => 'يجب أن يكون حجم حقل :attribute أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أكبر من أو يساوي :value.',
        'string' => 'يجب أن يكون حقل :attribute أكبر من أو يساوي :value حرفًا.',
    ],
    'image' => 'يجب أن يكون حقل :attribute صورة.',
    'in' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'in_array' => 'يجب أن يكون حقل :attribute موجودًا في :other.',
    'integer' => 'يجب أن يكون حقل :attribute عددًا صحيحًا.',
    'ip' => 'يجب أن يكون حقل :attribute عنوان IP صالحًا.',
    'ipv4' => 'يجب أن يكون حقل :attribute عنوان IPv4 صالحًا.',
    'ipv6' => 'يجب أن يكون حقل :attribute عنوان IPv6 صالحًا.',
    'json' => 'يجب أن يكون حقل :attribute سلسلة JSON صالحة.',
    'lowercase' => 'يجب أن يكون حقل :attribute حروفه في حالة صغيرة.',
    'lt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أقل من :value عنصرًا.',
        'file' => 'يجب أن يكون حجم حقل :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أقل من :value.',
        'string' => 'يجب أن يكون حقل :attribute أقل من :value حرفًا.',
    ],
    'lte' => [
        'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :value عنصر.',
        'file' => 'يجب أن يكون حجم حقل :attribute أقل من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute أقل من أو يساوي :value.',
        'string' => 'يجب أن يكون حقل :attribute أقل من أو يساوي :value حرفًا.',
    ],
    'mac_address' => 'يجب أن يكون حقل :attribute عنوان MAC صالحًا.',
    'max' => [
        'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :max عنصر.',
        'file' => 'يجب ألا يكون حجم حقل :attribute أكبر من :max كيلوبايت.',
        'numeric' => 'يجب ألا يكون حقل :attribute أكبر من :max.',
        'string' => 'يجب ألا يكون حقل :attribute أكبر من :max حرفًا.',
    ],

    'max_digits' => 'يجب ألا يحتوي حقل :attribute على أكثر من :max أرقام.',
    'mimes' => 'يجب أن يكون حقل :attribute ملف من النوع: :values.',
    'mimetypes' => 'يجب أن يكون حقل :attribute ملفًا من النوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي حقل :attribute على الأقل على :min عنصر.',
        'file' => 'يجب أن يكون حجم حقل :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute على الأقل :min.',
        'string' => 'يجب أن يكون حقل :attribute على الأقل :min حرفًا.',
    ],
    'min_digits' => 'يجب أن يحتوي حقل :attribute على الأقل على :min أرقام.',
    'missing' => 'يجب أن يكون حقل :attribute مفقودًا.',
    'missing_if' => 'يجب أن يكون حقل :attribute مفقودًا عندما يكون :other هو :value.',
    'missing_unless' => 'يجب أن يكون حقل :attribute مفقودًا ما لم يكن :other هو :value.',
    'missing_with' => 'يجب أن يكون حقل :attribute مفقودًا عندما يكون :values موجودًا.',
    'missing_with_all' => 'يجب أن يكون حقل :attribute مفقودًا عندما تكون :values موجودة.',
    'multiple_of' => 'يجب أن يكون حقل :attribute مضاعفًا لـ :value.',
    'not_in' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'not_regex' => 'تنسيق حقل :attribute غير صالح.',
    'numeric' => 'يجب أن يكون حقل :attribute رقمًا.',
    'password' => [
        'letters' => 'يجب أن يحتوي حقل :attribute على الأقل على حرف واحد.',
        'mixed' => 'يجب أن يحتوي حقل :attribute على الأقل على حرف كبير وحرف صغير واحد.',
        'numbers' => 'يجب أن يحتوي حقل :attribute على الأقل على رقم واحد.',
        'symbols' => 'يجب أن يحتوي حقل :attribute على الأقل على رمز واحد.',
        'uncompromised' => 'تم استخدام :attribute المعطى في تسريب بيانات. يرجى اختيار :attribute مختلفًا.',
    ],
    'present' => 'يجب أن يكون حقل :attribute موجودًا.',
    'prohibited' => 'حقل :attribute ممنوع.',
    'prohibited_if' => 'حقل :attribute ممنوع عندما يكون :other هو :value.',
    'prohibited_unless' => 'حقل :attribute ممنوع ما لم يكن :other في :values.',
    'prohibits' => 'حقل :attribute يمنع وجود :other.',
    'regex' => 'تنسيق حقل :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي حقل :attribute على مدخلات لـ: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عند قبول :other.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other في :values.',
    'required_with' => 'حقل :attribute مطلوب عند وجود :values.',
    'required_with_all' => 'حقل :attribute مطلوب عند وجود :values.',
    'required_without' => 'حقل :attribute مطلوب عند عدم وجود :values.',
    'required_without_all' => 'حقل :attribute مطلوب عند عدم وجود أيًا من :values.',
    'same' => 'يجب أن يتطابق حقل :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :size عنصرًا.',
        'file' => 'يجب أن يكون حجم حقل :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن يكون حقل :attribute :size.',
        'string' => 'يجب أن يكون حقل :attribute :size حرفًا.',
    ],
    'starts_with' => 'يجب أن يبدأ حقل :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون حقل :attribute نصًا.',
    'timezone' => 'يجب أن يكون حقل :attribute منطقة زمنية صالحة.',
    'unique' => 'تم أخذ :attribute بالفعل.',
    'uploaded' => 'فشل تحميل :attribute.',
    'uppercase' => 'يجب أن يكون حقل :attribute بالحروف الكبيرة.',
    'url' => 'يجب أن يكون حقل :attribute عنوان URL صالحًا.',
    'ulid' => 'يجب أن يكون حقل :attribute ULID صالحًا.',
    'uuid' => 'يجب أن يكون حقل :attribute UUID صالحًا.',


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
