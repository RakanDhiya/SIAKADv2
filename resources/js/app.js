
import './bootstrap';

/**
 * Menginisialisasi interaksi navigasi sidebar.
 *
 * Mendukung pembukaan dan penutupan sidebar melalui tombol menu,
 * overlay, tombol Escape, serta perubahan ukuran layar.
 *
 * Mengelola fokus keyboard dan mencegah interaksi dengan sidebar
 * ketika sidebar dalam keadaan tertutup.
 */
function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const toggleButton = document.getElementById('sidebar-toggle');

    if (!sidebar || !overlay || !toggleButton) {
        return;
    }

    const desktopBreakpoint = window.matchMedia('(min-width: 1024px)');

    let overlayHideTimer = null;

    /**
     * Membatalkan timer penutupan overlay sebelumnya.
     */
    function clearOverlayHideTimer() {
        if (overlayHideTimer !== null) {
            window.clearTimeout(overlayHideTimer);
            overlayHideTimer = null;
        }
    }

    /**
     * Membuka sidebar pada perangkat mobile.
     */
    function openSidebar() {
        if (desktopBreakpoint.matches) {
            return;
        }

        clearOverlayHideTimer();

        overlay.classList.remove('hidden');
        sidebar.inert = false;
        sidebar.classList.remove('-translate-x-full');

        window.requestAnimationFrame(() => {
            overlay.classList.remove('opacity-0');
        });

        toggleButton.setAttribute('aria-expanded', 'true');
        document.body.classList.add('overflow-hidden');
    }

    /**
     * Menutup sidebar dan mengembalikan kondisi halaman.
     *
     * @param {Object} options
     * @param {boolean} options.restoreFocus
     */
    function closeSidebar({ restoreFocus = false } = {}) {
        if (desktopBreakpoint.matches) {
            return;
        }

        clearOverlayHideTimer();

        sidebar.classList.add('-translate-x-full');
        sidebar.inert = true;

        overlay.classList.add('opacity-0');

        toggleButton.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');

        if (restoreFocus) {
            toggleButton.focus();
        }

        overlayHideTimer = window.setTimeout(() => {
            if (
                !desktopBreakpoint.matches &&
                toggleButton.getAttribute('aria-expanded') === 'false'
            ) {
                overlay.classList.add('hidden');
            }

            overlayHideTimer = null;
        }, 300);
    }

    /**
     * Menyesuaikan kondisi awal sidebar berdasarkan ukuran layar.
     */
    function initializeSidebarState() {
        const isDesktop = desktopBreakpoint.matches;

        sidebar.inert = !isDesktop;
        toggleButton.setAttribute('aria-expanded', 'false');

        if (isDesktop) {
            sidebar.classList.remove('-translate-x-full');
        } else {
            sidebar.classList.add('-translate-x-full');
        }

        overlay.classList.add('hidden', 'opacity-0');
        document.body.classList.remove('overflow-hidden');
    }

    /**
     * Mengatur kondisi sidebar ketika tombol menu ditekan.
     */
    toggleButton.addEventListener('click', () => {
        const isOpen =
            toggleButton.getAttribute('aria-expanded') === 'true';

        if (isOpen) {
            closeSidebar({ restoreFocus: true });
        } else {
            openSidebar();
        }
    });

    /**
     * Menutup sidebar ketika overlay ditekan.
     */
    overlay.addEventListener('click', () => {
        closeSidebar({ restoreFocus: true });
    });

    /**
     * Menutup sidebar menggunakan tombol Escape.
     */
    document.addEventListener('keydown', (event) => {
        const isOpen =
            toggleButton.getAttribute('aria-expanded') === 'true';

        if (event.key === 'Escape' && isOpen) {
            closeSidebar({ restoreFocus: true });
        }
    });

    /**
     * Menyesuaikan kondisi sidebar ketika ukuran layar berubah.
     */
    desktopBreakpoint.addEventListener('change', (event) => {
        clearOverlayHideTimer();

        overlay.classList.add('hidden', 'opacity-0');

        toggleButton.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');

        if (event.matches) {
            sidebar.inert = false;
            sidebar.classList.remove('-translate-x-full');
        } else {
            if (sidebar.contains(document.activeElement)) {
                toggleButton.focus();
            }

            sidebar.inert = true;
            sidebar.classList.add('-translate-x-full');
        }
    });

    initializeSidebarState();
}

document.addEventListener('DOMContentLoaded', initializeSidebar);