<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,500;6..72,600&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
{{-- Tailwind via CDN agar MVP langsung jalan tanpa build. Untuk produksi, pindahkan ke Vite/Tailwind build. --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['"Public Sans"', 'system-ui', 'sans-serif'],
                    display: ['Newsreader', 'Georgia', 'serif'],
                },
                colors: {
                    paper: '#F6F7F4',
                    board: {
                        50: '#F2F6F4', 100: '#E1EBE6', 200: '#BFD3CA', 300: '#97B6A8', 400: '#6B9484',
                        500: '#4A7766', 600: '#2F6150', 700: '#244C3E', 800: '#1B3A30', 900: '#122A22',
                    },
                    pencil: { 300: '#F7DB94', 400: '#F2C14E', 500: '#E0A82A' },
                },
            },
        },
    }
</script>
