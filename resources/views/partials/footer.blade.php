<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>{{ __('footer.brand_name') }}</h3>
            <p>{{ __('footer.brand_description') }}</p>
        </div>
        <div class="footer-section">
            <h3>{{ __('footer.explore_title') }}</h3>
            @foreach(__('footer.explore_links') as $link)
                <a href="#">{{ $link }}</a>
            @endforeach
        </div>
        <div class="footer-section">
            <h3>{{ __('footer.support_title') }}</h3>
            @foreach(__('footer.support_links') as $link)
                <a href="#">{{ $link }}</a>
            @endforeach
        </div>
        <div class="footer-section">
            <h3>{{ __('footer.connect_title') }}</h3>
            @foreach(__('footer.connect_links') as $link)
                <a href="#">{{ $link }}</a>
            @endforeach
        </div>
    </div>
    <div class="footer-bottom">
        <p>{{ __('footer.copyright') }}</p>
    </div>
</footer>

<div class="chatbot">
    <img src="https://cdn-icons-png.flaticon.com/512/8943/8943377.png" alt="{{ __('home.chatbot_alt') }}">
</div>
