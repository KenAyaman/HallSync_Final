import { chromium } from 'playwright';

const BASE = 'http://localhost:8765';
const EMAIL = 'test@example.com';
const PASS  = 'password123';

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();
    const log = [];
    page.on('console', m => log.push(`[console] ${m.text()}`));

    // --- Login ---
    await page.goto(`${BASE}/login`);
    await page.fill('input[name=email]', EMAIL);
    await page.fill('input[name=password]', PASS);
    await page.click('button[type=submit]');
    await page.waitForURL(/\/(dashboard|tickets)/, { timeout: 8000 });
    console.log('✅ Logged in, landed on:', page.url());

    // --- Navigate to ticket create ---
    await page.goto(`${BASE}/tickets/create`);
    await page.waitForLoadState('domcontentloaded');
    console.log('✅ On ticket create page');

    // --- Fill the form ---
    await page.fill('input[name=title]', 'Verify toast – sink leak in Room 101');
    await page.selectOption('select[name=category]', 'plumbing');
    await page.click('[data-priority="medium"]');
    await page.fill('textarea[name=description]', 'Hot water stopped working three days ago. Needs attention soon.');

    // --- Submit ---
    const submitBtn = page.locator('button[type=submit]');
    await submitBtn.click();
    console.log('✅ Form submitted');

    // --- Wait for redirect to /tickets ---
    await page.waitForURL(/\/tickets$/, { timeout: 10000 });
    console.log('✅ Redirected to tickets index:', page.url());

    // --- Check for toast (give it up to 3s to animate in) ---
    const toastLocator = page.locator('.app-toast.app-toast-success');
    let toastVisible = false;
    let toastTitle   = '';
    let toastDetail  = '';

    try {
        await toastLocator.waitFor({ state: 'visible', timeout: 3000 });
        toastVisible = true;
        toastTitle  = await toastLocator.locator('strong').textContent();
        toastDetail = await toastLocator.locator('span').textContent().catch(() => '');
        console.log('✅ Toast visible!');
        console.log('   Title  :', toastTitle);
        console.log('   Detail :', toastDetail);
    } catch {
        console.log('❌ Toast NOT visible within 3 seconds');
    }

    // --- Screenshot for evidence ---
    await page.screenshot({ path: 'verify_toast_result.png', fullPage: false });
    console.log('📸 Screenshot: verify_toast_result.png');

    // --- Check if toast auto-dismisses (stays through 5s window) ---
    if (toastVisible) {
        await page.waitForTimeout(2500);
        const stillVisible = await toastLocator.isVisible().catch(() => false);
        console.log(stillVisible
            ? '✅ Toast still visible at ~3s (auto-dismiss at 5.6s)'
            : '⚠️  Toast already gone at ~3s (dismissed early?)');
    }

    // --- Console log dump ---
    const wsLogs = log.filter(l => l.includes('websocket') || l.includes('Dashboard') || l.includes('grace') || l.includes('Ignoring'));
    if (wsLogs.length) {
        console.log('\nWebSocket console logs:');
        wsLogs.forEach(l => console.log(' ', l));
    }

    console.log('\n--- RESULT ---');
    console.log(toastVisible ? 'PASS' : 'FAIL');
    if (!toastVisible) process.exitCode = 1;

    await browser.close();
})();
