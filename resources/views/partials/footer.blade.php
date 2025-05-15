<footer class="footer bg-dark text-white pt-5 pb-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h4>About Us</h4>
                <p>
                    {{ Str::limit($companyProfile->about ?? 'CV Usaha Prima Lestari is a leading provider of construction and engineering services in Indonesia, committed to delivering excellence in every project.', 200) }}
                </p>
                <div class="social-icons mt-3">
                    @if(isset($companyProfile->facebook) && $companyProfile->facebook)
                        <a href="{{ $companyProfile->facebook }}" class="me-2 text-white" target="_blank"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if(isset($companyProfile->twitter) && $companyProfile->twitter)
                        <a href="{{ $companyProfile->twitter }}" class="me-2 text-white" target="_blank"><i class="bi bi-twitter"></i></a>
                    @endif
                    @if(isset($companyProfile->instagram) && $companyProfile->instagram)
                        <a href="{{ $companyProfile->instagram }}" class="me-2 text-white" target="_blank"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if(isset($companyProfile->linkedin) && $companyProfile->linkedin)
                        <a href="{{ $companyProfile->linkedin }}" class="me-2 text-white" target="_blank"><i class="bi bi-linkedin"></i></a>
                    @endif
                    @if(isset($companyProfile->youtube) && $companyProfile->youtube)
                        <a href="{{ $companyProfile->youtube }}" class="me-2 text-white" target="_blank"><i class="bi bi-youtube"></i></a>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <h4>Contact Info</h4>
                <ul class="list-unstyled contact-info">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        {{ $companyProfile->address ?? 'Jakarta, Indonesia' }}
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone-fill me-2"></i>
                        {{ $companyProfile->phone ?? '+62 123 456 789' }}
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope-fill me-2"></i>
                        {{ $companyProfile->email ?? 'info@cvupl.com' }}
                    </li>
                    <li>
                        <i class="bi bi-clock-fill me-2"></i>
                        Monday - Friday: 8:00 AM - 5:00 PM
                    </li>
                </ul>
            </div>
            <div class="col-lg-4 mb-4">
                <h4>Quick Links</h4>
                <ul class="list-unstyled quick-links">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white text-decoration-none"><i class="bi bi-chevron-right me-2"></i>Home</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-white text-decoration-none"><i class="bi bi-chevron-right me-2"></i>About Us</a></li>
                    <li class="mb-2"><a href="{{ route('services.index') }}" class="text-white text-decoration-none"><i class="bi bi-chevron-right me-2"></i>Services</a></li>
                    <li class="mb-2"><a href="{{ route('portfolio.index') }}" class="text-white text-decoration-none"><i class="bi bi-chevron-right me-2"></i>Portfolio</a></li>
                    <li class="mb-2"><a href="{{ route('blog.index') }}" class="text-white text-decoration-none"><i class="bi bi-chevron-right me-2"></i>Blog</a></li>
                    <li><a href="{{ route('contact.index') }}" class="text-white text-decoration-none"><i class="bi bi-chevron-right me-2"></i>Contact</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom border-top mt-4 pt-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'CV Usaha Prima Lestari') }}. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Designed & Developed by <a href="#" class="text-white">Your Company</a></p>
                </div>
            </div>
        </div>
    </div>
</footer>