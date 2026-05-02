# TaxFlow

<p align="center">
  <strong>TaxFlow</strong> is a modern Laravel tax administration platform with a sleek responsive UI, approval workflow, document management, and real-time tax monitoring.
</p>

<p align="center">
  <a href="https://github.com/Fikri-Alfarizi/taxflow/actions"><img src="https://img.shields.io/github/actions/workflow/status/Fikri-Alfarizi/taxflow/main.yml?label=CI&logo=github&style=flat-square" alt="CI Status" /></a>
  <a href="https://img.shields.io/github/license/Fikri-Alfarizi/taxflow?style=flat-square"><img src="https://img.shields.io/github/license/Fikri-Alfarizi/taxflow?style=flat-square" alt="License" /></a>
  <a href="https://img.shields.io/badge/Laravel-11.x-red?style=flat-square&logo=laravel"><img src="https://img.shields.io/badge/Laravel-11.x-red?style=flat-square&logo=laravel" alt="Laravel" /></a>
  <a href="https://img.shields.io/badge/Vite-%5E7.0.7-blue?style=flat-square&logo=vite"><img src="https://img.shields.io/badge/Vite-%5E7.0.7-blue?style=flat-square&logo=vite" alt="Vite" /></a>
  <a href="https://img.shields.io/badge/TailwindCSS-%5E4.0.0-teal?style=flat-square&logo=tailwindcss"><img src="https://img.shields.io/badge/TailwindCSS-%5E4.0.0-teal?style=flat-square&logo=tailwindcss" alt="TailwindCSS" /></a>
</p>

## 🚀 Project Overview

TaxFlow is designed for Indonesian tax administrators and finance teams to manage tax records, approvals, documents, and reports with a modern dashboard experience.

Built with:
- Laravel (PHP) backend
- Blade templates with TailwindCSS frontend styling
- Vite-powered asset pipeline
- Responsive layout optimized for desktop and tablet

## 🎯 Core Features

- **Modern Dashboard UI**: Clean cards, data tables, charts, and responsive page layout.
- **Tax Record Management**: Create, edit, view, and delete tax entries.
- **Approval Workflow**: ApprovalController and audit-ready approval flow for tax submissions.
- **Document Management**: Upload, view, and validate tax documents with file metadata tracking.
- **Monitoring & Status Tracking**: Real-time tax job monitoring with process status and user activity.
- **Reports & Export**: Generate tax reports and export data using Excel-ready views.
- **User Roles**: Admin and approval role support with middleware protection.

## 🧩 UI Highlights

TaxFlow uses a modern, polished interface with:

- **Responsive navigation** built for desktop and mobile.
- **Dashboard widgets** for quick status and KPI overview.
- **Data-driven tables** for tax and monitoring records.
- **Minimal, consistent styling** with Tailwind CSS.
- **Friendly form layouts** for tax creation, editing, and approval.

## 📁 Repository Structure

- `app/Http/Controllers/` — Controller logic for tax, approval, and reporting.
- `app/Models/` — Eloquent models for `Pajak`, `Dokumen`, `Monitoring`, `LaporanPajak`, and `CatatanPerbaikan`.
- `database/migrations/` — Schema migrations for tax workflows and document validation.
- `resources/views/` — Responsive Blade templates for dashboard, tax, laporan, approval, and errors.
- `routes/web.php` — Web route definitions for the application.
- `tests/Feature/ApprovalWorkflowTest.php` — Feature tests for approval scenarios.

## ⚙️ Installation

1. Clone repository:
   ```bash
   git clone https://github.com/Fikri-Alfarizi/taxflow.git
   cd taxflow
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install JavaScript dependencies:
   ```bash
   npm install
   ```
4. Copy environment file and configure:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Configure database settings in `.env`.
6. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

## 💻 Development

- Start the application server:
  ```bash
  php artisan serve
  ```
- Start Vite dev server:
  ```bash
  npm run dev
  ```
- Build production assets:
  ```bash
  npm run build
  ```

## 🧪 Testing

Run PHPUnit tests:
```bash
php artisan test
```

## 🔐 Authorization & Administration

- `AdminMiddleware` protects admin-only flows.
- `ApprovalController` handles step-by-step approval actions.
- Document uploads and tax record reviews are secured through middleware and user context.

## 📈 Recommended Workflow

1. Create tax entries in the `Pajak` module.
2. Upload associated documents in `Dokumen`.
3. Use the `Approval` dashboard to review and approve submissions.
4. Monitor progress in `Monitoring` and generate reports via `LaporanPajak`.

## 💡 Notes

- The UI is built to feel modern and clean using Tailwind utility classes.
- The project is ready for customization with additional role management and reporting features.
- Use `resources/views/pajak/export_document.blade.php` and `resources/views/laporan/excel.blade.php` for export workflows.

## 📌 License

This project is licensed under the MIT License.

---

## 📞 Contact

For questions, feature requests, or guidance, connect via GitHub at [Fikri-Alfarizi](https://github.com/Fikri-Alfarizi).
