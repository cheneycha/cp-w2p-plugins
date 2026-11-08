# Plugin Requirement (PRD)

## 1. System Compatibility Requirement
- **WordPress**: Compatible with WordPress v7.1 or above
- **PHP**: Compatible with PHP 8.3 or above
- **WooCommerce**: Compatible with WooCommerce plugin v11.2 or above
- **Node.JS**: Compatible with Node.JS v26 or newer LTS version
- **Frontend Build Tool**: React + Vite (compiled to static bundle for WordPress integration)
- **NBDesigner**: 100% compatible with WordPress plugin in folder `web-to-print-online-designer` without modifying core code

---

## 2. General Guidelines
- **UI Framework**: React + Vite for both admin settings and storefront components.
- **UI/UX Standard**: Smooth, modern, clean, and minimalist with integrated visual setup wizards and configuration guidance.
- **Backend Runtime**: PHP 8.3 only. Since the production server does not support external runtime daemons, frontend apps must compile to static assets enqueued by WordPress and communicate via WordPress REST API (`/wp-json/nbd-s3-offload/v1/`).
- **Memory Protocol**: Maintain project memory files (`activeContext.md`, `systemPatterns.md`, `progress.md`) to prevent unnecessary codebase scanning.

---

## 3. Function & Objective of this Plugin Development

### Background
NBDesigner provides web-to-print design tools for WooCommerce:
- **Admin**: Enables online design on products, designs templates, and downloads print-ready vector/raster files after orders are placed.
- **Guest / Customer**: Customizes products online, saves designs to their account, and submits orders.
- **Storage Bloat Problem**: NBDesigner stores all assets inside `wp-content/uploads/nbdesigner/`. Because admin templates, active customer designs, order items, and abandoned guest drafts are all mixed together under `designs/`, disk usage grows uncontrollably.
- **Font Bandwidth Problem**: Traditional and Simplified Chinese font files are large (15MB–40MB+ each). Currently, visitors must download these directly from the origin server for every canvas session, causing server bandwidth spikes and slow loading.

### Core Objectives
1. **Reduce Server Disk Costs via Tiered Storage**: Offload silent guest and order designs to low-cost S3-compatible object storage and reclaim local NVMe server disk space.
2. **Chinese Font Delivery & Dual-Sync**: Maintain font files both locally (for backend TCPDF print rendering) and in S3/CDN (for fast browser delivery). Enforce 1-month CDN browser caching (`Cache-Control: public, max-age=2592000, immutable`), permissive CORS headers, and provide a one-click admin tool to purge cloud fonts and resync from local storage.
3. **Admin Configuration Dashboard**:
   - Provide clean UI to configure S3-compatible storage credentials, bucket, region, endpoint, and CDN URL.
   - Support S3-compatible providers: **Wasabi**, **Backblaze B2**, **Bunny Storage / BunnyCDN**, **DigitalOcean Spaces**, **AWS S3**, and **Custom S3 Endpoint**.
   - Test Connection & Bucket Permissions check button.
   - **Font Management Tool**: One-click "Purge & Resync Fonts from Local" button with live progress indicator.
4. **Selective Offload Toggles**:
   - Design Templates (Offload vs. Keep Local)
   - Customer Saved Designs
   - Designs associated with Orders
5. **High Concurrency Support**: Gracefully handle 10–100 concurrent online users without blocking customer checkouts or canvas interactions.

---

## 4. Storage Tiering & Lifecycle Policy

NBDesigner stores both templates and guest orders as sibling folders inside `wp-content/uploads/nbdesigner/designs/{folder}/`. The plugin implements a multi-tier lifecycle engine to optimize cost and performance:

```
[ Tier 1: Local High-Speed NVMe Storage (Whitelist) ]
  └── Admin Templates (Active, read frequently by all shoppers, whitelisted from offload)

[ Tier 2: Hot Active Orders (Local NVMe SSD Fast Fulfillment) ]
  └── Active Guest / Order Designs (Hot Phase: 0 - 14 days, in active print fulfillment)

[ Tier 3: Inactive / Cold Orders (Move to S3 & Delete Local Copy) ]
  └── Archived Order Designs (> 14 days silent, local copy purged, hydrated on-demand)

[ Tier 4: Font Dual-Sync & Global Edge CDN ]
  └── Typography & Chinese Fonts (Local SSD for TCPDF + S3/CDN edge with 30d immutable caching)

[ Tier 5: Abandoned Drafts Garbage Collection ]
  └── Unpurchased Guest Drafts (> 7 days, shielded by Active Cart Protection)

[ Tier 6: Customer Saved Designs Lifecycle & Auto-Purge ]
  └── Customer Saved Designs (6, 12, 18, 24 mo retention, warning banners, notification emails)
```

### Detailed Tier Rules:

#### Tier 1: Admin Templates (High-Performance Local SSD)
- **Scope**: All template folders registered in `{$wpdb->prefix}nbdesigner_templates`.
- **Policy**: Kept on local NVMe SSD storage to guarantee instant canvas loading when shoppers browse or customize products. Protected by a whitelist—never moved to cold storage or deleted locally.

#### Tier 2: Active / Production Designs (Hot Phase)
- **Scope**: Newly created customer designs and order items (`designs/{nbd_item_key}`).
- **Policy**: Kept locally on SSD while the order is placed, printed, and fulfilled (configurable hot window, e.g., 0 to 14 days). NBDesigner native PDF and image generators read/write locally without network latency.

#### Tier 3: Inactive / Cold Orders (Move to S3 & Delete Local Copy)
- **Scope**: Order designs past the retention threshold (e.g., order status `wc-completed` for > 14 days, or file inactivity > 30 days).
- **Move & Delete Process**:
  1. **Upload**: Files are synced to S3 in the background via **WooCommerce Action Scheduler** (non-blocking).
  2. **Integrity Verification**: The plugin confirms every file exists on S3 with matching size and ETag / MD5 checksum.
  3. **Reclaim Disk Space**: Once verified, the local folder (`designs/{nbd_item_key}/`) is safely deleted from the web server.
- **On-Demand Hydration (Just-In-Time Restore)**:
  - If an admin clicks "Download Design" for an archived order, or a customer accesses past design history, the plugin intercepts the request, downloads the design folder from S3 back to local disk, and allows NBDesigner to proceed seamlessly.
- **Optional Smart Purge Mode**:
  - Admins can choose to delete only the heavy print assets (`customer-pdfs/`, `customer-png/`, `customer-jpg/`, saving ~98% of disk space) while keeping small metadata JSONs (`design.json`, `config.json`, ~10KB) local for instant order list thumbnails.

#### Tier 4: Font Dual-Sync & CDN Caching (Local & Cloud In-Sync)
- **Scope**: All font files in `wp-content/uploads/nbdesigner/fonts/` (custom fonts, subsets, and Google Fonts).
- **Dual-Sync Model**:
  - Fonts are maintained **both on the local web server and on S3/CDN simultaneously**.
  - **Local Copy Retention**: Critical because server-side print generation engines (TCPDF / Imagick / Ghostscript) require direct local filesystem access (`K_PATH_FONTS`, `NBDESIGNER_FONT_DIR`) when rendering vector PDFs and print-ready files.
  - **Cloud Copy Delivery**: Canvas frontend loads font files directly from S3/CDN.
  - **Caching Headers**: `Cache-Control: public, max-age=2592000, immutable` (30 days) to prevent repeated multi-megabyte downloads.
  - **CORS Headers**: `Access-Control-Allow-Origin: *` to prevent browser font blocking and Fabric.js canvas tainting.
- **Admin Purge & Resync Tool**:
  - In the React Admin UI, include a dedicated action button: **"Purge & Resync Fonts from Local"**.
  - When triggered:
    1. Optionally purges/cleans the remote S3 `fonts/` prefix and triggers a CDN cache invalidation/purge.
    2. Re-scans the local `wp-content/uploads/nbdesigner/fonts/` directory.
    3. Uploads all font assets (`.ttf`, `.otf`, `.woff`, `.eot`, `.svg`, `fonts.json`) to S3 with correct MIME types and cache headers.
    4. Displays real-time sync progress (file count, progress bar, completion toast) in the React dashboard.

#### Tier 5: Abandoned Drafts Cleanup
- **Scope**: Folders in `designs/` older than 7 days (configurable: 1, 7, 30, 90, 180 days) that do not belong to `nbdesigner_templates`, `nbdesigner_mydesigns`, or any completed/processing WooCommerce order.
- **Active Cart Protection Shield**: Immune to purge if the design key currently resides inside an active guest or logged-in WooCommerce cart session.
- **Policy**: Automatically purged to eliminate phantom storage bloat.

#### Tier 6: Customer Saved Designs Lifecycle & Auto-Purge Policy
- **Scope**: Customer saved designs stored in database table `{$wpdb->prefix}nbdesigner_mydesigns` and physical folders under `designs/{folder}/`.
- **Zero Core Edits Architecture**: 100% independent addon implementation hooking into standard WordPress/WooCommerce filters without modifying or hacking NBDesigner core code.
- **Configurable Retention Window ($X$ Months)**:
  - Presets: **6 Months** (180 days), **12 Months** (365 days, Default), **18 Months** (545 days), **24 Months** (730 days). Also accepts custom numeric day values up to 3650 days.
  - Automatic scheduled daily sweep deletes designs exceeding the retention window.
- **Customer Warning Window ($Y$ Days)**:
  - Configurable threshold (default: 7 days, selectable: 1, 3, 7, 14, 30 days).
  - When design age enters $X - Y$ days, customer is actively notified:
    1. **Frontend Expiry Notice & Countdown Badges**: Hooked via `nb_before_show_design` to inject high-visibility banner warnings and per-item badges directly into the customer's "My Saved Designs" account page.
    2. **Automated Warning Email**: Sends an advance alert email informing the customer of pending design expiration with a direct CTA link to view or purchase their design. Anti-duplicate tracking prevents repeat emails.
- **Automated & Manual Purge Operations**:
  - **Comprehensive Deletion**: Upon reaching retention age $X$:
    1. Deletes the physical design folder from local NVMe SSD (`wp-content/uploads/nbdesigner/designs/{folder}`).
    2. Deletes remote files from S3 bucket under `designs/{folder}/`.
    3. Removes the design record from `{$wpdb->prefix}nbdesigner_mydesigns`.
    4. Records an audit entry in the Activity Log (`SAVED_DESIGNS_PURGE`).
    5. Dispatches a friendly purge notification email informing the customer that their saved design reached retention expiration and was removed, with a button inviting them to create a new project.
  - **Dedicated Manual Purge Action**: Admin can trigger immediate on-demand sweep via **"Purge Expired Saved Designs Now"** in the Storage Stats & Sweeps dashboard tab or REST API (`POST /saved-designs/purge-now`).
- **Loco Translate Full Compatibility**:
  - All frontend notices, countdown badges, and email templates use standard WordPress gettext functions (`__()`, `_e()`, `_n()`, `esc_html__()`) mapped to domain `nbd-s3-offload` and indexed in `languages/nbd-s3-offload.pot`.
  - Can be translated, customized, or rebranded directly inside Loco Translate without developer intervention.

---

## 5. Technical Architecture & Non-Blocking Workflow

1. **Non-Blocking Background Queue**:
   - Large print PDFs (20MB–100MB+) are never uploaded during the checkout request.
   - Sync tasks are queued into **WooCommerce Action Scheduler** and processed asynchronously in the background.
2. **CORS Compliance**:
   - S3 buckets and CDN distributions must provide `Access-Control-Allow-Origin: *` and `Access-Control-Allow-Methods: GET, HEAD` to ensure Fabric.js canvas export (`toDataURL`) and cross-domain webfont rendering succeed.
3. **URL Interception & Pre-Definition**:
   - Pre-define or filter NBDesigner asset URLs (`NBDESIGNER_FONT_URL`, `NBDESIGNER_CUSTOMER_URL`) to redirect frontend asset delivery directly to the CDN domain.
4. **React + Vite Frontend Admin UI**:
   - Admin settings page is built in React + Vite, bundled into static JavaScript/CSS assets, and loaded via standard `wp_enqueue_script()` / `wp_enqueue_style()`.
   - Communicates with WordPress via nonce-protected WP REST API endpoints.

---

## 6. NBDesigner Asset Storage & Directory Specifications

In the `web-to-print-online-designer` plugin, all dynamic assets are stored under:
- **Root Base Path**: `wp-content/uploads/nbdesigner/`
- **PHP Constant**: `NBDESIGNER_DATA_DIR` (`$basedir . '/nbdesigner'`)

### A. Design Files (`NBDESIGNER_CUSTOMER_DIR`)
- **Directory Path**: `wp-content/uploads/nbdesigner/designs/{folder_or_nbd_item_key}/`
- **Files Inside Each Design Folder**:
  - `design.json`: Vector objects, canvas text, layers, Fabric.js elements.
  - `config.json`: Canvas dimensions, bleed lines, sides, area boundaries.
  - `product.json`: Real dimensions, offsets, scale factors.
  - `option.json`: DPI resolution, measurement units (`cm`, `mm`, `in`, `px`), orientation.
  - `used_font.json`: Manifest of fonts used in this specific design.
  - `customer-pdfs/`: Print-ready PDF files.
  - `customer-png/`: Rendered print-ready PNG files.
  - `customer-jpg/`: Rendered print-ready JPG files.
  - `customer-pdfs-watermark/`: Watermarked preview PDFs.
  - `preview/`: Side preview raster images (e.g. `0.png`, `1.png`).
  - `preview2/thumb/`: High-resolution thumbnails.
  - `svg/`, `svgpath/`, `part/`: Extracted SVG paths and layers.
- **Database References**:
  - Product Templates: `{$wpdb->prefix}nbdesigner_templates` (column `folder`)
  - Customer Saved Designs: `{$wpdb->prefix}nbdesigner_mydesigns`, `{$wpdb->prefix}nbdesigner_user_designs` (column `folder`)
  - Order Items: `woocommerce_order_itemmeta` (`meta_key`: `_nbd` or `_nbd_item_key`)

### B. Images
| Category | Storage Path | PHP Constant | Description |
| :--- | :--- | :--- | :--- |
| **Canvas Uploads** | `wp-content/uploads/nbdesigner/temp/{Y}/{m}/` | `NBDESIGNER_TEMP_DIR` | User photos, logos, artwork added inside canvas editor. |
| **Direct Product Uploads** | `wp-content/uploads/nbdesigner/uploads/{nbu_item_key}/` | `NBDESIGNER_UPLOAD_DIR` | Artwork files uploaded directly on product page. |
| **Cliparts & Graphics** | `wp-content/uploads/nbdesigner/cliparts/` | `NBDESIGNER_ART_DIR` | Vector art and stock clipart. Catalogs in `arts.json` & `art_cat.json`. |
| **Previews & Thumbnails** | `.../designs/{nbd_item_key}/preview/`<br>`.../designs/{nbd_item_key}/preview2/thumb/` | Inside design folder | Canvas side previews and small thumbnails. |
| **Product Mockups & Overlays**| `wp-content/uploads/{Y}/{m}/` | WP Media Library | Base product mockup photos and overlay masks (`_designer_setting`). |

### C. Fonts (`NBDESIGNER_FONT_DIR`)
- **Directory Path**: `wp-content/uploads/nbdesigner/fonts/`
- **Contents**: Custom uploaded fonts (`.ttf`, `.otf`, `.woff`) and downloaded Google Web Fonts.
- **Catalogs**: `fonts.json` (definitions), `font_cat.json` (categories), `googlefonts.json` (cached Google Font list).
- **TCPDF Fonts**: Located in `K_PATH_FONTS`.

### D. Complete Directory Tree
```
wp-content/uploads/nbdesigner/
├── designs/                 # [NBDESIGNER_CUSTOMER_DIR] Templates & customer order designs
│   └── {nbd_item_key}/
│       ├── design.json      # Vector canvas objects
│       ├── config.json      # Dimensions & bleed settings
│       ├── product.json     # Product dimensions & scales
│       ├── option.json      # DPI & units
│       ├── used_font.json   # Fonts used in design
│       ├── preview/         # Canvas raster preview images
│       ├── customer-pdfs/   # Final print-ready PDFs
│       ├── customer-png/    # Final print PNGs
│       └── customer-jpg/    # Final print JPGs
├── uploads/                 # [NBDESIGNER_UPLOAD_DIR] Raw files uploaded by customers on product page
│   └── {nbu_item_key}/
├── temp/                    # [NBDESIGNER_TEMP_DIR] Customer images uploaded while in design studio
├── cliparts/                # [NBDESIGNER_ART_DIR] Clipart images/SVGs
├── fonts/                   # [NBDESIGNER_FONT_DIR] .ttf, .otf, and Google fonts
├── download/                # [NBDESIGNER_DOWNLOAD_DIR] Temporary zip files for admin downloads
├── fonts.json / arts.json   # Metadata catalogs for fonts and cliparts
└── data/                    # [NBDESIGNER_DATA_CONFIG_DIR] Localization & settings data
```
