<?php
$currentPage = 'downloader';
$pageTitle = 'X (Twitter) Video Downloader - TwitExplorer';
$pageDescription = 'Download X (Twitter) videos and GIFs for free in high quality. Fast, easy, and secure video downloader.';
$canonicalUrl = '/downloader';

require __DIR__ . '/../includes/header.php';
?>

<section class="hero">
    <h1>X Video Downloader</h1>
    <p>Download your favorite X (Twitter) videos directly to your device.</p>
    
    <div class="search-bar" style="max-width: 600px; margin: 30px auto;">
        <input type="text" id="video-url" placeholder="Paste X video URL here..." style="width: 100%;">
        <button type="button" onclick="downloadVideo()">Download</button>
    </div>
    
    <div id="download-result" style="margin-top: 30px; display: none;">
        <!-- Results will be injected here -->
        <div class="tweet-card">
            <p>Processing video link...</p>
        </div>
    </div>
</section>

<section style="margin-top: 50px;">
    <h2 class="section-title"><i class="fa-solid fa-circle-question"></i> Frequently Asked Questions</h2>
    <div class="faq-container">
        <div class="trend-card" style="margin-bottom: 10px; cursor: default;">
            <div class="trend-name">How to download X videos?</div>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Copy the tweet URL, paste it into the search box above, and click Download.</p>
        </div>
        <div class="trend-card" style="margin-bottom: 10px; cursor: default;">
            <div class="trend-name">Is it free to use?</div>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Yes, our X video downloader is completely free and requires no registration.</p>
        </div>
        <div class="trend-card" style="margin-bottom: 10px; cursor: default;">
            <div class="trend-name">Can I download private videos?</div>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">No, for privacy reasons, we can only download videos from public accounts.</p>
        </div>
    </div>
</section>

<script>
function downloadVideo() {
    const url = document.getElementById('video-url').value;
    if (!url) return alert('Please enter a URL');
    
    const resultDiv = document.getElementById('download-result');
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="tweet-card"><p><i class="fa-solid fa-spinner fa-spin"></i> Analyzing video URL...</p></div>';
    
    // In a real scenario, this would call a backend API that handles the extraction
    // For now, we simulate a response
    setTimeout(() => {
        resultDiv.innerHTML = `
            <div class="tweet-card" style="text-align: left;">
                <div class="trend-name" style="margin-bottom: 15px;">Video Found!</div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="#" class="btn btn-primary" style="background: var(--success);">Download HD (720p)</a>
                    <a href="#" class="btn btn-primary">Download SD (360p)</a>
                </div>
                <p style="margin-top: 15px; font-size: 0.8rem; color: var(--text-secondary);">Note: This is a simulation. To implement real downloading, a specialized extraction API would be required.</p>
            </div>
        `;
    }, 1500);
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "How to download X videos?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Copy the tweet URL, paste it into the search box above, and click Download."
    }
  }, {
    "@type": "Question",
    "name": "Is it free to use?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Yes, our X video downloader is completely free and requires no registration."
    }
  }]
}
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
