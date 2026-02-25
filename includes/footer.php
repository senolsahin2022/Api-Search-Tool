    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3><i class="fa-brands fa-x-twitter"></i> TwitExplorer</h3>
                    <p><?= e(__('footer_desc')) ?></p>
                </div>
                <div class="footer-section">
                    <h4><?= e(__('quick_access')) ?></h4>
                    <ul>
                        <li><a href="/"><?= e(__('home')) ?></a></li>
                        <li><a href="/widget"><?= e(__('widget_title')) ?></a></li>
                        <li><a href="/hashtag/trending">Popüler Hashtag'ler</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4><?= e(__('popular_searches')) ?></h4>
                    <ul>
                        <li><a href="/search?q=crypto">Kripto</a></li>
                        <li><a href="/search?q=technology">Teknoloji</a></li>
                        <li><a href="/search?q=news">Haberler</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> TwitExplorer. <?= e(__('all_rights')) ?></p>
            </div>
        </div>
    </footer>

    <!-- Cookie Consent Popup -->
    <div id="cookie-consent" class="cookie-consent">
        <div class="container">
            <div class="cookie-content">
                <p><?= e(__('cookie_text')) ?> <a href="/privacy"><?= e(__('cookie_link')) ?></a></p>
                <div class="cookie-buttons">
                    <button id="cookie-accept" class="btn btn-primary btn-sm"><?= e(__('cookie_accept')) ?></button>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/app.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('cookieConsent')) {
            document.getElementById('cookie-consent').classList.add('show');
        }
        document.getElementById('cookie-accept').addEventListener('click', function() {
            localStorage.setItem('cookieConsent', 'true');
            document.getElementById('cookie-consent').classList.remove('show');
        });
    });
    </script>
</body>
</html>
