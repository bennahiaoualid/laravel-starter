<?php

return [
    'title' => 'إعدادات النظام',
    'subtitle' => 'إدارة معلومات الشركة',

    'categories' => [
        'company_info' => [
            'title' => 'معلومات الشركة',
            'description' => 'تحديث معلومات الشركة',
        ],
    ],

    'company_info' => [
        'company_name' => [
            'name' => 'اسم الشركة',
            'description' => 'الاسم الرسمي للشركة',
            'unit' => '',
            'help' => 'أدخل الاسم كما يجب أن يظهر في المستندات',
        ],
        'address' => [
            'name' => 'العنوان',
            'description' => 'عنوان الشركة',
            'unit' => '',
            'help' => 'تفاصيل عنوان الشركة',
        ],
        'field' => [
            'name' => 'مجال العمل',
            'description' => 'مجال العمل أو النشاط',
            'unit' => '',
            'help' => 'وصف قصير لنشاط الشركة',
        ],
    ],

    'common' => [
        'save' => 'حفظ الإعدادات',
        'cancel' => 'إلغاء',
        'edit' => 'تعديل الإعداد',
        'update' => 'تحديث الإعداد',
        'delete' => 'حذف الإعداد',
        'confirm_delete' => 'هل أنت متأكد من رغبتك في حذف هذا الإعداد؟',
        'setting_updated' => 'تم تحديث الإعداد بنجاح',
        'setting_deleted' => 'تم حذف الإعداد بنجاح',
        'validation_error' => 'يرجى التحقق من قيم الإدخال',
        'no_settings' => 'لم يتم العثور على إعدادات',
        'loading' => 'جاري تحميل الإعدادات...',
        'refresh' => 'تحديث الإعدادات',
        'export' => 'تصدير الإعدادات',
        'import' => 'استيراد الإعدادات',
        'current_value' => 'القيمة الحالية',
        'unit' => 'الوحدة',
        'help' => 'المساعدة',
        'new_value' => 'القيمة الجديدة',
        'disabled' => 'معطل',
        'enabled' => 'مفعل',
    ],

    'form' => [
        'setting_key' => 'مفتاح الإعداد',
        'setting_value' => 'قيمة الإعداد',
        'setting_trans_key' => 'مفتاح الترجمة',
        'category' => 'الفئة',
        'description' => 'الوصف',
        'validation_rules' => 'قواعد التحقق',
        'is_active' => 'مفعل',
        'created_at' => 'تاريخ الإضافة',
        'updated_at' => 'تاريخ التعديل',
    ],

    'validation' => [],
];
