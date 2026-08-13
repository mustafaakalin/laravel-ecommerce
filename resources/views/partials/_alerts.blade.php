
<div class="container mx-auto px-4">
    @if (session('success'))
        <div class="alert alert-success my-4 animate-fade-in-down">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error my-4 animate-fade-in-down">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning my-4 animate-fade-in-down">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('warning') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info my-4 animate-fade-in-down">
            <i class="fas fa-info-circle mr-2"></i>
            {{ session('info') }}
        </div>
    @endif
</div>

<style>
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }
</style>

<script>
    // Auto-hide alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        });
    });
</script>