<?php

return [
    // Resource labels
    'developers' => 'توسعه‌دهندگان',
    'developer' => 'توسعه‌دهنده',

    // Fields
    'username' => 'نام کاربری',
    'name' => 'نام',
    'avatar_url' => 'آدرس آواتار',
    'created_at' => 'تاریخ ایجاد',
    'updated_at' => 'تاریخ به‌روزرسانی',

    // Table columns
    'total_reports' => 'تعداد گزارش‌ها',
    'last_activity' => 'آخرین فعالیت',

    // Stats widget
    'stats' => [
        'total_reports' => 'کل گزارش‌ها',
        'total_reports_desc' => 'تعداد PRهای تحلیل شده',
        'avg_tone_score' => 'میانگین امتیاز لحن',
        'avg_tone_score_desc' => 'میانگین نمره لحن بازبینی‌کنندگان',
        'compliance_rate' => 'نرخ انطباق',
        'compliance_rate_desc' => 'میانگین رعایت اصول SOLID',
        'health_status' => 'وضعیت سلامت',
        'health_status_desc' => 'وضعیت کلی بر اساس گزارش‌های اخیر',
        'no_data' => 'بدون داده',
    ],

    // Health status values
    'health' => [
        'healthy' => 'سالم',
        'warning' => 'هشدار',
        'critical' => 'بحرانی',
        'unknown' => 'نامشخص',
    ],

    // Risk levels
    'risk' => [
        'high' => 'بالا',
        'medium' => 'متوسط',
        'low' => 'پایین',
        'unknown' => 'نامشخص',
    ],

    // Charts
    'charts' => [
        'performance_trends' => 'روند عملکرد',
        'technical_quality' => 'روند کیفیت فنی',
        'technical_quality_desc' => 'نمره انطباق SOLID و ارزش تجاری در طول زمان',
        'behavioral_trend' => 'روند رفتاری',
        'behavioral_trend_desc' => 'نمره لحن برای تشخیص فرسودگی شغلی',
        'solid_compliance' => 'انطباق SOLID',
        'business_value' => 'ارزش تجاری',
        'tone_score' => 'امتیاز لحن',
        'no_data_chart' => 'داده کافی برای نمایش نمودار وجود ندارد',
        'min_data_required' => 'حداقل ۲ گزارش برای مشاهده روند نیاز است',
    ],

    // Trend indicators
    'trend' => [
        'stable' => 'پایدار',
        'declining' => 'در حال کاهش',
        'improving' => 'در حال بهبود',
        'insufficient_data' => 'داده ناکافی',
        'burnout_warning' => '⚠️ هشدار: احتمال فرسودگی شغلی',
    ],

    // Relation manager
    'pr_reports' => 'گزارش‌های PR',
    'pr_reports_empty' => 'هنوز گزارشی برای این توسعه‌دهنده ثبت نشده است',
    'pr_reports_empty_desc' => 'پس از تحلیل PRها، گزارش‌ها در اینجا نمایش داده می‌شوند',

    // Profile section
    'profile_info' => 'اطلاعات پروفایل',
    'view_on_github' => 'مشاهده در گیت‌هاب',

    // Filters
    'filters' => [
        'risk_level' => 'سطح ریسک',
        'all' => 'همه',
    ],

    // Actions
    'actions' => [
        'view' => 'مشاهده',
        'edit' => 'ویرایش',
        'delete' => 'حذف',
    ],
];
