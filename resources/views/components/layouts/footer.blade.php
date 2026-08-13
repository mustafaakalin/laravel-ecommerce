<!-- Footer -->
<footer class="footer p-10 bg-base-200 text-base-content rounded-md">
    <div>
        <span class="footer-title">Kategoriler</span>
        @foreach($categories->take(5) as $category)
        <a href="{{ route('categories.show', $category->slug) }}" class="link link-hover">{{ $category->name
            }}</a>
        @endforeach
    </div>
    <div>
        <span class="footer-title">Kurumsal</span>
        <a href="#" class="link link-hover">Hakkımızda</a>
        <a href="#" class="link link-hover">İletişim</a>
        <a href="#" class="link link-hover">Sıkça Sorulan Sorular</a>
    </div>
    <div>
        <span class="footer-title">Yasal</span>
        <a href="#" class="link link-hover">Gizlilik Politikası</a>
        <a href="#" class="link link-hover">Kullanım Şartları</a>
        <a href="#" class="link link-hover">KVKK</a>
    </div>
</footer>