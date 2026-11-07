/**
 * NBDesigner S3 Offload - Admin Fallback Script
 *
 * Rendered when Vite production bundle (admin/dist/) is not yet compiled.
 */
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('nbd-s3-offload-root');
    if (!root) return;

    // Check if React already mounted
    if (root.children.length > 0) return;

    root.innerHTML = `
        <div style="background: #ffffff; border: 1px solid #ccd0d4; padding: 24px; border-radius: 8px; max-width: 800px; margin-top: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            <h2 style="margin-top: 0; color: #1d2327;">⚡ NBDesigner S3 Offload & Storage Tiering</h2>
            <div style="background: #fff8e5; border-left: 4px solid #dba617; padding: 12px 16px; margin: 16px 0;">
                <strong>Notice:</strong> The React admin dashboard bundle is being built.
                If you are running in a fresh development checkout, run:
                <pre style="background: #f0f0f1; padding: 8px; border-radius: 4px; margin: 8px 0;">cd nbdesigner-S3-offload/admin
npm install
npm run build</pre>
            </div>
            <p>Alternatively, all core backend functionality, WP-Cron schedules, S3 SigV4 sync, and REST API endpoints (<code>/wp-json/nbd-s3-offload/v1/</code>) are fully active and operational.</p>
        </div>
    `;
});

