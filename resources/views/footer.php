    </main>
    <footer class="mt-10" style="background:linear-gradient(120deg,#1F4E79,#285F96);color:#FFFFFF;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2">
                    <div class="text-lg font-semibold">Campus Eye Maintenance Reporting System</div>
                    <p class="text-sm max-w-xl" style="color:rgba(255,255,255,0.85);">Designed to keep campus facilities safe, with clear reporting, tracking, and resolution for everyone.</p>
                    <div class="text-sm" style="color:rgba(255,255,255,0.8);">&copy; 2025 TAR UMT. All rights reserved.</div>
                </div>
                <div class="flex flex-wrap gap-3 text-sm">
                    <a href="/help" class="px-4 py-2 rounded-full border" style="border-color:rgba(255,255,255,0.35);color:white;">Help / FAQ</a>
                    <a href="/support" class="px-4 py-2 rounded-full border" style="border-color:rgba(255,255,255,0.35);color:white;">Contact Support</a>
                    <a href="/privacy" class="px-4 py-2 rounded-full border" style="border-color:rgba(255,255,255,0.35);color:white;">Privacy</a>
                </div>
            </div>
        </div>
    </footer>
    <script src="/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const toggle = document.getElementById('navToggle');
            const mobileNav = document.getElementById('mobileNav');
            if (toggle && mobileNav) {
                toggle.addEventListener('click', () => {
                    const isHidden = mobileNav.hasAttribute('hidden');
                    if (isHidden) {
                        mobileNav.removeAttribute('hidden');
                    } else {
                        mobileNav.setAttribute('hidden', 'hidden');
                    }
                });
            }
        })();
    </script>
    <?php if (!empty($pageScripts) && is_array($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
