
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes pulse-scale {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-float { animation: float 3s ease-in-out infinite; }
    .animate-spin-slow { animation: spin-slow 3s linear infinite; }
    .animate-pulse-scale { animation: pulse-scale 2s ease-in-out infinite; }
    .animate-slide-up { animation: slide-up 0.5s ease-out forwards; }
</style>