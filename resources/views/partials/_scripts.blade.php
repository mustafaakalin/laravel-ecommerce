
<script>
    // Preloader
    window.addEventListener('load', () => {
        const preloader = document.getElementById('preloader');
        setTimeout(() => {
            preloader.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = 'auto';
            setTimeout(() => preloader.remove(), 300);
        }, 800);
    });

    // Alert Auto-hide
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.display = 'none';
        });
    }, 3000);

    // Theme Management
    document.addEventListener('DOMContentLoaded', function () {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.querySelectorAll('.theme-controller').forEach(radio => {
                radio.checked = radio.value === savedTheme;
            });
        }

        document.querySelectorAll('.theme-controller').forEach(radio => {
            radio.addEventListener('change', function () {
                document.documentElement.setAttribute('data-theme', this.value);
                localStorage.setItem('theme', this.value);
            });
        });
    });
</script>