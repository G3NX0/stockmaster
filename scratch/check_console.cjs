const puppeteer = require('puppeteer');

(async () => {
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    page.on('console', msg => console.log('BROWSER CONSOLE:', msg.text()));
    page.on('pageerror', err => console.error('BROWSER ERROR:', err.toString()));
    
    try {
        await page.goto('http://localhost:8000/', { waitUntil: 'networkidle0' });
    } catch (e) {
        console.error("Failed to load page:", e);
    }
    await browser.close();
})();
