-- قاعدة بيانات منصة قيمة (Qima Platform Database)
-- متوافقة تماماً مع XAMPP / MySQL / MariaDB (UTF-8 Unicode)

CREATE DATABASE IF NOT EXISTS `qima_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `qima_db`;

-- جدول المستخدمين (users)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `phone` VARCHAR(50) NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin', 'owner') DEFAULT 'user',
  `reset_token` VARCHAR(255) NULL,
  `reset_expires` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول تصنيفات الموارد (materials)
CREATE TABLE IF NOT EXISTS `materials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(150) NOT NULL,
  `english` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الخدمات (services)
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(150) NOT NULL,
  `short_desc` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `items` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول منتجات وعروض سوق الموارد (products)
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `handle` VARCHAR(191) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `unit` VARCHAR(50) DEFAULT 'طن',
  `location` VARCHAR(100) NOT NULL DEFAULT 'الرياض، المملكة العربية السعودية',
  `quantity` VARCHAR(100) NOT NULL DEFAULT 'متوفر بكميات تجارية',
  `description` TEXT NOT NULL,
  `image_url` VARCHAR(500) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول الاستفسارات وطلبات الموارد (inquiries)
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(50) NULL,
  `organization` VARCHAR(191) NULL,
  `subject` VARCHAR(255) NOT NULL,
  `details` TEXT NOT NULL,
  `status` ENUM('new', 'reviewed', 'contacted', 'closed') DEFAULT 'new',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- جدول المشتركين في النشرة البريدية (newsletter_subscribers)
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- إدخال بيانات تجريبية وحسابات قياسية (كلمة مرور المسؤول: admin123456)
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`) VALUES
(1, 'مدير منصة قيمة', 'admin@qima.sa', '966500000000', '$2y$10$w81oXq5611J16oR1i1cZ4eF3nKxUvFh2n2eR008n93nC9c4kQ98x6', 'owner'),
(2, 'مستخدم تجريبي', 'user@qima.sa', '966511111111', '$2y$10$w81oXq5611J16oR1i1cZ4eF3nKxUvFh2n2eR008n93nC9c4kQ98x6', 'user')
ON DUPLICATE KEY UPDATE `email`=VALUES(`email`);

-- بيانات الموارد
INSERT INTO `materials` (`slug`, `title`, `english`, `description`) VALUES
('plastic', 'البلاستيك', 'PLASTICS', 'مواد بلاستيكية قابلة لإعادة التدوير، وحبيبات وموارد صناعية لإعادة الاستخدام.'),
('paper', 'الورق والكرتون', 'PAPER & CARDBOARD', 'موارد ورقية وكرتون مفروز لتغذية سلاسل إعادة التدوير.'),
('metals', 'المعادن', 'METALS', 'معادن حديدية وغير حديدية، وفرص استعادة القيمة من الموارد المعدنية.'),
('electronics', 'الإلكترونيات', 'ELECTRONICS', 'أجهزة ومكونات إلكترونية قابلة لإعادة الاستخدام أو المعالجة المتخصصة.'),
('tires', 'الإطارات', 'TIRES', 'إطارات مستعملة وموارد مطاطية تحتاج إلى معالجة وإعادة تدوير مناسبة.'),
('textiles', 'المنسوجات', 'TEXTILES', 'ألياف ومنسوجات وبقايا إنتاج يمكن إعادة استخدامها أو تدويرها.')
ON DUPLICATE KEY UPDATE `slug`=VALUES(`slug`);

-- بيانات الخدمات
INSERT INTO `services` (`slug`, `title`, `short_desc`, `description`, `items`) VALUES
('logistics', 'النقل والتنسيق اللوجستي', 'من المورد إلى الوجهة المناسبة.', 'تنسيق حركة الموارد بين الموردين والمشترين والمصانع، مع تحديد نوع المادة والكميات ومواقع الاستلام والتسليم ومتطلبات المناولة قبل الاتفاق.', '["تحديد احتياجات النقل والمناولة", "تنسيق الاستلام والتسليم بين الأطراف", "وضوح المسؤوليات ونطاق الخدمة"]'),
('esg', 'تقارير الأثر والاستدامة', 'أثر واضح، وقرارات أكثر مسؤولية.', 'تنظيم بيانات الموارد والتدوير لإعداد تقارير الأثر البيئي والحوكمة الاجتماعية والمؤسسية (ESG)، وفق البيانات المتاحة والمنهجيات الموضّحة، دون افتراض نتائج غير موثقة.', '["تجميع بيانات الموارد ومخرجات التدوير", "توثيق مصادر البيانات ومنهجية الحساب", "دعم المتابعة والإفصاح المؤسسي"]'),
('market', 'التحليلات السوقية والتسعير', 'رؤية أوضح لسوق الموارد.', 'متابعة الأسعار المتاحة والعوامل المؤثرة في قيمة الموارد، مثل النوع والجودة والموقع وحجم الكمية، مع التمييز بين سعر العرض والسعر الفعلي للمعاملة.', '["تنظيم الأسعار حسب نوع المورد", "مقارنة مواصفات وكميات العروض", "توضيح المصدر وتاريخ التحديث"]')
ON DUPLICATE KEY UPDATE `slug`=VALUES(`slug`);

-- بيانات سوق الموارد (Products)
INSERT INTO `products` (`handle`, `title`, `category`, `price`, `unit`, `location`, `quantity`, `description`, `image_url`) VALUES
('recycled-hdpe-pellets', 'حبيبات بلاستيك معاد تدويره (HDPE)', 'plastic', 3200.00, 'طن', 'الرياض، المدينة الصناعية الثانية', '50 طن شهرياً', 'حبيبات بولي إيثيلين عالي الكثافة مفروزة ومغسولة ومحببة بدقة عالية صالحة للأغراض الصناعية والقوالب.', 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?auto=format&fit=crop&w=800&q=80'),
('sorted-corrugated-cardboard', 'كرتون مضلع مفروز ومكبوس (OCC)', 'paper', 650.00, 'طن', 'جدة، المنطقة الصناعية', '120 طن شهرياً', 'كرتون مضلع نظيف مفروز ومضغوط في بالات عالية الكثافة جاهزة لمصانع الورق والكرتون المعاد تدويره.', 'https://images.unsplash.com/photo-1607344645866-009c320c5ab8?auto=format&fit=crop&w=800&q=80'),
('clean-aluminum-scrap', 'خردة ألمنيوم صناعي مفروز ونظيف', 'metals', 7400.00, 'طن', 'الدمام، الصناعية الأولى', '30 طن', 'قصاصات وألواح ألمنيوم مفروزة خالية من الشوائب والحديد، مثالية لأفران الصهر وإعادة التشكيل.', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=800&q=80'),
('shredded-tires-tdf', 'مطاط إطارات مفروم ومشذب (TDF)', 'tires', 450.00, 'طن', 'القصيم', '80 طن', 'حبيبات ومطاط إطارات مقطع جاهز للاستخدام كوقود بديل في مصانع الأسمنت أو التطبيقات الإنشائية والأرضيات.', 'https://images.unsplash.com/photo-1578844251758-2f71da64c96f?auto=format&fit=crop&w=800&q=80')
ON DUPLICATE KEY UPDATE `handle`=VALUES(`handle`);