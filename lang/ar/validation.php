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

    'accepted' => ':attribute يجب ان يكون مقبول.',
    'accepted_if' => ':attribute يجب ان يكون مقبول عندما :other يكون :value.',
    'active_url' => ':attribute ليس رابط صحيح.',
    'after' => ':attribute يجب ان يكون تاريخ بعد :date.',
    'after_or_equal' => ':attribute يجب ان يكون تاريخ بعد او يساوي :date.',
    'alpha' => ':attribute يجب ان يحتوي على حروف فقط.',
    'alpha_dash' => ':attribute يجب ان يحتوي على حروف وارقام وشرطات وشرطات سفلية.',
    'alpha_num' => ':attribute يجب ان يحتوي على حروف وارقام فقط.',
    'array' => ':attribute يجب ان يكون مصفوفة.',
    'before' => ':attribute يجب ان يكون تاريخ قبل :date.',
    'before_or_equal' => ':attribute يجب ان يكون تاريخ قبل او يساوي :date.',
    'between' => [
        'array' => ':attribute يجب ان يحتوي على بين :min و :max عناصر.',
        'file' => ':attribute يجب ان يكون بين :min و :max كيلوبايت.',
        'numeric' => ':attribute يجب ان يكون بين :min و :max.',
        'string' => ':attribute يجب ان يكون بين :min و :max حروف.',
    ],
    'boolean' => ':attribute يجب ان يكون صحيح او خاطئ.',
    'confirmed' => ':attribute تاكيد غير صحيح.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => ':attribute ليس تاريخ صحيح.',
    'date_equals' => ':attribute يجب ان يكون تاريخ يساوي :date.',
    'date_format' => ':attribute لا يطابق الصيغة :format.',
    'declined' => ':attribute يجب ان يكون مرفوض.',
    'declined_if' => ':attribute يجب ان يكون مرفوض عندما :other يكون :value.',
    'different' => ':attribute و :other يجب ان يكون مختلفين.',
    'digits' => ':attribute يجب ان يكون :digits ارقام.',
    'digits_between' => ':attribute يجب ان يكون بين :min و :max ارقام.',
    'dimensions' => ':attribute لديه ابعاد صورة غير صحيحة.',
    'distinct' => ':attribute لديه قيمة مكررة.',
    'doesnt_end_with' => ':attribute لا يمكن ان ينتهي ب :values.',
    'doesnt_start_with' => ':attribute لا يمكن ان يبدا ب :values.',
    'email' => ':attribute يجب ان يكون بريد الكتروني صحيح.',
    'ends_with' => ':attribute يجب ان ينتهي ب :values.',
    'enum' => ':attribute يجب ان يكون :values.',
    'exists' => ':attribute يجب ان يكون موجود.',
    'file' => ':attribute يجب ان يكون ملف.',
    'filled' => ':attribute يجب ان يكون موجود.',
    'gt' => [
        'array' => ':attribute يجب ان يحتوي على اكثر من :value عنصر.',
        'file' => ':attribute يجب ان يكون اكبر من :value كيلوبايت.',
        'numeric' => ':attribute يجب ان يكون اكبر من :value.',
        'string' => ':attribute يجب ان يكون اكبر من :value حرف.',
    ],
    'gte' => [
        'array' => ':attribute يجب ان يحتوي على :value عنصر او اكثر.',
        'file' => ':attribute يجب ان يكون اكبر من او يساوي :value كيلوبايت.',
        'numeric' => ':attribute يجب ان يكون اكبر من او يساوي :value.',
        'string' => ':attribute يجب ان يكون اكبر من او يساوي :value حرف.',
    ],
    'image' => ':attribute يجب ان يكون صورة.',
    'in' => ':attribute المختار يجب ان يكون :values.',
    'in_array' => ':attribute المختار يجب ان يكون موجود في :other.',
    'integer' => ':attribute يجب ان يكون عدد صحيح.',
    'ip' => ':attribute يجب ان يكون عنوان IP صحيح.',
    'ipv4' => ':attribute يجب ان يكون عنوان IPv4 صحيح.',
    'ipv6' => ':attribute يجب ان يكون عنوان IPv6 صحيح.',
    'json' => ':attribute يجب ان يكون JSON صحيح.',
    'lt' => [
        'array' => ':attribute يجب ان يحتوي على اقل من :value عنصر.',
        'file' => ':attribute يجب ان يكون اقل من :value كيلوبايت.',
        'numeric' => ':attribute يجب ان يكون اقل من :value.',
        'string' => ':attribute يجب ان يكون اقل من :value حرف.',
    ],
    'lte' => [
        'array' => ':attribute يجب ان يحتوي على :value عنصر او اقل.',
        'file' => ':attribute يجب ان يكون اقل من او يساوي :value كيلوبايت.',
        'numeric' => ':attribute يجب ان يكون اقل من او يساوي :value.',
        'string' => ':attribute يجب ان يكون اقل من او يساوي :value حرف.',
    ],
    'mac_address' => ':attribute يجب ان يكون عنوان MAC صحيح.',
    'max' => [
        'array' => ':attribute يجب ان يحتوي على :max عنصر او اقل.',
        'file' => ':attribute يجب ان يكون اقل من او يساوي :max كيلوبايت.',
        'numeric' => ':attribute يجب ان يكون اقل من او يساوي :max.',
        'string' => ':attribute يجب ان يكون اقل من او يساوي :max حرف.',
    ],
    'max_digits' => ':attribute يجب ان يحتوي على :max رقم او اقل.',
    'mimes' => ':attribute يجب ان يكون ملف من نوع: :values.',
    'mimetypes' => ':attribute يجب ان يكون ملف من نوع: :values.',
    'min' => [
        'array' => ':attribute يجب ان يحتوي على :min عنصر او اكثر.',
        'file' => ':attribute يجب ان يكون اكبر من او يساوي :min كيلوبايت.',
        'numeric' => ':attribute يجب ان يكون اكبر من او يساوي :min.',
        'string' => ':attribute يجب ان يكون اكبر من او يساوي :min حرف.',
    ],
    'min_digits' => ':attribute يجب ان يحتوي على :min رقم او اكثر.',
    'multiple_of' => ':attribute يجب ان يكون من مضاعفات :value.',
    'not_in' => ':attribute المختار غير صحيح.',
    'not_regex' => ':attribute الصيغة غير صحيحة.',
    'numeric' => ':attribute يجب ان يكون رقم.',
    'password' => [
        'letters' => ':attribute يجب ان يحتوي على حرف واحد على الاقل.',
        'mixed' => ':attribute يجب ان يحتوي على حرف كبير وصغير.',
        'numbers' => ':attribute يجب ان يحتوي على رقم واحد على الاقل.',
        'symbols' => ':attribute يجب ان يحتوي على رمز واحد على الاقل.',
        'uncompromised' => ':attribute لقد ظهر في تسريب بيانات. يرجى اختيار :attribute مختلف.',
    ],
    'present' => ':attribute يجب ان يكون موجود.',
    'prohibited' => ':attribute يجب ان يكون محظور.',
    'prohibited_if' => ':attribute يجب ان يكون محظور عندما :other يكون :value.',
    'prohibited_unless' => ':attribute يجب ان يكون محظور ما لم يكن :other في :values.',
    'prohibits' => ':attribute يمنع وجود :other.',
    'regex' => ':attribute الصيغة غير صحيحة.',
    'required' => ':attribute يجب ان يكون موجود.',
    'required_array_keys' => ':attribute يجب ان يحتوي على :values.',
    'required_if' => ':attribute يجب ان يكون موجود عندما :other يكون :value.',
    'required_unless' => ':attribute يجب ان يكون موجود ما لم يكن :other في :values.',
    'required_with' => ':attribute يجب ان يكون موجود عندما :values يكون موجود.',
    'required_with_all' => ':attribute يجب ان يكون موجود عندما :values يكون موجود.',
    'required_without' => ':attribute يجب ان يكون موجود عندما :values يكون غير موجود.',
    'required_without_all' => ':attribute يجب ان يكون موجود عندما :values يكون غير موجود.',
    'same' => ':attribute و :other يجب ان يكونا متطابقين.',
    'size' => [
        'array' => ':attribute يجب ان يحتوي على :size عنصر.',
        'file' => ':attribute يجب ان يكون :size كيلوبايت.',
        'numeric' => ':attribute يجب ان يكون :size.',
        'string' => ':attribute يجب ان يكون :size حرف.',
    ],
    'starts_with' => ':attribute يجب ان يبدا ب :values.',
    'string' => ':attribute يجب ان يكون نص.',
    'timezone' => ':attribute يجب ان يكون منطقة زمنية صحيحة.',
    'unique' => ':attribute تم اخذه بالفعل.',
    'uploaded' => ':attribute فشل في الرفع.',
    'url' => ':attribute يجب ان يكون رابط صحيح.',
    'uuid' => ':attribute يجب ان يكون UUID صحيح.',

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
        'otp' => [
            'required' => 'رمز التحقق مطلوب.',
            'regex' => 'رمز التحقق يجب ان يكون رقم من 6 ارقام.',
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

    'attributes' => [
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'email' => 'البريد الإلكتروني',
        'name' => 'الاسم',
        'full_name' => 'الاسم الكامل',
        'username' => 'اسم المستخدم',
        'phone' => 'رقم الهاتف',
        'address' => 'العنوان',
        'city' => 'المدينة',
        'state' => 'الولاية',
        'zip' => 'الرمز البريدي',
        'country' => 'الدولة',
        'image' => 'الصورة',
        'status' => 'الحالة',
        'description' => 'الوصف',
        'value' => 'القيمة',
    ],

];
