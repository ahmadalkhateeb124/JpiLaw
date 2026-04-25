-- ─────────────────────────────────────────────────────────────────────────────
-- JpiLaw — CMS Database Schema
-- All content tables include both _ar (Arabic) and _en (English) columns.
-- ─────────────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─── Admins ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(150) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('superadmin','admin','editor') NOT NULL DEFAULT 'admin',
    avatar          VARCHAR(255) DEFAULT NULL,
    last_login_at   DATETIME DEFAULT NULL,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Site settings (single-row key/value) ────────────────────────────────────
CREATE TABLE IF NOT EXISTS site_settings (
    `key`           VARCHAR(100) NOT NULL PRIMARY KEY,
    value_ar        TEXT,
    value_en        TEXT,
    `group`         VARCHAR(50) NOT NULL DEFAULT 'general',
    type            ENUM('text','textarea','image','number','email','url','phone','color','boolean') NOT NULL DEFAULT 'text',
    label_ar        VARCHAR(150),
    label_en        VARCHAR(150),
    sort_order      INT NOT NULL DEFAULT 0,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Media library ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS media (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    file_name       VARCHAR(255) NOT NULL,
    file_path       VARCHAR(500) NOT NULL,
    mime_type       VARCHAR(100),
    file_size       INT UNSIGNED DEFAULT 0,
    alt_ar          VARCHAR(255),
    alt_en          VARCHAR(255),
    uploaded_by     INT UNSIGNED,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_uploaded_by (uploaded_by),
    CONSTRAINT fk_media_admin FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Blog ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blog_categories (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(150) NOT NULL UNIQUE,
    name_ar         VARCHAR(150) NOT NULL,
    name_en         VARCHAR(150) NOT NULL,
    description_ar  TEXT,
    description_en  TEXT,
    sort_order      INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_posts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(200) NOT NULL UNIQUE,
    category_id     INT UNSIGNED,
    author_id       INT UNSIGNED,
    title_ar        VARCHAR(255) NOT NULL,
    title_en        VARCHAR(255) NOT NULL,
    excerpt_ar      TEXT,
    excerpt_en      TEXT,
    content_ar      LONGTEXT NOT NULL,
    content_en      LONGTEXT NOT NULL,
    featured_image  VARCHAR(500),
    meta_title_ar   VARCHAR(255),
    meta_title_en   VARCHAR(255),
    meta_description_ar VARCHAR(500),
    meta_description_en VARCHAR(500),
    meta_keywords_ar VARCHAR(500),
    meta_keywords_en VARCHAR(500),
    views           INT UNSIGNED NOT NULL DEFAULT 0,
    status          ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    published_at    DATETIME DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_published (published_at),
    INDEX idx_category (category_id),
    CONSTRAINT fk_post_category FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_post_author FOREIGN KEY (author_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_tags (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(100) NOT NULL UNIQUE,
    name_ar         VARCHAR(100) NOT NULL,
    name_en         VARCHAR(100) NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_post_tags (
    post_id         INT UNSIGNED NOT NULL,
    tag_id          INT UNSIGNED NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    CONSTRAINT fk_pt_post FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_pt_tag FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Practice areas (مجالات الممارسة) ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS practice_areas (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(150) NOT NULL UNIQUE,
    title_ar        VARCHAR(200) NOT NULL,
    title_en        VARCHAR(200) NOT NULL,
    short_desc_ar   VARCHAR(500),
    short_desc_en   VARCHAR(500),
    content_ar      LONGTEXT,
    content_en      LONGTEXT,
    icon            VARCHAR(100),
    image           VARCHAR(500),
    sort_order      INT NOT NULL DEFAULT 0,
    is_featured     TINYINT(1) NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Services ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS services (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(150) NOT NULL UNIQUE,
    title_ar        VARCHAR(200) NOT NULL,
    title_en        VARCHAR(200) NOT NULL,
    short_desc_ar   VARCHAR(500),
    short_desc_en   VARCHAR(500),
    content_ar      LONGTEXT,
    content_en      LONGTEXT,
    icon            VARCHAR(100),
    image           VARCHAR(500),
    sort_order      INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Team / Attorneys ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS attorneys (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(150) NOT NULL UNIQUE,
    name_ar         VARCHAR(150) NOT NULL,
    name_en         VARCHAR(150) NOT NULL,
    title_ar        VARCHAR(200),
    title_en        VARCHAR(200),
    bio_ar          LONGTEXT,
    bio_en          LONGTEXT,
    image           VARCHAR(500),
    email           VARCHAR(150),
    phone           VARCHAR(50),
    linkedin        VARCHAR(255),
    twitter         VARCHAR(255),
    facebook        VARCHAR(255),
    instagram       VARCHAR(255),
    sort_order      INT NOT NULL DEFAULT 0,
    is_featured     TINYINT(1) NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Testimonials ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS testimonials (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name_ar         VARCHAR(150) NOT NULL,
    name_en         VARCHAR(150) NOT NULL,
    role_ar         VARCHAR(150),
    role_en         VARCHAR(150),
    content_ar      TEXT NOT NULL,
    content_en      TEXT NOT NULL,
    image           VARCHAR(500),
    rating          TINYINT NOT NULL DEFAULT 5,
    sort_order      INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── FAQs ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS faqs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_ar     VARCHAR(500) NOT NULL,
    question_en     VARCHAR(500) NOT NULL,
    answer_ar       TEXT NOT NULL,
    answer_en       TEXT NOT NULL,
    category_ar     VARCHAR(150),
    category_en     VARCHAR(150),
    sort_order      INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Certificates ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS certificates (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title_ar        VARCHAR(200) NOT NULL,
    title_en        VARCHAR(200) NOT NULL,
    description_ar  TEXT,
    description_en  TEXT,
    image           VARCHAR(500),
    issued_by_ar    VARCHAR(200),
    issued_by_en    VARCHAR(200),
    issued_at       DATE,
    sort_order      INT NOT NULL DEFAULT 0,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Pages content (free-form sections for static pages) ─────────────────────
CREATE TABLE IF NOT EXISTS pages_content (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_slug       VARCHAR(100) NOT NULL,
    section_key     VARCHAR(100) NOT NULL,
    title_ar        VARCHAR(255),
    title_en        VARCHAR(255),
    content_ar      LONGTEXT,
    content_en      LONGTEXT,
    image           VARCHAR(500),
    extra_data      JSON,
    sort_order      INT NOT NULL DEFAULT 0,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_page_section (page_slug, section_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Appointments / Inquiries (extend existing pattern) ──────────────────────
CREATE TABLE IF NOT EXISTS appointments (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(150) NOT NULL,
    email           VARCHAR(150) NOT NULL,
    phone           VARCHAR(50),
    subject         VARCHAR(255),
    message         TEXT,
    preferred_date  DATE,
    preferred_time  TIME,
    status          ENUM('new','contacted','scheduled','completed','cancelled') NOT NULL DEFAULT 'new',
    admin_notes     TEXT,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Existing tables (re-create idempotently) ────────────────────────────────
CREATE TABLE IF NOT EXISTS Inquiries (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    FullName        VARCHAR(150) NOT NULL,
    Mobile          VARCHAR(50),
    Email           VARCHAR(150),
    Subject         VARCHAR(255),
    Msg             TEXT,
    is_read         TINYINT(1) NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS NewsletterMails (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Email           VARCHAR(150) NOT NULL UNIQUE,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Activity log ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS activity_log (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id        INT UNSIGNED,
    action          VARCHAR(100) NOT NULL,
    entity_type     VARCHAR(50),
    entity_id       INT UNSIGNED,
    description     TEXT,
    ip_address      VARCHAR(45),
    user_agent      VARCHAR(500),
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin (admin_id),
    INDEX idx_created (created_at),
    CONSTRAINT fk_log_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
