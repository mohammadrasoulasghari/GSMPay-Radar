<?php

namespace Database\Seeders;

use App\Models\Developer;
use App\Models\PrReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding demo data for GSMPay Radar...');

        // Create developers
        $developers = $this->createDevelopers();

        // Create PR reports for each developer
        foreach ($developers as $developerData) {
            $developer = Developer::firstOrCreate(
                ['username' => $developerData['username']],
                [
                    'name' => $developerData['name'],
                    'avatar_url' => $developerData['avatar_url'],
                ]
            );

            $this->command->info("  👤 Creating reports for {$developer->name}...");

            foreach ($developerData['reports'] as $reportData) {
                $this->createPrReport($developer, $reportData);
            }
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('   - ' . Developer::count() . ' developers');
        $this->command->info('   - ' . PrReport::count() . ' PR reports');
    }

    /**
     * Create a PR report with full raw_analysis payload.
     */
    private function createPrReport(Developer $developer, array $data): PrReport
    {
        $rawAnalysis = $this->buildRawAnalysis($developer, $data);

        return PrReport::create([
            'developer_id' => $developer->id,
            'repository' => $data['repository'],
            'pr_number' => $data['pr_number'],
            'pr_link' => "https://github.com/{$data['repository']}/pull/{$data['pr_number']}",
            'title' => $data['title'],
            'business_value_score' => $data['business_value_score'],
            'solid_compliance_score' => $data['solid_compliance_score'],
            'tone_score' => $data['tone_score'],
            'health_status' => $data['health_status'],
            'risk_level' => $data['risk_level'],
            'change_type' => $data['change_type'],
            'raw_analysis' => $rawAnalysis,
            'created_at' => Carbon::now()->subDays($data['days_ago']),
            'updated_at' => Carbon::now()->subDays($data['days_ago']),
        ]);
    }

    /**
     * Build the complete raw_analysis structure matching the webhook payload.
     */
    private function buildRawAnalysis(Developer $developer, array $data): array
    {
        return [
            'meta_data' => [
                'analysis_timestamp' => Carbon::now()->subDays($data['days_ago'])->toIso8601String(),
                'model_version' => 'claude-sonnet-4-20250514',
            ],
            'executive_summary' => [
                'title_summary' => $data['title'],
                'business_value_clarity' => $data['business_value_score'],
                'overall_health_status' => $data['health_status'],
            ],
            'classification' => [
                'change_type' => $data['change_type'],
                'risk_level' => $data['risk_level'],
                'is_blocking' => $data['risk_level'] === 'high',
            ],
            'author_analytics' => [
                'identity' => $developer->username,
                'quality_metrics' => [
                    'solid_compliance' => $data['solid_compliance_score'],
                    'bug_potential' => $this->getBugPotential($data['solid_compliance_score']),
                    'test_coverage_quality' => $data['test_coverage_quality'] ?? 'پوشش تست مناسب',
                ],
                'velocity_metrics' => [
                    'avg_response_time_hours' => $data['avg_response_time'] ?? rand(2, 12),
                    'rework_cycles' => $data['rework_cycles'] ?? rand(0, 3),
                ],
                'trend_analysis' => [
                    'improvement_status' => $data['improvement_status'] ?? 'stable',
                    'recurring_mistakes' => $data['recurring_mistakes'] ?? [],
                ],
                'educational_path' => $data['educational_path'] ?? [],
            ],
            'reviewers_analytics' => $data['reviewers'] ?? [],
            'gamification_badges' => $data['badges'] ?? [],
            'technical_debt_analysis' => [
                'added_debt_level' => $data['debt_level'] ?? 'low',
                'over_engineering_detected' => $data['over_engineering'] ?? false,
                'suggestions_for_refactor' => $data['refactor_suggestions'] ?? [],
            ],
            'management_decision_assist' => [
                'final_verdict_fa' => $data['verdict_fa'] ?? 'این PR آماده merge است.',
                'performance_review_topic' => $data['review_topic'] ?? null,
                'hr_flag' => $data['hr_flag'] ?? false,
            ],
        ];
    }

    /**
     * Get bug potential based on SOLID compliance score.
     */
    private function getBugPotential(int $solidScore): string
    {
        if ($solidScore >= 80) return 'low';
        if ($solidScore >= 60) return 'medium';
        return 'high';
    }

    /**
     * Define all developers and their PR reports.
     */
    private function createDevelopers(): array
    {
        return [
            // Developer 1: Afshiin - Mixed performance, declining tone
            [
                'username' => 'Iamafshiin',
                'name' => 'افشین محمدی',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/12345678',
                'reports' => [
                    [
                        'repository' => 'gsmpay/payment-gateway',
                        'pr_number' => '142',
                        'title' => 'افزودن ماژول مدیریت فاکتورها (Invoices) شامل لیست، ایمپورت اکسل و پرداخت از کیف پول',
                        'business_value_score' => 75,
                        'solid_compliance_score' => 55,
                        'tone_score' => 92,
                        'health_status' => 'warning',
                        'risk_level' => 'medium',
                        'change_type' => 'feature',
                        'days_ago' => 1,
                        'test_coverage_quality' => 'در کامنت‌ها اشاره‌ای به تست‌ها نشده؛ احتمالاً پوشش تست کافی وجود ندارد.',
                        'recurring_mistakes' => [
                            'عدم رعایت conventions نام‌گذاری پوشه‌ها و فایل‌ها',
                            'استفاده نادرست از relationship‌ها به جای repository اضافی',
                            'عدم استفاده از کامپوننت‌های موجود در پروژه',
                            'ساختار کنترل جریان (if/else) غیربهینه با return‌های تکراری',
                        ],
                        'educational_path' => [
                            [
                                'topic' => 'Laravel Eloquent Relationships',
                                'reason' => 'برای استفاده بهتر از ریلیشن‌ها به جای repository‌های اضافی',
                                'link' => 'https://laravel.com/docs/eloquent-relationships',
                            ],
                            [
                                'topic' => 'Laravel Excel - ToModel & WithValidation',
                                'reason' => 'برای پیاده‌سازی صحیح ایمپورت اکسل با اینترفیس‌های استاندارد',
                                'link' => 'https://docs.laravel-excel.com/3.1/imports/',
                            ],
                        ],
                        'reviewers' => [
                            [
                                'reviewer_login' => 'imahmood',
                                'engagement_metrics' => [
                                    'total_comments' => 21,
                                    'nitpicking_ratio' => 0.15,
                                    'response_speed_rating' => 'fast',
                                ],
                                'behavioral_metrics' => [
                                    'tone_score' => 92,
                                    'mentorship_score' => 78,
                                ],
                                'category_breakdown' => [
                                    'code_style' => 6,
                                    'architecture_design' => 7,
                                    'security' => 0,
                                    'product_requirement' => 3,
                                    'other' => 5,
                                ],
                                'feedback_samples' => [
                                    'best_comment_quote' => 'افشین چرا اینترفیس ToModel و WithValidation پیاده سازی نشده؟',
                                    'worst_comment_quote' => 'ایراد بنی اسرائیلی :)',
                                ],
                            ],
                        ],
                        'badges' => [
                            [
                                'badge_name' => 'The Sniper',
                                'recipient' => 'imahmood',
                                'type' => 'positive',
                                'reason_fa' => 'شناسایی دقیق ۲۱ نقطه بهبود در یک PR با جزئیات کامل و پیشنهادات کد',
                            ],
                            [
                                'badge_name' => 'Teacher',
                                'recipient' => 'imahmood',
                                'type' => 'positive',
                                'reason_fa' => 'ارائه توضیحات آموزشی همراه با suggestion‌های کد برای بهبود یادگیری',
                            ],
                        ],
                        'refactor_suggestions' => [
                            'استفاده از ریلیشن user به جای UserRepository برای دسترسی به کاربر از طریق invoice',
                            'پیاده‌سازی اینترفیس‌های ToModel و WithValidation برای کلاس InvoicesImport',
                            'جداسازی صفحه ایمپورت از صفحه لیست فاکتورها',
                        ],
                        'verdict_fa' => 'این PR نیاز به اصلاحات قبل از merge دارد. ریویور ۲۱ نکته مهم شناسایی کرده که شامل مشکلات معماری است.',
                        'review_topic' => 'رعایت conventions و استانداردهای پروژه',
                    ],
                    [
                        'repository' => 'gsmpay/payment-gateway',
                        'pr_number' => '138',
                        'title' => 'پیاده‌سازی سیستم نوتیفیکیشن برای کاربران',
                        'business_value_score' => 80,
                        'solid_compliance_score' => 62,
                        'tone_score' => 85,
                        'health_status' => 'warning',
                        'risk_level' => 'medium',
                        'change_type' => 'feature',
                        'days_ago' => 7,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'imahmood',
                                'engagement_metrics' => ['total_comments' => 12, 'nitpicking_ratio' => 0.1, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 85, 'mentorship_score' => 72],
                                'category_breakdown' => ['code_style' => 4, 'architecture_design' => 5, 'security' => 1, 'product_requirement' => 2, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'خوبه ولی باید از Queue استفاده کنی', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [],
                    ],
                    [
                        'repository' => 'gsmpay/payment-gateway',
                        'pr_number' => '130',
                        'title' => 'بهبود عملکرد صفحه داشبورد',
                        'business_value_score' => 65,
                        'solid_compliance_score' => 70,
                        'tone_score' => 78,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'optimization',
                        'days_ago' => 14,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'sara_dev',
                                'engagement_metrics' => ['total_comments' => 5, 'nitpicking_ratio' => 0.2, 'response_speed_rating' => 'medium'],
                                'behavioral_metrics' => ['tone_score' => 78, 'mentorship_score' => 65],
                                'category_breakdown' => ['code_style' => 2, 'architecture_design' => 1, 'security' => 0, 'product_requirement' => 1, 'other' => 1],
                                'feedback_samples' => ['best_comment_quote' => 'کش رو خوب پیاده کردی 👍', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Speed Demon', 'recipient' => 'Iamafshiin', 'type' => 'positive', 'reason_fa' => 'بهبود چشمگیر سرعت لود صفحه'],
                        ],
                    ],
                    [
                        'repository' => 'gsmpay/payment-gateway',
                        'pr_number' => '125',
                        'title' => 'رفع باگ محاسبه مالیات',
                        'business_value_score' => 90,
                        'solid_compliance_score' => 75,
                        'tone_score' => 70,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'bugfix',
                        'days_ago' => 21,
                        'reviewers' => [],
                        'badges' => [
                            ['badge_name' => 'Bug Hunter', 'recipient' => 'Iamafshiin', 'type' => 'positive', 'reason_fa' => 'شناسایی و رفع باگ بحرانی در محاسبات مالی'],
                        ],
                    ],
                    [
                        'repository' => 'gsmpay/payment-gateway',
                        'pr_number' => '118',
                        'title' => 'افزودن API برای اپلیکیشن موبایل',
                        'business_value_score' => 85,
                        'solid_compliance_score' => 58,
                        'tone_score' => 65,
                        'health_status' => 'warning',
                        'risk_level' => 'high',
                        'change_type' => 'feature',
                        'days_ago' => 30,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'imahmood',
                                'engagement_metrics' => ['total_comments' => 18, 'nitpicking_ratio' => 0.05, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 65, 'mentorship_score' => 80],
                                'category_breakdown' => ['code_style' => 3, 'architecture_design' => 8, 'security' => 4, 'product_requirement' => 2, 'other' => 1],
                                'feedback_samples' => ['best_comment_quote' => 'این endpoint نیاز به rate limiting داره', 'worst_comment_quote' => 'چرا اینقدر پیچیده کردی؟!'],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Security Guardian', 'recipient' => 'imahmood', 'type' => 'positive', 'reason_fa' => 'شناسایی ۴ مشکل امنیتی در API'],
                        ],
                        'hr_flag' => false,
                    ],
                ],
            ],

            // Developer 2: Sara - High performer, improving trend
            [
                'username' => 'sara_dev',
                'name' => 'سارا احمدی',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/23456789',
                'reports' => [
                    [
                        'repository' => 'gsmpay/user-service',
                        'pr_number' => '89',
                        'title' => 'پیاده‌سازی احراز هویت دو مرحله‌ای (2FA)',
                        'business_value_score' => 95,
                        'solid_compliance_score' => 88,
                        'tone_score' => 95,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'feature',
                        'days_ago' => 2,
                        'improvement_status' => 'improving',
                        'test_coverage_quality' => 'پوشش تست عالی با ۹۵٪ coverage و تست‌های integration کامل',
                        'reviewers' => [
                            [
                                'reviewer_login' => 'ali_senior',
                                'engagement_metrics' => ['total_comments' => 3, 'nitpicking_ratio' => 0.0, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 95, 'mentorship_score' => 90],
                                'category_breakdown' => ['code_style' => 0, 'architecture_design' => 2, 'security' => 1, 'product_requirement' => 0, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'عالی پیاده‌سازی کردی! فقط یه نکته امنیتی کوچیک داره', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Security Champion', 'recipient' => 'sara_dev', 'type' => 'positive', 'reason_fa' => 'پیاده‌سازی امن و کامل سیستم 2FA'],
                            ['badge_name' => 'Test Master', 'recipient' => 'sara_dev', 'type' => 'positive', 'reason_fa' => 'پوشش تست ۹۵٪ با کیفیت بالا'],
                        ],
                        'verdict_fa' => 'این PR آماده merge است. کیفیت کد عالی و پوشش تست کامل.',
                    ],
                    [
                        'repository' => 'gsmpay/user-service',
                        'pr_number' => '85',
                        'title' => 'بهبود سیستم مدیریت نقش‌ها و دسترسی‌ها',
                        'business_value_score' => 88,
                        'solid_compliance_score' => 85,
                        'tone_score' => 90,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'refactor',
                        'days_ago' => 10,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'ali_senior',
                                'engagement_metrics' => ['total_comments' => 5, 'nitpicking_ratio' => 0.0, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 90, 'mentorship_score' => 85],
                                'category_breakdown' => ['code_style' => 1, 'architecture_design' => 3, 'security' => 1, 'product_requirement' => 0, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'خیلی تمیز refactor کردی', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Clean Coder', 'recipient' => 'sara_dev', 'type' => 'positive', 'reason_fa' => 'رفکتور تمیز و بهبود خوانایی کد'],
                        ],
                    ],
                    [
                        'repository' => 'gsmpay/user-service',
                        'pr_number' => '80',
                        'title' => 'پیاده‌سازی سیستم لاگین با شبکه‌های اجتماعی',
                        'business_value_score' => 82,
                        'solid_compliance_score' => 80,
                        'tone_score' => 88,
                        'health_status' => 'healthy',
                        'risk_level' => 'medium',
                        'change_type' => 'feature',
                        'days_ago' => 18,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'imahmood',
                                'engagement_metrics' => ['total_comments' => 8, 'nitpicking_ratio' => 0.1, 'response_speed_rating' => 'medium'],
                                'behavioral_metrics' => ['tone_score' => 88, 'mentorship_score' => 75],
                                'category_breakdown' => ['code_style' => 2, 'architecture_design' => 4, 'security' => 2, 'product_requirement' => 0, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'OAuth flow خوب پیاده شده', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [],
                    ],
                    [
                        'repository' => 'gsmpay/user-service',
                        'pr_number' => '75',
                        'title' => 'رفع باگ session management',
                        'business_value_score' => 70,
                        'solid_compliance_score' => 78,
                        'tone_score' => 85,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'bugfix',
                        'days_ago' => 25,
                        'reviewers' => [],
                        'badges' => [],
                    ],
                ],
            ],

            // Developer 3: Ali Senior - Mentor, excellent reviewer
            [
                'username' => 'ali_senior',
                'name' => 'علی رضایی',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/34567890',
                'reports' => [
                    [
                        'repository' => 'gsmpay/core-lib',
                        'pr_number' => '45',
                        'title' => 'پیاده‌سازی Event Sourcing برای تراکنش‌های مالی',
                        'business_value_score' => 98,
                        'solid_compliance_score' => 95,
                        'tone_score' => 98,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'feature',
                        'days_ago' => 3,
                        'improvement_status' => 'stable',
                        'test_coverage_quality' => 'پوشش تست کامل با property-based testing و mutation testing',
                        'reviewers' => [
                            [
                                'reviewer_login' => 'cto_review',
                                'engagement_metrics' => ['total_comments' => 2, 'nitpicking_ratio' => 0.0, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 98, 'mentorship_score' => 95],
                                'category_breakdown' => ['code_style' => 0, 'architecture_design' => 2, 'security' => 0, 'product_requirement' => 0, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'معماری عالی! این الگو رو باید document کنیم', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Architect', 'recipient' => 'ali_senior', 'type' => 'positive', 'reason_fa' => 'طراحی معماری Event Sourcing برای سیستم مالی'],
                            ['badge_name' => 'Documentation Hero', 'recipient' => 'ali_senior', 'type' => 'positive', 'reason_fa' => 'مستندسازی کامل و واضح'],
                        ],
                        'verdict_fa' => 'این PR آماده merge است. کیفیت کد در سطح enterprise.',
                    ],
                    [
                        'repository' => 'gsmpay/core-lib',
                        'pr_number' => '42',
                        'title' => 'بهبود performance cache layer',
                        'business_value_score' => 85,
                        'solid_compliance_score' => 92,
                        'tone_score' => 95,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'optimization',
                        'days_ago' => 12,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'sara_dev',
                                'engagement_metrics' => ['total_comments' => 4, 'nitpicking_ratio' => 0.0, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 95, 'mentorship_score' => 70],
                                'category_breakdown' => ['code_style' => 1, 'architecture_design' => 2, 'security' => 0, 'product_requirement' => 1, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'بنچمارک‌ها عالی هستن!', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Performance Wizard', 'recipient' => 'ali_senior', 'type' => 'positive', 'reason_fa' => 'بهبود ۴۰٪ در سرعت cache operations'],
                        ],
                    ],
                ],
            ],

            // Developer 4: Mahmood - Good reviewer but critical tone sometimes
            [
                'username' => 'imahmood',
                'name' => 'محمود حسینی',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/45678901',
                'reports' => [
                    [
                        'repository' => 'gsmpay/payment-gateway',
                        'pr_number' => '145',
                        'title' => 'پیاده‌سازی Webhook برای درگاه‌های پرداخت',
                        'business_value_score' => 90,
                        'solid_compliance_score' => 82,
                        'tone_score' => 88,
                        'health_status' => 'healthy',
                        'risk_level' => 'medium',
                        'change_type' => 'feature',
                        'days_ago' => 4,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'ali_senior',
                                'engagement_metrics' => ['total_comments' => 6, 'nitpicking_ratio' => 0.0, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 88, 'mentorship_score' => 85],
                                'category_breakdown' => ['code_style' => 1, 'architecture_design' => 3, 'security' => 2, 'product_requirement' => 0, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'Retry mechanism خوب پیاده شده', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Integration Master', 'recipient' => 'imahmood', 'type' => 'positive', 'reason_fa' => 'پیاده‌سازی کامل webhook با retry و idempotency'],
                        ],
                    ],
                    [
                        'repository' => 'gsmpay/payment-gateway',
                        'pr_number' => '140',
                        'title' => 'رفع مشکل race condition در پرداخت همزمان',
                        'business_value_score' => 95,
                        'solid_compliance_score' => 85,
                        'tone_score' => 90,
                        'health_status' => 'healthy',
                        'risk_level' => 'high',
                        'change_type' => 'bugfix',
                        'days_ago' => 8,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'ali_senior',
                                'engagement_metrics' => ['total_comments' => 4, 'nitpicking_ratio' => 0.0, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 90, 'mentorship_score' => 88],
                                'category_breakdown' => ['code_style' => 0, 'architecture_design' => 2, 'security' => 2, 'product_requirement' => 0, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'لاک‌ها درست پیاده شدن، فقط timeout رو چک کن', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Concurrency Expert', 'recipient' => 'imahmood', 'type' => 'positive', 'reason_fa' => 'رفع مشکل پیچیده race condition'],
                        ],
                    ],
                    [
                        'repository' => 'gsmpay/payment-gateway',
                        'pr_number' => '135',
                        'title' => 'افزودن گزارش‌گیری مالی',
                        'business_value_score' => 88,
                        'solid_compliance_score' => 78,
                        'tone_score' => 85,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'feature',
                        'days_ago' => 15,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'sara_dev',
                                'engagement_metrics' => ['total_comments' => 7, 'nitpicking_ratio' => 0.15, 'response_speed_rating' => 'medium'],
                                'behavioral_metrics' => ['tone_score' => 85, 'mentorship_score' => 70],
                                'category_breakdown' => ['code_style' => 3, 'architecture_design' => 2, 'security' => 0, 'product_requirement' => 2, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'Query optimization خوب شده', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [],
                    ],
                ],
            ],

            // Developer 5: New junior developer - Learning curve
            [
                'username' => 'reza_junior',
                'name' => 'رضا کریمی',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/56789012',
                'reports' => [
                    [
                        'repository' => 'gsmpay/admin-panel',
                        'pr_number' => '67',
                        'title' => 'افزودن صفحه مدیریت کاربران',
                        'business_value_score' => 60,
                        'solid_compliance_score' => 45,
                        'tone_score' => 75,
                        'health_status' => 'warning',
                        'risk_level' => 'medium',
                        'change_type' => 'feature',
                        'days_ago' => 5,
                        'improvement_status' => 'improving',
                        'test_coverage_quality' => 'تست‌های پایه نوشته شده ولی edge case‌ها پوشش داده نشدن',
                        'recurring_mistakes' => [
                            'عدم استفاده از validation request class',
                            'N+1 query در لیست‌ها',
                            'کد تکراری در controller‌ها',
                        ],
                        'educational_path' => [
                            [
                                'topic' => 'Laravel Form Request Validation',
                                'reason' => 'برای جداسازی منطق validation از controller',
                                'link' => 'https://laravel.com/docs/validation#form-request-validation',
                            ],
                            [
                                'topic' => 'Eloquent Eager Loading',
                                'reason' => 'برای حل مشکل N+1 query',
                                'link' => 'https://laravel.com/docs/eloquent-relationships#eager-loading',
                            ],
                        ],
                        'reviewers' => [
                            [
                                'reviewer_login' => 'imahmood',
                                'engagement_metrics' => ['total_comments' => 15, 'nitpicking_ratio' => 0.1, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 75, 'mentorship_score' => 85],
                                'category_breakdown' => ['code_style' => 5, 'architecture_design' => 6, 'security' => 2, 'product_requirement' => 1, 'other' => 1],
                                'feedback_samples' => ['best_comment_quote' => 'خوب شروع کردی! چند تا نکته هست که باید یاد بگیری', 'worst_comment_quote' => 'این روش اصلاً درست نیست!'],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Mentor', 'recipient' => 'imahmood', 'type' => 'positive', 'reason_fa' => 'راهنمایی دقیق و آموزشی برای توسعه‌دهنده جدید'],
                        ],
                        'verdict_fa' => 'این PR نیاز به اصلاحات دارد. پیشنهاد می‌شود نویسنده با mentor خود جلسه بگذارد.',
                        'review_topic' => 'آموزش اصول پایه Laravel و best practices',
                    ],
                    [
                        'repository' => 'gsmpay/admin-panel',
                        'pr_number' => '63',
                        'title' => 'رفع باگ نمایش تاریخ در لیست',
                        'business_value_score' => 50,
                        'solid_compliance_score' => 55,
                        'tone_score' => 80,
                        'health_status' => 'healthy',
                        'risk_level' => 'low',
                        'change_type' => 'bugfix',
                        'days_ago' => 12,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'sara_dev',
                                'engagement_metrics' => ['total_comments' => 3, 'nitpicking_ratio' => 0.0, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 80, 'mentorship_score' => 75],
                                'category_breakdown' => ['code_style' => 1, 'architecture_design' => 1, 'security' => 0, 'product_requirement' => 1, 'other' => 0],
                                'feedback_samples' => ['best_comment_quote' => 'خوبه! فقط Carbon::parse استفاده کن', 'worst_comment_quote' => null],
                            ],
                        ],
                        'badges' => [],
                    ],
                ],
            ],

            // Developer 6: Problematic developer - HR flag candidate
            [
                'username' => 'ahmad_trouble',
                'name' => 'احمد محمدزاده',
                'avatar_url' => 'https://avatars.githubusercontent.com/u/67890123',
                'reports' => [
                    [
                        'repository' => 'gsmpay/merchant-portal',
                        'pr_number' => '34',
                        'title' => 'افزودن فیلتر به لیست تراکنش‌ها',
                        'business_value_score' => 55,
                        'solid_compliance_score' => 35,
                        'tone_score' => 45,
                        'health_status' => 'critical',
                        'risk_level' => 'high',
                        'change_type' => 'feature',
                        'days_ago' => 6,
                        'improvement_status' => 'declining',
                        'test_coverage_quality' => 'هیچ تستی نوشته نشده',
                        'recurring_mistakes' => [
                            'عدم رعایت هیچ یک از اصول SOLID',
                            'SQL injection vulnerability',
                            'عدم استفاده از transaction در عملیات‌های چندگانه',
                            'کد غیرقابل نگهداری و بدون کامنت',
                            'نادیده گرفتن feedback ریویورها',
                        ],
                        'educational_path' => [
                            [
                                'topic' => 'SOLID Principles',
                                'reason' => 'برای بهبود کیفیت کد و قابلیت نگهداری',
                                'link' => 'https://www.digitalocean.com/community/conceptual_articles/s-o-l-i-d-the-first-five-principles-of-object-oriented-design',
                            ],
                            [
                                'topic' => 'Laravel Security Best Practices',
                                'reason' => 'برای جلوگیری از SQL injection و مشکلات امنیتی',
                                'link' => 'https://laravel.com/docs/security',
                            ],
                        ],
                        'reviewers' => [
                            [
                                'reviewer_login' => 'ali_senior',
                                'engagement_metrics' => ['total_comments' => 25, 'nitpicking_ratio' => 0.0, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 45, 'mentorship_score' => 90],
                                'category_breakdown' => ['code_style' => 5, 'architecture_design' => 8, 'security' => 8, 'product_requirement' => 2, 'other' => 2],
                                'feedback_samples' => [
                                    'best_comment_quote' => 'این یک مشکل امنیتی جدی است که باید فوراً رفع شود',
                                    'worst_comment_quote' => 'متاسفانه این کد قابل merge نیست',
                                ],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Red Flag', 'recipient' => 'ahmad_trouble', 'type' => 'negative', 'reason_fa' => 'مشکلات امنیتی متعدد و عدم رعایت استانداردها'],
                        ],
                        'over_engineering' => false,
                        'debt_level' => 'high',
                        'refactor_suggestions' => [
                            'بازنویسی کامل با رعایت اصول SOLID',
                            'استفاده از Query Builder به جای raw SQL',
                            'افزودن validation و sanitization',
                            'نوشتن تست‌های واحد و integration',
                        ],
                        'verdict_fa' => '⚠️ این PR حاوی مشکلات امنیتی جدی است و نباید merge شود. پیشنهاد می‌شود جلسه‌ای با مدیر فنی برگزار شود.',
                        'review_topic' => 'مشکلات امنیتی و عدم رعایت استانداردها - نیاز به بررسی HR',
                        'hr_flag' => true,
                    ],
                    [
                        'repository' => 'gsmpay/merchant-portal',
                        'pr_number' => '30',
                        'title' => 'تغییرات UI داشبورد',
                        'business_value_score' => 40,
                        'solid_compliance_score' => 40,
                        'tone_score' => 50,
                        'health_status' => 'critical',
                        'risk_level' => 'medium',
                        'change_type' => 'feature',
                        'days_ago' => 14,
                        'reviewers' => [
                            [
                                'reviewer_login' => 'imahmood',
                                'engagement_metrics' => ['total_comments' => 18, 'nitpicking_ratio' => 0.05, 'response_speed_rating' => 'fast'],
                                'behavioral_metrics' => ['tone_score' => 50, 'mentorship_score' => 70],
                                'category_breakdown' => ['code_style' => 6, 'architecture_design' => 5, 'security' => 3, 'product_requirement' => 3, 'other' => 1],
                                'feedback_samples' => ['best_comment_quote' => 'لطفاً feedback‌های قبلی رو هم اعمال کن', 'worst_comment_quote' => 'چرا همون اشتباهات رو تکرار می‌کنی؟'],
                            ],
                        ],
                        'badges' => [
                            ['badge_name' => 'Repeat Offender', 'recipient' => 'ahmad_trouble', 'type' => 'negative', 'reason_fa' => 'تکرار اشتباهات قبلی بدون بهبود'],
                        ],
                        'hr_flag' => true,
                    ],
                ],
            ],
        ];
    }
}
