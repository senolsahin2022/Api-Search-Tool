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
                        <li><a href="/search?q=gündem">Gündem</a></li>
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
    <script src="/assets/js/app.js"></script>
</body>
</html>
