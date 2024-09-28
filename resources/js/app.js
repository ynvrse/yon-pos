import './bootstrap';

    import collapse from "@alpinejs/collapse";
    import anchor from "@alpinejs/anchor";
    // On page load or when changing themes, best to add inline in `head` to avoid FOUC
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark')
    }

     
    document.addEventListener(
        "alpine:init",
        () => {
            const modules = import.meta.glob("./plugins/**/*.js", { eager: true });
     
            for (const path in modules) {
                window.Alpine.plugin(modules[path].default);
            }
            window.Alpine.plugin(collapse);
            window.Alpine.plugin(anchor);
        },
        { once: true },
    );

