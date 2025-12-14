# 🚀 Template AI Vibe

یک template repository برای پروژه‌های جدید با تمرکز بر معماری تمیز و اصل **Single Responsibility Principle (SRP)**.

## 📋 درباره این Template

این template بر اساس بهترین الگوهای معماری Laravel و اصول SOLID ساخته شده است. مخصوص پروژه‌های که نیاز به:

- ✅ معماری تمیز و قابل نگهداری
- ✅ جداسازی کامل مسئولیت‌ها (SRP)
- ✅ آزمایش‌پذیری بالا
- ✅ کد قابل گسترش

دارند.

## 🏗️ معماری

```
app/
├── Http/
│   └── Controllers/          # فقط درخواست‌ها را مدیریت می‌کند
├── Services/                 # منطق کسب‌وکار
├── Repositories/             # دستیابی به داده‌ها
├── Models/                   # مدل‌های دیتابیس
└── Providers/               # Service Providers
```

## 🎯 اصل SRP (Single Responsibility Principle)

هر کلاس باید **فقط یک مسئولیت** داشته باشد و فقط یک دلیل برای تغییر داشته باشد.

### ❌ غلط:
```php
class UserController {
    public function store(Request $request) {
        // اعتبارسنجی
        // ایجاد کاربر
        // ارسال ایمیل
        // ثبت log
    }
}
```

### ✅ صحیح:
```php
class UserController {
    public function __construct(private CreateUserService $createUserService) {}
    
    public function store(CreateUserRequest $request) {
        $user = $this->createUserService->execute($request->validated());
        return response()->json($user);
    }
}
```

## 🚀 شروع کار

1. **Clone این template:**
   ```bash
   git clone https://github.com/mohammadrasoulasghari/template-ai-vibe.git my-new-project
   cd my-new-project
   ```

2. **تغییر origin:**
   ```bash
   git remote remove origin
   git remote add origin https://github.com/YOUR_USERNAME/my-new-project.git
   ```

3. **Push کن:**
   ```bash
   git branch -M main
   git push -u origin main
   ```

## 📦 نیازمندی‌ها

- PHP 8.2+
- Laravel 11+
- Composer
- Node.js & npm

## 🛠️ Setup

```bash
# نصب dependencies
composer install
npm install

# میگریشن‌ها
php artisan migrate

# Build assets
npm run build
```

## 📚 فایل‌های کلیدی

- **`.copilot-instructions.md`** - دستورالعمل‌های معماری و تکنیک‌های توسعه
- **`app/Services/`** - تمام منطق کسب‌وکار
- **`app/Repositories/`** - لایۀ دستیابی به داده‌ها
- **`app/Http/Controllers/`** - فقط مدیریت درخواست‌ها

## 💡 نکات مهم

- هر سرویس فقط **یک** مسئولیت دارد
- از Dependency Injection استفاده کنید
- کد را قابل تست کنید
- نام‌گذاری واضح و شفاف استفاده کنید

## 📖 منابع مفید

- [Laravel Documentation](https://laravel.com/docs)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)

---

**ساخته شده با ❤️ برای توسعه‌دهندگان که کد تمیز دوست دارند**
